<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentairePublicationRequest;
use App\Http\Requests\StorePublicationQuartierRequest;
use App\Models\PublicationQuartier;
use App\Models\Quartier;
use App\Services\GroqAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicationQuartierController extends Controller
{
    public function index(Request $request): View
    {
        $quartier = $this->quartier($request);
        $query = PublicationQuartier::with(['user'])
            ->withCount(['commentaires', 'confirmations'])
            ->where('quartier_id', $quartier->id);

        if (in_array($request->string('type')->toString(), ['probleme', 'information', 'entraide', 'alerte'], true)) {
            $query->where('type', $request->string('type')->toString());
        }

        if (in_array($request->string('statut')->toString(), ['signale', 'en_cours', 'resolu'], true)) {
            $query->where('statut', $request->string('statut')->toString());
        }

        match ($request->string('tri', 'recent')->toString()) {
            'commentes' => $query->orderByDesc('commentaires_count')->latest(),
            'confirmes' => $query->orderByDesc('confirmations_count')->latest(),
            default => $query->latest(),
        };

        return view('quartier.discussions.index', [
            'quartier' => $quartier,
            'publications' => $query->get(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('quartier.discussions.create', ['quartier' => $this->quartier($request)]);
    }

    public function store(StorePublicationQuartierRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $publication = PublicationQuartier::create($data + [
            'user_id' => $request->user()->id,
            'quartier_id' => $this->quartier($request)->id,
            'statut' => 'signale',
        ]);

        return redirect()->route('discussions-quartier.show', $publication)->with('success', 'Votre discussion a été publiée.');
    }

    public function show(Request $request, PublicationQuartier $publication): View
    {
        $this->ensureSameQuartier($request, $publication);
        $publication->load(['user', 'quartier', 'commentaires.user'])->loadCount(['commentaires', 'confirmations']);

        return view('quartier.discussions.show', ['publication' => $publication]);
    }

    public function comment(StoreCommentairePublicationRequest $request, PublicationQuartier $publication): RedirectResponse
    {
        $this->ensureSameQuartier($request, $publication);
        $data = $request->validated();
        $publication->commentaires()->create($data + ['user_id' => $request->user()->id]);

        return back()->with('success', 'Votre commentaire a été ajouté.');
    }

    public function toggleConfirmation(Request $request, PublicationQuartier $publication): RedirectResponse
    {
        $this->ensureSameQuartier($request, $publication);
        $userId = $request->user()->id;

        if ($publication->confirmations()->whereKey($userId)->exists()) {
            $publication->confirmations()->detach($userId);

            return back()->with('success', 'Votre confirmation a été retirée.');
        }

        $publication->confirmations()->syncWithoutDetaching([$userId]);

        return back()->with('success', 'Votre confirmation a été ajoutée.');
    }

    public function updateStatus(Request $request, PublicationQuartier $publication): RedirectResponse
    {
        $this->ensureSameQuartier($request, $publication);
        abort_unless($publication->user_id === $request->user()->id || $request->user()->role === 'admin', 403);
        $data = $request->validate(['statut' => ['required', 'in:signale,en_cours,resolu']]);
        $publication->update([
            'statut' => $data['statut'],
            'resolved_at' => $data['statut'] === 'resolu' ? now() : null,
        ]);

        return back()->with('success', 'Le statut de la discussion a été mis à jour.');
    }

    public function summarize(Request $request, PublicationQuartier $publication, GroqAiService $groq): JsonResponse
    {
        $this->ensureSameQuartier($request, $publication);
        $publication->load('commentaires');

        if ($publication->commentaires->count() < 2) {
            return response()->json(['message' => 'Pas encore assez de commentaires pour générer un résumé utile.'], 422);
        }

        try {
            return response()->json(['summary' => $groq->summarizeQuartierDiscussion($publication)]);
        } catch (\RuntimeException $exception) {
            $status = str_contains(mb_strtolower($exception->getMessage()), 'quota') ? 429 : 422;

            return response()->json(['message' => $exception->getMessage()], $status);
        }
    }

    private function quartier(Request $request): Quartier
    {
        $quartier = $request->user()->quartier;
        abort_unless($quartier, 422, 'Veuillez d’abord sélectionner votre quartier dans votre profil.');

        return $quartier;
    }

    private function ensureSameQuartier(Request $request, PublicationQuartier $publication): void
    {
        abort_unless($publication->quartier_id === $this->quartier($request)->id, 403);
    }
}
