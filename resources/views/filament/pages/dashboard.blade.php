<x-filament-panels::page>
    {{-- Custom Dashboard Content --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold tracking-tight">{{ __('Dashboard') }}</h1>
        <x-filament::badge color="success">
            {{ now()->format('F j, Y') }}
        </x-filament::badge>
    </div>

    {{-- Header Widgets --}}
    @if ($this->getHeaderWidgets())
        <x-filament-widgets::widgets
            :widgets="$this->getHeaderWidgets()"
            :columns="$this->getHeaderWidgetsColumns()"
            :data="$this->getWidgetData()"
        />
    @endif

    {{-- Main Content Widgets --}}
    @if ($this->getWidgets())
        <x-filament-widgets::widgets
            :widgets="$this->getWidgets()"
            :columns="$this->getWidgetsColumns()"
            :data="$this->getWidgetData()"
        />
    @endif
</x-filament-panels::page>
