<?php

namespace App\Http\Controllers;

use App\Models\Equipement;
use App\Services\EquipementQrService;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EquipementQrController extends Controller
{
    public function show(Request $request, Equipement $equipement, EquipementQrService $qrService): Response
    {
        return $this->response($request, $equipement, $qrService, false);
    }

    public function download(Request $request, Equipement $equipement, EquipementQrService $qrService): Response
    {
        return $this->response($request, $equipement, $qrService, true);
    }

    private function response(Request $request, Equipement $equipement, EquipementQrService $qrService, bool $download): Response
    {
        $equipement = $request->user()->equipements()
            ->with([
                'conseils' => fn ($query) => $query->where('actif', true),
                'entretiens',
            ])
            ->findOrFail($equipement->id);

        $data = $qrService->buildPayload($equipement);

        $result = Builder::create()
            ->writer(new SvgWriter)
            ->data($data)
            ->size(320)
            ->margin(12)
            ->build();

        return response($result->getString(), 200, [
            'Content-Type' => $result->getMimeType(),
            'Content-Disposition' => ($download ? 'attachment' : 'inline').'; filename="equipement-'.$equipement->id.'-qr.svg"',
            'Cache-Control' => 'private, no-store, max-age=0, must-revalidate',
            'Pragma' => 'no-cache',
        ]);
    }
}
