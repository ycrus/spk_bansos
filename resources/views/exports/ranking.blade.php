<!DOCTYPE html>
<html>
<head>
    <title>Ranking PDF</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; }
    </style>
</head>
<body>
    <h2>Data Rangking</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Ranking</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->penerima->nama ?? '-' }}</td>
                    <td>{{ $item->ranking }}</td>
                    <td>{{ $item->total }}</td>
                    <td>{{ $item->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
