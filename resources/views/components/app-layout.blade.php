@props(['title' => null])
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title . ' | Chemsphere' : 'Chemsphere' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen lg:flex">
    <aside class="hidden w-60 shrink-0 flex-col border-r border-line bg-white lg:sticky lg:top-0 lg:flex lg:h-screen">
        @include('partials.nav')
    </aside>

    <header class="border-b border-line bg-white lg:hidden">
        <details class="group">
            <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-3 font-semibold">
                Chemsphere
                <span class="text-sm font-medium text-muted group-open:hidden">Menu</span>
                <span class="hidden text-sm font-medium text-muted group-open:inline">Close</span>
            </summary>
            <div class="flex flex-col border-t border-line">@include('partials.nav')</div>
        </details>
    </header>

    <main class="min-w-0 flex-1 px-4 py-6 sm:px-8 lg:py-10">
        <div class="mx-auto max-w-6xl">
            @if ($title)
                <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                    <h1 class="text-2xl font-semibold tracking-tight">{{ $title }}</h1>
                    {{ $actions ?? '' }}
                </div>
            @endif

            <x-flash />
            {{ $slot }}
        </div>
    </main>
</body>

</html>
