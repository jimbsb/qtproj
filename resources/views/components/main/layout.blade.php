<main
    style=""
    class="flex-1 py-2 px-4 overflow-auto bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex items-center flex-col h-full"
>
    @yield('header','Default Header Content')
    @yield('content','Default Content')
    {{-- @yield('footer','Default Footer Content') --}}
</main>