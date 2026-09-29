@if (session('success'))
<div role="status" style="color: green;">{{ session('success') }}</div>
@endif

@if (session('info'))
<div role="status" style="color: dimgray;">{{ session('info') }}</div>
@endif

@if (session('error'))
<div role="alert" style="color: red;">{{ session('error') }}</div>
@endif

@if ($errors->any())
<div role="alert" style="color: red;">
    <ul>
        @foreach ($errors->all() as $message)
        <li>{{ $message }}</li>
        @endforeach
    </ul>
</div>
@endif
