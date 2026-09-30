<x-filament-panels::page>
    <div class="mb-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            This module selector is exclusively accessible to the <strong>Superadmin</strong>. 
            Adjusting modules here instantly updates the available sidebar resources, page builder blocks, frontend routes, and SEO sitemaps across the entire CMS.
        </p>
    </div>

    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-end gap-3">
            <x-filament::button type="submit" size="lg" icon="heroicon-o-check">
                Apply & Save Configuration
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
