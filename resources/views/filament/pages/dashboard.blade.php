<x-filament-panels::page>
    @include('filament.pages.dashboard-header')

    <div class="space-y-6">
        <!-- Top 4 Stats Cards -->
        @livewire(\Celios\Core\Filament\Widgets\StatsOverviewWidget::class)

        <!-- Main 2-Column Responsive Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left Column (8 cols): Portfolio Visual Inventory & Recent Resources Table -->
            <div class="lg:col-span-8 space-y-6">
                @livewire(\Celios\Core\Filament\Widgets\PortfolioVisualInventoryWidget::class)
                @livewire(\Celios\Core\Filament\Widgets\RecentResourcesTableWidget::class)
            </div>

            <!-- Right Column (4 cols): Localization & Activity Log -->
            <div class="lg:col-span-4 space-y-6">
                @livewire(\Celios\Core\Filament\Widgets\LocalizationWidget::class)
                @livewire(\Celios\Core\Filament\Widgets\ActivityLogWidget::class)
            </div>
        </div>
    </div>
</x-filament-panels::page>
