<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dataset</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-4">Upload Dataset CSV</h1>

        {{-- Menampilkan Pesan Sukses --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Menampilkan Error Validasi --}}
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Upload --}}
        <form action="{{ route('datasets.store') }}" method="POST" enctype="multipart/form-data" class="mb-8">
            @csrf
            <div class="flex gap-4">
                <input type="file" name="file_csv" class="border p-2 rounded w-full" accept=".csv,.txt" required>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Upload</button>
            </div>
        </form>

        <h2 class="text-xl font-semibold mb-2">Daftar Dataset</h2>
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 p-2">Nama File</th>
                    <th class="border border-gray-300 p-2">Total Baris</th>
                    <th class="border border-gray-300 p-2">Total Kolom</th>
                    <th class="border border-gray-300 p-2">Tanggal Upload</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datasets as $dataset)
                <tr>
                    <td class="border border-gray-300 p-2">{{ $dataset->name }}</td>
                    <td class="border border-gray-300 p-2 text-center">{{ $dataset->total_rows }}</td>
                    <td class="border border-gray-300 p-2 text-center">{{ $dataset->total_columns }}</td>
                    <td class="border border-gray-300 p-2 text-center">{{ $dataset->created_at->format('d M Y H:i') }}</td>
                </tr>
                @endforeach
                @if($datasets->isEmpty())
                <tr><td colspan="4" class="text-center p-4 text-gray-500">Belum ada dataset.</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>