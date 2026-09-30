<!-- DEMO MODAL POPUP -->
<div class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden items-center justify-center p-3 sm:p-space-4" id="demo-modal">
    <div class="bg-surface-container-lowest max-w-lg w-full rounded-2xl p-5 sm:p-space-6 border border-surface-container-high shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <button class="absolute top-3 sm:top-4 right-3 sm:right-4 p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors" onclick="closeDemoModal()" aria-label="Close modal">
            <span class="material-symbols-outlined">close</span>
        </button>
        <div class="flex items-center gap-3 mb-space-4 pr-8">
            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">admin_panel_settings</span>
            </div>
            <div>
                <h3 class="text-lg sm:text-xl font-bold text-on-surface">Celios Admin Sandbox (Test Mode)</h3>
                <p class="font-label-xs text-label-xs text-on-surface-variant">Filament v3 Demo Instanca</p>
            </div>
        </div>
        <div class="p-space-3 rounded-xl bg-surface-container-low border border-surface-container space-y-2 mb-space-4 text-body-sm font-body-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-0.5 sm:gap-2">
                <span class="text-on-surface-variant text-xs sm:text-sm">URL Pristup:</span>
                <span class="font-code-sm text-primary font-medium break-all text-xs sm:text-sm">{{ url('/admin') }}</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-0.5 sm:gap-2">
                <span class="text-on-surface-variant text-xs sm:text-sm">Korisničko ime:</span>
                <span class="font-code-sm text-on-surface font-semibold text-xs sm:text-sm">demo@celios.io</span>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-0.5 sm:gap-2">
                <span class="text-on-surface-variant text-xs sm:text-sm">Lozinka:</span>
                <span class="font-code-sm text-on-surface font-semibold text-xs sm:text-sm">celios-sandbox-2024</span>
            </div>
        </div>
        <p class="font-body-sm text-body-sm text-on-surface-variant mb-space-5">
            Imate potpunu slobodu da kreirate nove blokove, menjate jezike i testirate Livewire reaktivnost. Podaci se automatski resetuju svakog punog sata.
        </p>
        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 sm:gap-space-3">
            <button class="w-full sm:w-auto px-space-4 py-2.5 sm:py-2 rounded-lg text-on-surface-variant hover:bg-surface-container font-label-sm text-label-sm transition-colors text-center" onclick="closeDemoModal()">
                Zatvori
            </button>
            <a class="w-full sm:w-auto px-space-5 py-2.5 sm:py-2 rounded-lg bg-primary text-on-primary font-label-sm text-label-sm font-semibold shadow-sm hover:bg-primary-container transition-all text-center" href="{{ url('/admin') }}">
                Uđi u Dashboard
            </a>
        </div>
    </div>
</div>

<script>
    function openDemoModal() {
        const modal = document.getElementById('demo-modal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }
    function closeDemoModal() {
        const modal = document.getElementById('demo-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
</script>
