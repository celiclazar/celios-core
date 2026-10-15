<x-filament-panels::page>
    @php
        $themes = $this->getAvailableThemes();
        $activeThemeId = $this->getActiveThemeId();
    @endphp

    <!-- Themes Gallery -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Installed Website Themes</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Themes loaded from packages and local <code>themes/</code> directory.</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-primary-100 text-primary-800 dark:bg-primary-900/40 dark:text-primary-300">
                {{ $themes->count() }} {{ \Illuminate\Support\Str::plural('Theme', $themes->count()) }} Available
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
                                <span class="material-symbols-outlined text-4xl mb-1">web</span>
                                <span class="text-[11px] font-medium uppercase tracking-wider">{{ $theme->getId() }}</span>
                            </div>
                        @endif

                        @if($isActive)
                            <div class="absolute top-2.5 right-2.5 bg-emerald-500 text-white text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full shadow-sm flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                Active Theme
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
                            <span class="text-[10px] uppercase font-semibold text-gray-400 tracking-wider block mb-1">Declared Slots:</span>
                            <div class="flex flex-wrap gap-1">
                                @forelse($theme->getMenuLocations() as $locKey => $locLabel)
                                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 dark:bg-gray-800 text-slate-700 dark:text-gray-300 font-medium">
                                        {{ $locLabel }}
                                    </span>
                                @empty
                                    <span class="text-[10px] text-gray-400">Standard slots</span>
                                @endforelse
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-2 flex items-center justify-between">
                            <span class="text-[11px] text-gray-400">By {{ $theme->getAuthor() }}</span>
                            @if(!$isActive)
                                <button type="button" 
                                        wire:click="activateTheme('{{ $id }}')"
                                        class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold bg-primary text-white hover:bg-primary-600 transition shadow-sm cursor-pointer">
                                    <span>Activate</span>
                                    <span class="material-symbols-outlined text-xs">arrow_forward</span>
                                </button>
                            @else
                                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">check_circle</span>
                                    Currently In Use
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Visual Color Preview Swatches (For Default & Core Palette) -->
    <div class="mb-6">
        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 mb-1">Color Palette Swatches</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Color variables and surface tokens for admin and default core stylesheets.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Ocean Blue Card -->
            <div class="rounded-xl p-4 border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Ocean Blue</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Corporate</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Corporate blue accents with balanced slate surfaces.</p>
                </div>
                <div class="flex items-center gap-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <span class="w-6 h-6 rounded-full inline-block shadow-inner border border-black/10" style="background-color: #4474bf;" title="Primary: #4474bf"></span>
                    <span class="w-6 h-6 rounded-full inline-block shadow-inner border border-black/10" style="background-color: #3f6654;" title="Secondary: #3f6654"></span>
                    <span class="w-6 h-6 rounded-full inline-block shadow-inner border border-black/10" style="background-color: #088557;" title="Success: #088557"></span>
                    <span class="w-6 h-6 rounded-full inline-block shadow-inner border border-black/10" style="background-color: #e8eeff;" title="Surface: #e8eeff"></span>
                </div>
            </div>

            <!-- Emerald Forest Card -->
            <div class="rounded-xl p-4 border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Emerald Forest</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">Eco / Fresh</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Fresh emerald primary with sky blue and mint surfaces.</p>
                </div>
                <div class="flex items-center gap-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <span class="w-6 h-6 rounded-full inline-block shadow-inner border border-black/10" style="background-color: #059669;" title="Primary: #059669"></span>
                    <span class="w-6 h-6 rounded-full inline-block shadow-inner border border-black/10" style="background-color: #0284c7;" title="Secondary: #0284c7"></span>
                    <span class="w-6 h-6 rounded-full inline-block shadow-inner border border-black/10" style="background-color: #16a34a;" title="Success: #16a34a"></span>
                    <span class="w-6 h-6 rounded-full inline-block shadow-inner border border-black/10" style="background-color: #dcfce7;" title="Surface: #dcfce7"></span>
                </div>
            </div>

            <!-- Midnight Obsidian Card -->
            <div class="rounded-xl p-4 border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Midnight Obsidian</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">Modern / Noir</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Vibrant violet accents with amber secondary and high-contrast surfaces.</p>
                </div>
                <div class="flex items-center gap-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <span class="w-6 h-6 rounded-full inline-block shadow-inner border border-black/10" style="background-color: #7c3aed;" title="Primary: #7c3aed"></span>
                    <span class="w-6 h-6 rounded-full inline-block shadow-inner border border-black/10" style="background-color: #d97706;" title="Secondary: #d97706"></span>
                    <span class="w-6 h-6 rounded-full inline-block shadow-inner border border-black/10" style="background-color: #10b981;" title="Success: #10b981"></span>
                    <span class="w-6 h-6 rounded-full inline-block shadow-inner border border-black/10" style="background-color: #27272a;" title="Surface: #27272a"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" size="lg">
                Save Theme Settings
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
