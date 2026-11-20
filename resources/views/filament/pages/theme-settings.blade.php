<x-filament-panels::page>
    @php
        $themes = $this->getAvailableThemes();
        $activeThemeId = $this->getActiveThemeId();
    @endphp

    <!-- Themes Gallery -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">{{ __('themes.installed_themes') }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('themes.installed_themes_description') }}</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-primary-100 text-primary-800 dark:bg-primary-900/40 dark:text-primary-300">
                {{ trans_choice('themes.themes_available', $themes->count(), ['count' => $themes->count()]) }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($themes as $id => $theme)
                @php
                    $isActive = ($id === $activeThemeId);
                @endphp
                <div class="relative rounded-2xl border transition-all duration-200 overflow-hidden flex flex-col justify-between {{ $isActive ? 'border-primary-500 ring-2 ring-primary-500/20 bg-primary-50/10 dark:bg-primary-950/10 shadow-md' : 'border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 hover:border-gray-300 dark:hover:border-gray-700 shadow-sm' }}">
                    
                    <!-- Screenshot / Preview Header -->
                    <div class="h-36 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-gray-800 dark:to-gray-950 flex items-center justify-center border-b border-gray-100 dark:border-gray-800 overflow-hidden relative group">
                        @if($theme->getScreenshotUrl())
                            <img src="{{ $theme->getScreenshotUrl() }}" alt="{{ $theme->getName() }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                <x-heroicon-o-computer-desktop class="w-9 h-9 mb-1" />
                                <span class="text-[11px] font-medium uppercase tracking-wider">{{ $theme->getId() }}</span>
                            </div>
                        @endif

                        @if($isActive)
                            <div class="absolute top-2.5 right-2.5 bg-emerald-500 text-white text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full shadow-sm flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                {{ __('themes.active_theme') }}
                            </div>
                        @endif
                    </div>

                    <!-- Theme Details -->
                    <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100 truncate">{{ $theme->getName() }}</h4>
                                <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">v{{ $theme->getVersion() }}</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2 leading-relaxed">
                                {{ $theme->getDescription() ?: 'Custom designed theme for Celios CMS.' }}
                            </p>
                        </div>

                        <!-- Menu Locations Support -->
                        <div class="pt-2 border-t border-gray-100 dark:border-gray-800/60">
                            <span class="text-[10px] uppercase font-semibold text-gray-400 tracking-wider block mb-1">{{ __('themes.declared_slots') }}</span>
                            <div class="flex flex-wrap gap-1">
                                @forelse($theme->getMenuLocations() as $locKey => $locLabel)
                                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 dark:bg-gray-800 text-slate-700 dark:text-gray-300 font-medium">
                                        {{ $locLabel }}
                                    </span>
                                @empty
                                    <span class="text-[10px] text-gray-400">{{ __('themes.standard_slots') }}</span>
                                @endforelse
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-2 flex items-center justify-between">
                            <span class="text-[11px] text-gray-400">{{ __('themes.by_author', ['author' => $theme->getAuthor()]) }}</span>
                            <div class="flex items-center gap-2">
                                @if(!$isActive && $id !== 'default' && $this->canDeleteThemes())
                                    <button type="button" 
                                            wire:click="deleteTheme('{{ $id }}')"
                                            wire:confirm="{{ __('themes.confirm_delete_theme', ['theme' => $theme->getName()]) }}"
                                            class="inline-flex items-center justify-center p-1.5 rounded-lg text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer"
                                            title="{{ __('themes.delete_theme') }}">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                @endif

                                @if(!$isActive)
                                    <button type="button" 
                                            wire:click="activateTheme('{{ $id }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-primary text-white hover:bg-primary-600 transition shadow-sm cursor-pointer">
                                        <span>{{ __('themes.activate') }}</span>
                                        <x-heroicon-m-arrow-right class="w-3.5 h-3.5" />
                                    </button>
                                @else
                                    <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                                        <x-heroicon-m-check-circle class="w-4 h-4 text-emerald-500" />
                                        {{ __('themes.currently_in_use') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Main Settings Form -->
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" size="lg">
                {{ __('themes.save_settings') }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
