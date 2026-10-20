<div class="modal fade qr-modal" id="qr-modal-{{ $equipement->id }}" tabindex="-1" aria-labelledby="qr-modal-title-{{ $equipement->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div>
                    <p class="profile-kicker">Accès rapide</p>
                    <h2 class="modal-title" id="qr-modal-title-{{ $equipement->id }}">QR Code HeatAlert</h2>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body text-center">
                <p class="qr-modal-equipment">{{ $equipement->nom }}</p>
                <img src="{{ route('equipements.qr', $equipement) }}?v=passport-v2" alt="QR Code du passeport {{ $equipement->nom }}" class="qr-modal-image">
                <p class="qr-modal-help">Scannez pour consulter le passeport technique de cet équipement.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <a class="profile-secondary-action" href="{{ route('equipements.qr.download', $equipement) }}"><i class="bi bi-download"></i> Télécharger QR</a>
                <a class="btn-main" href="{{ route('equipements.pdf.download', $equipement) }}"><i class="bi bi-file-earmark-pdf"></i> Télécharger la fiche PDF</a>
                <button type="button" class="qr-modal-close" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
