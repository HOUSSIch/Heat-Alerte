<?php

namespace App\Http\Controllers;

use App\Models\Equipement;
use App\Services\EquipementQrService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class EquipementPdfController extends Controller
{
    public function download(Request $request, Equipement $equipement, EquipementQrService $qrService): Response
    {
        $equipement = $request->user()->equipements()
            ->with([
                'conseils' => fn ($query) => $query->where('actif', true),
                'entretiens' => fn ($query) => $query->orderByDesc('date_realisee')->orderBy('date_prevue'),
            ])
            ->findOrFail($equipement->id);

        $entretiens = $equipement->entretiens;
        $photoDataUri = $equipement->photo && Storage::disk('public')->exists($equipement->photo)
            ? 'data:'.Storage::disk('public')->mimeType($equipement->photo).';base64,'.base64_encode(Storage::disk('public')->get($equipement->photo))
            : null;
        $logoPath = public_path('images/heatalert-logo-final.png');
        $logoDataUri = File::exists($logoPath)
            ? 'data:image/png;base64,'.base64_encode(File::get($logoPath))
            : null;

        return Pdf::loadView('equipements.pdf', [
            'equipement' => $equipement,
            'reference' => $qrService->publicReference($equipement),
            'prochainEntretien' => $entretiens
                ->filter(fn ($entretien) => ! $entretien->date_realisee && $entretien->date_prevue->gte(today()))
                ->sortBy('date_prevue')
                ->first(),
            'dernierEntretien' => $entretiens
                ->filter(fn ($entretien) => $entretien->date_realisee)
                ->sortByDesc('date_realisee')
                ->first(),
            'photoDataUri' => $photoDataUri,
            'logoDataUri' => $logoDataUri,
        ])
            ->setPaper('a4')
            ->download('fiche-'.$qrService->publicReference($equipement).'.pdf');
    }
}
