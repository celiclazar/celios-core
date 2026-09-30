<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Gornje dugme za masovno čitanje --}}
        {{-- Sređeno i pozicionirano dugme za masovno čitanje --}}
        @if(auth()->user()->unreadNotifications->count() > 0)
            <div style="display: flex; justify-content: flex-end; margin-bottom: 24px; margin-top: -8px;">
                <x-filament::button
                    wire:click="markAllAsRead"
                    color="gray"
                    icon="heroicon-m-check-circle"
                    size="sm">
                    {{ __('Označi sve kao pročitano') }}
                </x-filament::button>
            </div>
        @endif

        {{-- Lista obaveštenja --}}
        <div class="space-y-4">
            @forelse($this->notifications as $notification)
                @php
                    $isUnread = $notification->unread();
                @endphp

                <x-filament::section style="margin-bottom: 16px;">
                    {{-- Glavni kontejner: delimo prostor na ikonu levo i sadržaj desno --}}
                    <div style="display: flex; align-items: flex-start; gap: 16px;">

                        {{-- 1. IKONA / INDIKATOR LEVO --}}
                        <div style="flex-shrink: 0; padding-top: 4px;">
                            @if($isUnread)
                                <div style="height: 10px; width: 10px; border-radius: 9999px; background-color: rgb(79, 70, 229); margin-top: 4px; box-shadow: 0 0 8px rgb(79, 70, 229);"></div>
                            @else
                                <x-filament::icon
                                    icon="heroicon-o-envelope-open"
                                    class="h-5 w-5 text-gray-400"
                                    style="width: 20px; height: 20px; color: #9ca3af;"
                                />
                            @endif
                        </div>

                        {{-- 2. SADRŽAJ DESNO --}}
                        <div style="flex-grow: 1; min-width: 0;">
                            {{-- Gornji red unutar sadržaja: Naslov levo, vreme desno --}}
                            <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 16px; margin-bottom: 4px;">
                                <strong style="font-size: 14px; font-weight: 600; color: currentColor;">
                                    {{ $notification->data['title'] ?? __('Obaveštenje') }}
                                </strong>
                                <span style="font-size: 12px; color: #9ca3af; white-space: nowrap;">
                    {{ $notification->created_at->diffForHumans() }}
                </span>
                            </div>

                            {{-- Donji red unutar sadržaja: Poruka obaveštenja --}}
                            <p style="font-size: 14px; color: #9ca3af; line-height: 1.5; margin: 0;">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                        </div>

                        {{-- 3. DUGME ZA ČITANJE SKROZ DESNO (ako je nepročitano) --}}
                        @if($isUnread)
                            <div style="flex-shrink: 0; padding-left: 8px;">
                                <x-filament::icon-button
                                    wire:click="markAsRead('{{ $notification->id }}')"
                                    icon="heroicon-m-check"
                                    color="success"
                                    size="sm"
                                />
                            </div>
                        @endif

                    </div>
                </x-filament::section>

            @empty
                {{-- Prazno stanje --}}
                <x-filament::section>
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px 0; gap: 12px;">
                        <x-filament::icon icon="heroicon-o-bell-slash" class="h-8 w-8 text-gray-400" style="width: 32px; height: 32px; color: #9ca3af;" />
                        <span style="font-size: 14px; font-weight: 500;">
                            {{ __('Nema obaveštenja') }}
                        </span>
                        <p style="font-size: 14px; color: #9ca3af; margin: 0;">
                            {{ __('Sve je čisto! Nemate novih poruka.') }}
                        </p>
                    </div>
                </x-filament::section>
            @endforelse
        </div>

        {{-- Paginacija --}}
        @if($this->notifications->hasPages())
            <div class="mt-4">
                {{ $this->notifications->links() }}
            </div>
        @endif

    </div>
</x-filament-panels::page>
