<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component
{
    public $responder_id = '';
    public $priority     = 'Medium';
    public $subject      = '';
    public $description  = '';
    public $assignee     = '';

    protected $rules = [
        'responder_id' => 'required|string',
        'priority'     => 'required|string',
        'subject'      => 'required|string|max:255',
        'description'  => 'nullable|string',
        'assignee'     => 'nullable|string',
    ];

    #[On('on-select')]
    public function onSelect($option, $field)
    {
        $this->$field = $option;
        $this->dispatch('on-select');
    }

    public function save()
    {
        // $this->validate();

        // Ticket::create([
        //     'ticket_id'    => 'TK-' . strtoupper(uniqid()),
        //     'user_id'      => auth()->id(),
        //     'responder_id' => $this->responder_id,
        //     'priority_id'  => $this->priority,
        //     'subject'      => $this->subject,
        //     'description'  => $this->description,
        //     'assigned_to'  => $this->assignee,
        // ]);

        $this->reset(['responder_id', 'priority', 'subject', 'description', 'assignee']);
        $this->priority = 'Medium';

        $this->dispatch('ticket-created');
        $this->dispatch('close-ticket-modal');
    }

    #[On('on-select')]
    public function refreshTable() {
        printf("Ticket created event received. Refreshing table data from endpoint: %s\n", $this->endpoint_url);
    }   // render() re-runs, table refreshes

    public function with(): array
    {
        return [
            'responders' => ['IT', 'Facility', 'Data & System'],
            'priorities' => ['High', 'Medium', 'Low'],
            'assignees'  => ['Alice Smith', 'David Kim', 'Engineering Team'],
        ];
    }
};
?>

<div
    x-data="{ open: false }"
    x-on:ticket-modal.window="open = true"
    x-on:close-ticket-modal.window="open = false"
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
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-3">
            <h2 class="text-lg font-semibold text-gray-900">Create Ticket</h2>
            <button type="button" @click="open = false"
                    class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form wire:submit="save" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
            {{-- Responder --}}
            <div>
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">Responder</label>
                <div class="flex flex-wrap gap-2">
                    @foreach ($responders as $option)
                        <button type="button" wire:click="$dispatch('on-select', ['field' => 'responder_id', 'value' => '{{ $option }}'])"
                                @class([
                                    'rounded-full border px-4 py-1.5 text-sm font-medium transition',
                                    'bg-blue-50 text-blue-700 border-blue-300 ring-1 ring-blue-300' => $responder_id === $option,
                                    'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' => $responder_id !== $option,
                                ])>
                            {{ $option }}
                        </button>
                    @endforeach
                </div>
                @error('responder_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Subject --}}
            <div>
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">Subject</label>
                <input type="text" wire:model="subject"
                       placeholder="Brief summary of the issue"
                       class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400">
                @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">Description</label>
                <textarea wire:model="description" rows="4"
                          placeholder="Describe the issue in detail..."
                          class="w-full resize-none rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400"></textarea>
                @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Priority --}}
            <div>
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">Priority</label>
                <div class="flex flex-wrap gap-2">
                    @foreach ($priorities as $option)
                        <button type="button" wire:click="$dispatch('on-select', ['field' => 'priority', 'value' => '{{ $option }}'])"
                                @class([
                                    'rounded-full border px-4 py-1.5 text-sm font-medium transition',
                                    'bg-gray-900 text-white border-gray-900' => $priority === $option,
                                    'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' => $priority !== $option,
                                ])>
                            {{ $option }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Assignee --}}
            <div>
                <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-gray-500">Request Assignee</label>
                <select wire:model="assignee"
                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400">
                    <option value="">Unassigned</option>
                    @foreach ($assignees as $name)
                        <option value="{{ $name }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <div class="flex items-center justify-end gap-3 border-t border-gray-100 px-6 py-3">
            <button type="button" @click="open = false"
                    class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancel
            </button>
            <button type="button" wire:click="$set('subject', 'CLICKED')" wire:loading.attr="disabled"
                    class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-50">
                <span wire:loading.remove wire:target="save">Create Ticket</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        </div>
    </div>
</div>