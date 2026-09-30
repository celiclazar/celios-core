<x-filament-panels::page>
    <!-- Privacy & Compliance Overview Header Cards -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- GDPR & ePrivacy Badge -->
        <div class="rounded-xl p-4 border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-1.5">
                        <x-filament::icon icon="heroicon-o-shield-check" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                        GDPR & ePrivacy
                    </span>
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                        Compliant
                    </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Prior-consent enforcement. Third-party scripts are withheld until user acceptance.</p>
            </div>
            <div class="mt-3 pt-2 border-t border-gray-100 dark:border-gray-800 text-[11px] text-gray-400">
                Granular categories (Necessary, Analytics, Marketing, Functional)
            </div>
        </div>

        <!-- Script Injection Engine Card -->
        <div class="rounded-xl p-4 border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-1.5">
                        <x-filament::icon icon="heroicon-o-code-bracket" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                        Dynamic Script Injector
                    </span>
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        Zero-Cookie Leak
                    </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Injected scripts execute only after consent is granted and are persisted seamlessly.</p>
            </div>
            <div class="mt-3 pt-2 border-t border-gray-100 dark:border-gray-800 text-[11px] text-gray-400">
                Supports Google Analytics 4, Meta Pixel, Matomo, GTM & Custom JS
            </div>
        </div>

        <!-- Footer Control Card -->
        <div class="rounded-xl p-4 border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-1.5">
                        <x-filament::icon icon="heroicon-o-adjustments-horizontal" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                        Always Reconfigurable
                    </span>
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                        Revocable
                    </span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Visitors can re-open their preferences modal at any time via the footer link.</p>
            </div>
            <div class="mt-3 pt-2 border-t border-gray-100 dark:border-gray-800 text-[11px] text-gray-400">
                Customizable layout: full-width bar, floating cards or center modal
            </div>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" size="lg">
                {{ __('actions.save') ?? 'Save Cookie Settings' }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
