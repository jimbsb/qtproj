@props([
    'data'        => [],
    'action'      => null,
    'method'      => 'POST',
])

@php
$responders = ['IT', 'Facility', 'Electrical', 'Aircondition'];
$priorities = ['High', 'Medium', 'Low'];
$assignees =  ['Alice Smith', 'David Kim', 'Engineering Team'];   // e.g. ['Alice Smith', 'David Kim', 'Engineering Team']
@endphp

<div
    x-data="{{ filled($data) ? json_decode($data) :
        "{open: false,
        responder: '',
        priority: 'Medium',
        assignee: '',}"
    }}"
    x-on:open-ticket-modal.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition.opacity
        @click="open = false"
        class="absolute inset-0 bg-gray-900/40"
    ></div>

    {{-- Modal --}}
    <div
        x-show="open"
        x-transition
        class="relative flex max-h-[95vh] w-full max-w-xl flex-col rounded-2xl bg-white shadow-xl"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-gray-100  px-6 py-3">
            <h2 class="text-lg font-semibold text-gray-900">Create Ticket</h2>
            <button
                type="button"
                @click="open = false"
                class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <form
            method="POST"
            action="{{ $action }}"
            {{-- action="{{ $action ?? route('tickets.store') }}" --}}
            class="flex-1 overflow-y-auto py-5  px-6"
        >
            @csrf
            @if (strtoupper($method) !== 'POST')
                @method($method)
            @endif

            {{-- Responder --}}
            <div class="mb-4">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500 required">
                    Responder
                </label>
                <div class="flex flex-wrap gap-2">
                    @foreach ($responders as $option)
                        <button
                            type="button"
                            @click="responder = '{{ $option }}'"
                            :class="responder === '{{ $option }}'
                                ? 'bg-blue-50 text-blue-700 border-blue-300 ring-1 ring-blue-300'
                                : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                            class="rounded-full border px-4 py-1.5 text-sm font-medium transition"
                        >
                            {{ $option }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="responder" :value="responder">
                @error('responder')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subject --}}
            <div class="mb-4">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Subject
                </label>
                <input
                    required
                    type="text"
                    name="subject"
                    value="{{ old('subject') }}"
                    placeholder="Brief summary of the issue"
                    class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400"
                >
                @error('subject')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Description
                </label>
                <textarea
                    name="description"
                    rows="4"
                    placeholder="Describe the issue in detail..."
                    class="w-full resize-none rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Priority --}}
            <div class="mb-4">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Priority
                </label>
                <div class="flex flex-wrap gap-2">
                    @foreach ($priorities as $option)
                        <button
                            type="button"
                            @click="priority = '{{ $option }}'"
                            :class="priority === '{{ $option }}'
                                ? 'bg-gray-900 text-white border-gray-900'
                                : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                            class="rounded-full border px-4 py-1.5 text-sm font-medium transition"
                        >
                            {{ $option }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="priority" :value="priority">
            </div>

            {{-- Request Assignee --}}
            <div class="mb-2">
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Request Assignee
                </label>
                <select
                    name="assignee"
                    x-model="assignee"
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400"
                >
                    <option value="">Unassigned</option>
                    @foreach ($assignees as $name)
                        <option value="{{ $name }}" @selected(old('assignee') === $name)>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('assignee')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </form>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 border-t border-gray-100 py-3 px-6">
            <button
                type="button"
                @click="open = false"
                class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Cancel
            </button>
            <button
                type="submit"
                @click="$el.closest('.relative').querySelector('form').submit()"
                class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700"
            >
                Create Ticket
            </button>
        </div>
    </div>
</div>