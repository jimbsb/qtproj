@props([
    'data' => null,
    'action' => null,
    'requiredFields' => ['responder', 'priority', 'subject'],
    'method' => 'POST',
])

@php
    $default_data = [
        'responder' => '',
        'priority' => 'Medium',
        'assignee' => '',
        'description' => '',
        'subject' => '',
    ];
    $responders = ['IT', 'Facility', 'Data & System'];
    $priorities = ['High', 'Medium', 'Low'];
    $assignees = ['Alice Smith', 'David Kim', 'Engineering Team']; // e.g. ['Alice Smith', 'David Kim', 'Engineering Team']
@endphp

<div x-data="data({{ json_encode($data ?? $default_data) }}, {{ json_encode($requiredFields) }})" x-on:ticket-modal.window="handleOpen($event)" x-on:keydown.escape.window="handleClose()"
    x-show="open" x-cloak class="create-ticket-modal fixed inset-0 z-50 flex items-center justify-center p-4">
    {{-- Backdrop --}}
    <div } }" x-on:keydown.escape.window="handleClose()" x-show="open" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4">
        {{-- Backdrop --}}
        <div x-show="open" x-transition.opacity @click="handleClose()" class="absolute inset-0 bg-gray-900/40"></div>

        {{-- Modal --}}
        <div x-show="open" x-transition
            class="relative flex max-h-[95vh] w-full max-w-xl flex-col rounded-2xl bg-white shadow-xl">
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-gray-100  px-6 py-3">
                <h2 class="text-lg font-semibold text-gray-900">Create Ticket</h2>
                <button type="button" @click="handleClose()"
                    class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <form {{-- method="POST" --}} {{-- action="{{ $action }}" --}} {{-- action="{{ $action ?? route('tickets.store') }}" --}} x-ref="ticketForm"
                class="flex-1 overflow-y-auto py-5  px-6" @submit.prevent="onSubmit">
                @csrf
                {{-- @if (strtoupper($method) !== 'POST')
                @method($method)
            @endif --}}

                {{-- Responder --}}
                <div class="mb-4 responder">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Responder
                    </label>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($responders as $option)
                            <button type="button" @click="data.responder = '{{ $option }}'"
                                :class="data.responder === '{{ $option }}' ?
                                    'bg-blue-50 text-blue-700 border-blue-300 ring-1 ring-blue-300' :
                                    'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                                class="rounded-full border px-4 py-1.5 text-sm font-medium transition">
                                {{ $option }}
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="responder" :value="data.responder" required>
                </div>

                <div class="relative mb-4 subject error transition">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Subject
                    </label>
                    <input type="text" required name="subject" x-model="data.subject" value="old('data.subject')"
                        placeholder="Brief summary of the issue"
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400">
                    <span class="hidden bottom-[-16px] font-sx left-4 text-red-600 text-xs">This field is required.</span>
                </div>

                {{-- Description --}}
                <div class="relative mb-4 description">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Description
                    </label>
                    <textarea name="description" rows="4" value="old('data.description')"
                        @keyup="data.description = $event.target.value" placeholder="Describe the issue in detail..."
                        class="w-full resize-none rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400"></textarea>
                    <span class="hidden bottom-[-10px] font-sx left-4 text-red-600 text-xs">This field is required.</span>
                </div>

                {{-- Priority --}}
                <div class="relative mb-4 priority">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Priority
                    </label>
                    <div class="relative flex flex-wrap gap-2">
                        @foreach ($priorities as $option)
                            <button type="button" @click="data.priority = '{{ $option }}'"
                                :class="data.priority == '{{ $option }}' ?
                                    'bg-gray-900 text-white border-gray-900' :
                                    'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                                class="rounded-full border px-4 py-1.5 text-sm font-medium transition">
                                {{ $option }}
                            </button>
                        @endforeach
                    </div>
                    <span class="hidden bottom-[-16px] font-sx left-4 text-red-600 text-xs">This field is required.</span>
                    <input type="hidden" name="priority" :value="data.priority" required>
                </div>

                {{-- Request Assignee --}}
                <div class="relative assignee">
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">
                        Request Assignee
                    </label>
                    <select name="assignee" x-model="data.assignee"
                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400">
                        <option value="">Unassigned</option>
                        @foreach ($assignees as $name)
                            <option value="{{ $name }}" @selected(old('assignee') === $name)>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="hidden bottom-[-16px] font-sx left-4 text-red-600 text-xs">This field is required.</span>
                </div>
                <button type="submit" class="hidden"></button>
            </form>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 border-t border-gray-100 py-3 px-6">
                <div class="error-message text-red-500 text-xs mr-auto hidden">sadas</div>
                <button type="button" @click="handleClose()"
                    class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" @click="$refs.ticketForm.querySelector('button[type=submit]').click()"
                    class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                    Create Ticket
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    console.log('ticket-modal script loaded');

    function data(data, requiredFields) {
        return {
            open: false,
            data: data ?? this.default.data,
            requiredFields: requiredFields ?? this.default.requiredFields,
            handleOpen,
            handleClose,
            default: {
                data: {
                    'responder': '',
                    'priority': 'Medium',
                    'assignee': null,
                    'description': '',
                    'subject': '',
                },
                requiredFields: ['responder', 'priority', 'subject']
            },
        }
    }

    function handleOpen(event) {
        const payload = event.detail ?? {};
        console.log('ticket-modal opened with payload:', payload);
        this.data = {
            ...this.data,
            ...(payload.data ?? {})
        };
        this.requiredFields = payload.requiredFields ?? [];
        this.open = true;
    }

    function handleClose() {
        this.data = this.default.data;
        this.requiredFields = this.default.requiredFields ?? ['responder', 'priority', 'subject'];
        console.log('ticket-modal closed with payload:', this.data);
        this.open = false;
    }

    function onSubmit(e) {
        console.log('ticket-modal submitted with payload:', this.data);
        console.log(document.querySelector('.create-ticket-modal .error-message'));
        let validate = [];
        let message = "Field"
        let last_message_part = "is required"

        for (let i = 0; i < this.requiredFields.length; i++) {
            const prop = this.requiredFields[i];
            console.log(prop, this.data[prop], !this.data[prop]);
            if (!this.data[prop])
                validate.push(prop);
        }

        console.log(validate)

        // Add your submission logic here, e.g., send data to the server
    }

    function onSelectResponder(e) {
        this.data.assignee = null;
        this.data.responder = e.target.value;

        $ajax({
            url: `/api/v1/assignees/${e.target.value}`,
            method: 'GET',
            success: (response) => {
                this.assignees = response.assignees;
            },
            error: (error) => {
                console.error('Error fetching assignees:', error);
            }
        });
    }
</script>
