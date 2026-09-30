@props(['title' => null, 'heading' => null])
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

<body class="flex min-h-screen items-center justify-center px-4 py-10">
    <main class="w-full max-w-sm">
        <a href="{{ route('welcome') }}" class="mb-6 flex items-center gap-2 font-semibold">
            <svg class="size-6 text-reagent-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 3h6M10 3v6l-5 9a2 2 0 0 0 1.8 3h10.4A2 2 0 0 0 19 18l-5-9V3M7.5 15h9" /></svg>
            Chemsphere
        </a>
        @if ($heading)<h1 class="mb-6 text-2xl font-semibold tracking-tight">{{ $heading }}</h1>@endif
        <x-flash />
        <div class="card p-6">{{ $slot }}</div>
    </main>
</body>

</html>
