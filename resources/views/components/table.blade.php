<?php

use Livewire\Component;
use Illuminate\Support\Facades\Http;

new class extends Component
{
    public string $title;
    public Array $headers;
    public Array $data;
    public string $endpoint_url = '';
    public Array $actions;

    // public function mount(string $title = '', string $endpoint_url = '', array $headers = [], array $data = []): void
    // {
    //     $this->title   = $title;
    //     $this->headers = $headers;
    //     $this->data    = $data;
    //     $this->endpoint_url= $endpoint_url;
    // }

    public function mount(): void {
        if(filled($this->endpoint_url)) {
            $response = Http::timeout(30)
                ->get($this->endpoint_url);

            if ($response->failed()) {
                // handle error
            }
            $_data = $response->json();
            // dump($_data);
            $this->data = $_data['data'];
            // $this->data = [];
        };
    }
}

?>


<div class="w-full overflow-x-auto relative md:rounded-lg rounded-lg border-squircle border border-gray-200 bg-gray-200 shadow-sm md:h-[calc(100%-38px)] h-[calc(100%-80px)] relative flex flex-col justify-between">
    <table class="w-full text-left text-sm relative">
        <thead class="bg-white">
            <tr class="border-b border-gray-200 text-xs uppercase tracking-wide text-gray-500 bg-gray-100 sticky top-0 z-10">
                @foreach ($headers as $header)
                    <th class="px-2 py-3 pl-3 font-medium text-nowrap">
                        {{ $header['name'] }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white">
            @forelse ($data as $ticket)
                <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50">
                    @foreach ($headers as $header)
                        <td class="p-2 px-3 max-w-100 overflow-hidden">
                            
                            @php
                                $value = data_get($ticket, $header['field']);
                                $is_array = is_array($value) && array_key_exists('value', $value);
                                if($is_array)
                                {
                                    $status = data_get($value, 'status');
                                    $class = data_get($value, 'class') ?? data_get($value, 'class');
                                    $value = data_get($value, 'value');
                                }
                            @endphp
                            @if(data_get($header, 'type') && $header['type'] == 'time')
                                {{ filled($header['parser']) ? \App\Helper::parser('time', [...$header['parser'], 'timestamp'=>$value]) : $value }}

                            @elseif($header['field'] == 'status' || data_get($header, 'type') == 'status' || $is_array)
                                <span class="status status-badge {{ 
                                    strtolower(implode('-',explode(' ', $is_array ? $status : $value)))
                                }} {{ $class ?? '' }}">{{ $value }}</span>

                            @elseif(is_array($value))
                                {{ $value['title'] ?? '-' }}
                                @if (array_key_exists('description', $value))
                                    <span class="text-xs text-gray-400 block overflow-x-hidden text-ellipsis text-nowrap pr-[10%]">{{ $value['description'] }}</span>
                                @endif
                                
                            @else   
                                {{ filled($value) ? $value : '-' }}
                            @endif
                        </td>
                    @endforeach
                                        {{-- Actions dropdown --}}
                    @if (!empty($actions))
                        @php
                            $sorted = collect($actions)->sortBy(fn ($i) => ($i['action'] ?? '') === 'delete' ? 1 : 0)->values();

                            $colors = [
                                'default' => 'text-gray-700 hover:bg-gray-50',
                                'primary' => 'text-blue-600 hover:bg-blue-50',
                                'success' => 'text-green-600 hover:bg-green-50',
                                'warning' => 'text-amber-600 hover:bg-amber-50',
                                'danger'  => 'text-red-600 hover:bg-red-50',
                            ];
                        @endphp
                        <td class=" text-center">
                            <div x-data="{ open: false }"
                                x-on:click.outside="open = false"
                                x-on:keydown.escape.window="open = false"
                                class="relative inline-block text-left">

                                <button type="button"
                                        @click="open = !open"
                                        class="inline-flex items-center justify-center rounded-lg text-white hover:bg-blue-450 bg-blue-500">
                                        Actions
                                </button>

                                <div x-show="open" x-cloak x-transition
                                    class="absolute right-0 z-50 mt-1 w-40 rounded-xl border border-gray-200 bg-white py-1 shadow-lg">
                                    @foreach ($sorted as $item)
                                        <button type="button"
                                                @click="open = false; $dispatch('{{ $item['action'] }}', { id: '{{ data_get($ticket, 'ticket_id') }}' })"
                                                class="block w-full px-3 py-2 text-left text-sm {{ $colors[$item['color'] ?? 'default'] ?? $colors['default'] }}">
                                            {{ $item['label'] }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="px-6 py-8 text-center text-gray-200">
                        No data found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="w-[-web-webkit-fill-available] flex items-center border-t border-gray-200 px-6 py-2 pl-3 text-sm text-gray-600 bg-white sticky bottom-0">
        <span {{ count($data) ? '' : 'hidden' }}">Showing 1 to {{ count($data) }} of {{ count($data) }} {{ strtolower($title ?? 'data')}}</span>
        <div class="flex gap-2 ml-auto">
            <button class="rounded-md !bg-gray-400 border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:!bg-gray-500">
                Previous
            </button>
            <button class="rounded-md bg-gray-900 px-3 py-1.5 text-sm text-white hover:bg-gray-700">
                Next
            </button>
        </div>
    </div>
    <div x-data
        x-on:ticket-view.window="console.log('view', $event.detail.id)"
        x-on:ticket-edit.window="console.log('edit', $event.detail.id)"
        x-on:ticket-delete.window="if (confirm('Delete?')) console.log('delete', $event.detail.id)">
        ...
    </div>
</div>