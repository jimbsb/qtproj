@extends('components.main.layout')

@php
    $headers = [
        ['name' => 'Ticket ID', 'field' => 'ticket_id'],
        ['name' => 'Subject',   'field' => 'subject'],
        ['name' => 'Status',    'field' => 'status', 'type' => 'status'],
        ['name' => 'Priority',  'field' => 'priority'],
        ['name' => 'Assignee',  'field' => 'assignee', 'type' => 'status'],
        ['name' => 'Updated',   'field' => 'updated'],
    ];

    $data = [
        [
            'ticket_id' => 'TK-1024',
            'subject'   => 'Cannot access dashboard after password reset',
            'status'    => 'open',
            'priority'  => 'High',
            'assignee'  => 'Alice Smith',
            'updated'   => '2 mins ago',
        ],
        [
            'ticket_id' => 'TK-1023',
            'subject'   => 'Submitted by Michael Chen',
            'status'    => 'in_progress',
            'priority'  => 'Medium',
            'assignee'  => ['value' => 'David Kim', 'status' => '!bg-amber-200'],
            'updated'   => '1 hour ago',
        ],
        [
            'ticket_id' => 'TK-1022',
            'subject'   => 'Submitted by Emily R.',
            'status'    => 'resolved',
            'priority'  => 'Low',
            'assignee'  => ['value' => 'Unassigned', 'status' => ''],
            'updated'   => 'Yesterday',
        ],
        [
            'ticket_id' => 'TK-1021',
            'subject'   => 'Submitted by System Alert',
            'status'    => 'open',
            'priority'  => 'High',
            'assignee'  => '-',
            'updated'   => '5 days ago',
        ],
    ];
@endphp

@section('header')
    <div class="header w-full p-0 pb-1 flex justify-between items-center">
        <div class="flex flex-col ">
            <span class="text-lg md:text-2xl font-bold">Services Management</span>
            <span class="text-[#6b7280] text-xs font-normal">List and creation of service requests</span>
        </div>
        <button class="bg-[#2563EB] text-[#FDFDFC] py-0 shadow dark:hover:bg-[#2a2a2a]" onclick="window.dispatchEvent(new CustomEvent('open-ticket-modal'))">
            <span class="hidden sm:block " >+ Create Ticket</span>
            <span class="sm:hidden text-nowrap ">+ Ticket</span>
        </button>
    </div>
    {{-- <div class="header w-full !pt-0 bg-white text-[#6b7280] dark:bg-[#1a1a1a] text-[#6b7280] dark:text-[#a1a1aa] shadow-bottom flex flex-inline">
        <span class="text-xs line-clamp-1">Tickets</span>
        <span class="text-xs line-clamp-1 px-2">/</span>
        <span class="text-xs line-clamp-1">Create Ticket</span>
    </div> --}}
@endsection

@section('footer')
    <div class="footer w-full bg-white dark:bg-[#1a1a1a] shadow">
        <p class="text-center text-[#1b1b18] dark:text-[#FDFDFC]">
            Main Footer
        </p>
    </div>
@endsection

@section('content')
    <div class="content w-full py-2 bg-white dark:bg-[#1a1a1a] scrollable relative" style="height:-webkit-fill-available; max-height: calc(100dvh - 90px);">
        <div class="w-full flex flex-row gap-4 mb-2">
            <input type="text" placeholder="Search ticket id, requestor, respondent..."
                class="search w-full dark:bg-[#0a0a0a] dark:text-[#FDFDFC] py-1">
            <button class="text-sm shadow dark:hover:bg-[#2a2a2a] grid gap-2 grid-flow-col content-center items-center">
                <img src="/icons/filter.svg" color alt="" class="brightness-0 invert max-w-3 hidden md:inline-flex">
                <span>Filter</span>
            </button>
        </div>
            @include('reusables.table',[$headers, $data, $collection_of = 'Tickets'])
        {{-- <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Example ticket cards -->
            <div
                class="ticket-card p-4 bg-[#FDFDFC] dark:bg-[#1a1a1a] rounded-lg shadow hover:shadow-lg transition duration-300">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#FDFDFC]">Ticket #12345</h2>
                <p class="text-sm text-[#6b7280] dark:text-[#a1a1aa]">Issue: Unable to login</p>
                <p class="text-sm text-[#6b7280] dark:text-[#a1a1aa]">Status: Open</p>
            </div>
            <div
                class="ticket-card p-4 bg-[#FDFDFC] dark:bg-[#1a1a1a] rounded-lg shadow hover:shadow-lg transition duration-300">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#FDFDFC]">Ticket #12346</h2>
                <p class="text-sm text-[#6b7280] dark:text-[#a1a1aa]">Issue: Error on checkout</p>
                <p class="text-sm text-[#6b7280] dark:text-[#a1a1aa]">Status: In Progress</p>
            </div>
            <!-- Add more ticket cards as needed -->
        </div> --}}
    </div>
@endsection
