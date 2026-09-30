<x-filament-panels::page>
    <!-- Visual Preview Swatches -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Ocean Blue Card -->
        <div class="rounded-xl p-4 border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Ocean Blue</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Default</span>
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
