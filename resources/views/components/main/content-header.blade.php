@extends('components.main.layout')

@section('header')
    <div class="header w-full !pt-0 bg-white text-[#6b7280] dark:bg-[#1a1a1a] text-[#6b7280] dark:text-[#a1a1aa] shadow-bottom flex flex-inline">
        <span class="text-xs line-clamp-1">Tickets</span>
        <span class="text-xs line-clamp-1 px-2">/</span>
        <span class="text-xs line-clamp-1">Create Ticket</span>
    </div>
@endsection

@section('footer')
    <div class="footer w-full bg-white dark:bg-[#1a1a1a] shadow">
        <p class="text-center text-[#1b1b18] dark:text-[#FDFDFC]">
            Main Footer
        </p>
    </div>
@endsection

@section('content')
    <div class="content w-full bg-white dark:bg-[#1a1a1a] scrollable" style="height:-webkit-fill-available;">
        <!-- Your main content here -->
        <p>Main content area</p>
    </div>
@endsection