@props([
    'collection_of'=> null,
    'headers' => [],
    'data' => [],
])

<div class="w-full overflow-x-auto relative md:rounded-lg rounded-lg border-squircle border border-gray-200 bg-gray-200 shadow-sm h-[calc(100%-34px)] relative flex flex-col justify-between">
    <table class="w-full text-left text-sm relative max-h-[calc(100%-64px)]">
        <thead class="bg-white">
            <tr class="border-b border-gray-200 text-xs uppercase tracking-wide text-gray-500">
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
                        <td class="p-2 px-3 align-top">
                            @php
                                $value = data_get($ticket, $header['field']);
                                $is_array = is_array($value) && array_key_exists('value', $value);
                                if($is_array)
                                {
                                    $status = data_get($value, 'status');
                                    $class = data_get($value, 'class') ?? "";
                                    $value = data_get($value, 'value');
                                }
                            @endphp
                            @if($header['field'] == 'status' || data_get($header, 'type') == 'status' || $is_array)
                                <span class="status status-badge {{ $is_array ? $status : $value }} {{ $class ?? '' }}">{{ $value }}</span>
                            @else   
                                {{ filled($value) ? $value : '-' }}
                            @endif
                        </td>
                    @endforeach
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

    <div class="w-full flex items-center border-t border-gray-200 px-6 py-4 pl-3 text-sm text-gray-600 bg-white">
        <span {{ count($data) ? '' : 'hidden' }}">Showing 1 to {{ count($data) }} of {{ count($data) }} {{ strtolower($collection_of ?? 'data')}}</span>
        <div class="flex gap-2 ml-auto">
            <button class="rounded-md !bg-gray-400 border-gray-300 px-3 py-1.5 text-sm text-gray-700 hover:!bg-gray-500">
                Previous
            </button>
            <button class="rounded-md bg-gray-900 px-3 py-1.5 text-sm text-white hover:bg-gray-700">
                Next
            </button>
        </div>
    </div>
</div>