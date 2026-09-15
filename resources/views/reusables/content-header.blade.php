@extends('components.main.layout')

@section('header')
    <header class="w-full max-w-4xl p-4 bg-white dark:bg-[#1a1a1a] rounded-lg shadow">
        <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#FDFDFC]">
            Main Header
        </h1>
    </header>
@endsection

@section('footer')
    <footer class="w-full max-w-4xl p-4 bg-white dark:bg-[#1a1a1a] rounded-lg shadow">
        <p class="text-center text-[#1b1b18] dark:text-[#FDFDFC]">
            Main Footer
        </p>
    </footer>
@endsection

@section('content')
    <section class="w-[12rem] max-w-4xl p-4 bg-[#1a1a1a]">
        <!-- Your main content here -->
        <p>Main content area</p>
    </section>
@endsection