<?php

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\On;

new class extends Component {
    public string $title;
    public array $headers;
    public array $data;
    public string $endpoint_url = '';
    public array $actions = [];
    public string $subject = 'Initial subject'; // Added property for testing
    public int $key = 0; // Added property for testing
    public array $page_details = [];
    public int $active_page = 1;
    public array $pages = [];

    // public function mount(string $title = '', string $endpoint_url = '', array $headers = [], array $data = []): void
    // {
    //     $this->title   = $title;
    //     $this->headers = $headers;
    //     $this->data    = $data;
    //     $this->endpoint_url= $endpoint_url;
    // }

    #[On('ticket-created')]
    public function refreshTable()
    {
        printf("Ticket created event received. Refreshing table data from endpoint: %s\n", $this->endpoint_url);
    } // render() re-runs, table refreshes

    #[On('search-tickets')]
    public function searchData($query)
    {
        $query = strtolower($query);
        $this->fetchData($query);

        $this->data = array_filter($this->data, function ($item) use ($query) {
            foreach ($this->headers as $header) {
                $field = $header['field'];
                if (isset($item[$field]) && str_contains(strtolower((string) $item[$field]), $query)) {
                    return true;
                }
            }
        });

        return view('components.table', ['data' => $this->data, 'headers' => $this->headers, 'actions' => $this->actions])->render();

        // \Log::info('ticket-created received');

        // $this->subject = 'listener fired'; // changes UI
        // $this->key++; // changes UI
    }

    public function page($query)
    {
        $this->fetchData(null, $query);
    }

    public function mount(): void
    {
        $this->fetchData();
    }

    function fetchData($query_params = null, $url = null): void
    {
        if (filled($this->endpoint_url)) {
            if ($url) {
                $response = Http::timeout(60)->get($url);
            } else {
                $response = Http::timeout(60)->get($this->endpoint_url, ['search' => $query_params ?? '', 'per_page' => $query_params ? 1000 : 10]);
            }

            if ($response->failed()) {
                // handle error
            }

            $_data = $response->json();
            $this->data = $_data['data'];
            $this->dispatch('page-update', ['data' => $_data, 'url' => $url ? [] : ['search' => $query_params ?? '', 'per_page' => $query_params ? 1000 : 10]]);
            unset($_data['data']);
            $this->page_details = $_data;
            $this->active_page = $_data['current_page'];

            $pages = array_slice($_data['links'], 1, -1);
            if (count($pages) != count($this->pages)) {
                $this->pages = array_slice($_data['links'], 1, -1);
            }
        }
    }
};

?>


<div
    class="w-full overflow-x-auto relative md:rounded-lg rounded-lg border-squircle border border-gray-200 bg-gray-200 shadow-sm md:h-[calc(100%-38px)] h-[calc(100%-80px)] relative flex flex-col justify-between">
    <table class="w-full text-left text-sm relative">
        <thead class="bg-gray-100">
            <tr class="border-b border-gray-200 text-xs uppercase tracking-wide text-gray-500 sticky top-0 z-10">
                @foreach ($headers as $header)
                    @if (data_get($header, 'type') == 'actions')
                        <th class="px-2 py-3 pl-3 font-medium text-nowrap">
                            {{ $header['name'] }}
                        </th>
                    @else
                        <th class="px-2 py-3 pl-3 font-medium text-nowrap {{ $header['css_class'] ?? '' }}">
                            {{ $header['name'] }}
                        </th>
                    @endif
                @endforeach
                @if ($actions && data_get(collect($headers)->last(), 'type') !== 'actions')
                    <th></th>
                @endif
            </tr>
        </thead>
        <tbody class="bg-white">
            @placeholder
                <div>
                    <!-- Loading spinner... -->
                    <svg>...</svg>
                </div>
            @endplaceholder
            @forelse ($data as $ticket)
                <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50">
                    @foreach ($headers as $header)
                        @if (data_get($header, 'type') == 'actions')
                            @continue
                        @endif
                        <td class="p-2 px-3 max-w-50 overflow-hidden text-ellipsis whitespace-nowrap text-gray-700">
                            @php
                                $value = data_get($ticket, $header['field']);
                                $is_array = is_array($value) && array_key_exists('value', $value);
                                if ($is_array) {
                                    $status = data_get($value, 'status');
                                    $class = data_get($value, 'class') ?? data_get($value, 'class');
                                    $value = data_get($value, 'value');
                                }
                            @endphp
                            @if (data_get($header, 'type') && $header['type'] == 'time')
                                {{ filled($header['parser']) ? \App\Helper::parser('time', [...$header['parser'], 'timestamp' => $value]) : $value }}
                            @elseif($header['field'] == 'status' || data_get($header, 'type') == 'status' || $is_array)
                                <span
                                    class="status status-badge {{ strtolower(implode('-', explode(' ', $is_array ? $status : $value))) }} {{ $class ?? '' }}">{{ $value }}</span>
                            @elseif(is_array($value))
                                {{ $value['title'] ?? '-' }}
                                @if (array_key_exists('description', $value))
                                    <span
                                        class="text-xs text-gray-400 block overflow-x-hidden text-ellipsis text-nowrap pr-[10%]">{{ $value['description'] }}</span>
                                @endif
                            @else
                                {{ filled($value) ? $value : '-' }}
                            @endif
                        </td>
                    @endforeach
                    {{-- Actions popout --}}
                    @if (!empty($actions))
                        @php
                            $sorted = collect($actions)
                                ->sortBy(fn($i) => ($i['action'] ?? '') === 'delete' ? 1 : 0)
                                ->values();

                            $colors = [
                                'default' => 'text-gray-700 hover:bg-gray-100',
                                'primary' => 'text-blue-600 hover:bg-blue-50',
                                'success' => 'text-green-600 hover:bg-green-50',
                                'warning' => 'text-amber-600 hover:bg-amber-50',
                                'danger' => 'text-red-600 hover:bg-red-50',
                            ];
                            if (filled($item['custom-tailwind'] ?? '')) {
                                $colors['custom'] = $item['custom-tailwind'];
                            }
                        @endphp
                        <td class=" text-center">
                            <div x-data="{ open: false }" x-on:click.outside="open = false"
                                x-on:keydown.escape.window="open = false" class="relative inline-block text-left">

                                <button type="button" @click="open = !open"
                                    class="inline-flex items-center justify-center rounded-lg text-white hover:bg-blue-450 bg-blue-500">
                                    Actions
                                </button>

                                <div x-show="open" x-cloak x-transition
                                    class="absolute right-0 z-50 mt-1 w-40 rounded-xl border border-gray-200 bg-white py-1 shadow-lg overflow-hidden">
                                    {{-- <div class="absolute right-0 top-0 h-2 w-2 translate-x-1/2 -translate-y-1/2 rotate-45 bg-white"></div> --}}
                                    {{-- <div class="absolute right-0 top-0 h-2 w-2 translate-x-1/2 -translate-y-1/2 rotate-45 bg-white border-t border-l border-gray-200"></div> --}}
                                    @foreach ($sorted as $item)
                                        <button type="button"
                                            @click="open = false; $dispatch('{{ $item['action'] }}', { id: '{{ data_get($ticket, 'ticket_id') }}' })"
                                            class="block w-full px-3 py-2 !rounded-none text-left text-sm {{ $item['custom-tailwind'] ?? ($colors[$item['color'] ?? 'default'] ?? $colors['default']) }} ">
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

    <div
        class="w-[-web-webkit-fill-available] flex items-center border-t border-gray-200 border-b-0 px-3 py-2 pl-3 text-sm text-gray-600 bg-gray-50 sticky left-0 mb-[-2px] bottom-0">
        @php
            $current_page = $page_details['current_page'];
            $per_page = $page_details['per_page'];
            $total_data = $page_details['total'];
            $last_page = $page_details['last_page'];

            $max_of_current_page = $current_page * $per_page;
            $least_of_current_page = 1 + $max_of_current_page - $per_page;
            $if_total_lower_than_length = $total_data < $per_page;
        @endphp
        <span {{ count($data) ? '' : 'hidden' }}">
            <span>Showing by
                <select class="border-1 border-gray-350 p-1 rounded" wire:change="page($event.target.value)">
                    @for ($page = 1; $page <= ($page_details['last_page'] ?? 1); $page++)
                        <option value="{{ $page }}" @selected($page == $active_page)>{{ $page }}
                        </option>
                    @endfor
                </select> the
            </span>
            {{ $least_of_current_page }} to
            {{ $if_total_lower_than_length ? $total_data : $per_page * $current_page }} of
            {{ $total_data }}
            {{ strtolower($title ?? 'data') }}
        </span>
        <div class="flex gap-2 ml-auto items-center">
            <span>Page:
                <select class="border-1 border-gray-350 p-1 mr-2 rounded" wire:change="page($event.target.value)">
                    @foreach ($pages as $_page)
                        <option value="{{ $_page['url'] }}" @selected($page == $active_page)>{{ $_page['page'] }} of
                            {{ $last_page }}
                        </option>
                    @endforeach
                </select></span>
            <button
                class="rounded-md !bg-gray-400 border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:!bg-gray-500">
                Previous
            </button>
            <button class="rounded-md bg-gray-900 px-3 py-1.5 text-sm text-white hover:bg-gray-700">
                Next
            </button>
        </div>
    </div>
    <div x-data x-on:ticket-view.window="console.log('view', $event.detail.id)" x-data
        x-on:page-update.window="console.log('view', $event)"
        x-on:ticket-edit.window="console.log('edit', $event.detail.id)"
        x-on:ticket-delete.window="if (confirm('Delete?')) console.log('delete', $event)">
    </div>
</div>
