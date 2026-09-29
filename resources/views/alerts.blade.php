<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Alerts</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <h1>Alerts</h1>

    <x-flash />

    <div id="alerts-table">
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Chemical</th>
                    <th>Message</th>
                    <th>Received</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $alert)
                <tr @if (!$alert->read_at) style="font-weight: bold;" @endif>
                    <td>{{ $alert->type?->name }}</td>
                    <td>{{ $alert->chemical?->chemical_name }}</td>
                    <td>{{ $alert->message }}</td>
                    <td>{{ $alert->created_at->diffForHumans() }}</td>
                    <td>
                        @if ($alert->read_at)
                        Read {{ $alert->read_at->diffForHumans() }}
                        @else
                        <form action="{{ route('alerts.read', $alert->alert_id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit">Mark as read</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $data->links('pagination::bootstrap-5') }}
    </div>

    <br>
    <a href="{{ route('welcome') }}">Return</a>
</body>

</html>
