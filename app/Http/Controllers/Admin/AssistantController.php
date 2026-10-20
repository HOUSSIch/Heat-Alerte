<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminAssistantContextService;
use App\Services\HeatAlertAiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssistantController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.assistant.index', [
            'history' => $request->session()->get('admin_chat_history', []),
        ]);
    }

    public function send(Request $request, AdminAssistantContextService $contextService, HeatAlertAiService $ai): RedirectResponse
    {
        $data = $request->validate(['question' => ['required', 'string', 'max:2000']]);
        $history = $request->session()->get('admin_chat_history', []);
        $history[] = ['role' => 'user', 'content' => $data['question']];

        try {
            $context = $contextService->buildContext();
            $answer = $ai->ask('Tu es Assistant HeatAlert pour un administrateur. Réponds en français, de façon concise et utile à la surveillance. N’invente aucune donnée et ne révèle jamais mots de passe, clés API, tokens ou sessions. Contexte administratif synthétique : '.json_encode($context, JSON_UNESCAPED_UNICODE).'. Question : '.$data['question']);
            $history[] = ['role' => 'assistant', 'content' => $answer];
        } catch (\RuntimeException $exception) {
            $history[] = ['role' => 'assistant', 'content' => $exception->getMessage()];
        } catch (\Throwable) {
            $history[] = ['role' => 'assistant', 'content' => 'Le service Assistant HeatAlert est momentanément indisponible.'];
        }

        $request->session()->put('admin_chat_history', array_slice($history, -12));

        return redirect()->route('admin.assistant.index');
    }
}
