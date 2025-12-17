<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analysis - {{ $dataset->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 p-5 md:p-10 text-gray-800">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold italic">{{ $dataset->name }}</h1>
            <a href="{{ route('dashboard') }}" class="bg-gray-800 text-white px-6 py-2 rounded-lg font-bold">Dashboard</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
            <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-md border-t-4 border-blue-500">
                <h2 class="text-lg font-bold mb-4 uppercase tracking-wider text-blue-600">Statistics Summary</h2>
                <div class="space-y-4">
                    @foreach($stats as $col => $val)
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <p class="font-bold border-b border-blue-200 mb-2">{{ $col }}</p>
                        <div class="grid grid-cols-2 text-sm">
                            <span>Mean: <b class="text-blue-700">{{ number_format($val['mean'], 2) }}</b></span>
                            <span>Max: <b class="text-green-600">{{ $val['max'] }}</b></span>
                            <span>Min: <b class="text-red-600">{{ $val['min'] }}</b></span>
                            <span>Count: <b>{{ $val['count'] }}</b></span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-md border-t-4 border-purple-500">
                <h2 class="text-lg font-bold mb-4 uppercase tracking-wider text-purple-600">Numerical Distribution</h2>
                <canvas id="statChart" height="150"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-gray-800 overflow-x-auto">
            <h2 class="text-lg font-bold mb-4 uppercase tracking-wider">Data Preview (50 Rows)</h2>
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        @foreach($header as $h)
                        <th class="border p-2">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($previewRows as $r)
                    <tr class="hover:bg-gray-50">
                        @foreach($r as $cell)
                        <td class="border p-2">{{ $cell }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        new Chart(document.getElementById('statChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($stats)) !!},
                datasets: [{
                    label: 'Average Value',
                    data: {!! json_encode(array_values(array_column($stats, 'mean'))) !!},
                    backgroundColor: 'rgba(99, 102, 241, 0.5)',
                    borderColor: 'rgb(99, 102, 241)',
                    borderWidth: 1
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    </script>
</body>
</html>