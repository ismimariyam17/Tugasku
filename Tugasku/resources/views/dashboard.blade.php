<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DS Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-5 md:p-10">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow-lg">
        <h1 class="text-3xl font-extrabold mb-6 text-gray-800">Data Science Center</h1>

        @if(session('success'))
            <div class="bg-green-500 text-white p-4 rounded-lg mb-6 shadow-md">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('datasets.store') }}" method="POST" enctype="multipart/form-data" class="bg-gray-50 p-6 rounded-lg border-2 border-dashed border-gray-300 mb-10">
            @csrf
            <div class="flex flex-col md:flex-row gap-4 items-center">
                <input type="file" name="file_csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-8 py-2 rounded-full font-bold hover:bg-blue-700 transition">Upload CSV</button>
            </div>
        </form>

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Your Datasets</h2>
            <form action="{{ route('dashboard') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" placeholder="Cari file..." class="border rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-gray-200 px-3 py-1 rounded-lg text-sm hover:bg-gray-300">Cari</button>
            </form>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="w-full text-left">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="p-4">Filename</th>
                        <th class="p-4 text-center">Rows</th>
                        <th class="p-4 text-center">Cols</th>
                        <th class="p-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($datasets as $dataset)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4 font-medium">{{ $dataset->name }}</td>
                        <td class="p-4 text-center text-gray-600">{{ number_format($dataset->total_rows) }}</td>
                        <td class="p-4 text-center text-gray-600">{{ $dataset->total_columns }}</td>
                        <td class="p-4 flex justify-center gap-3">
                            <a href="{{ route('datasets.show', $dataset->id) }}" class="text-blue-600 font-bold hover:underline">Analyze</a>
                            <a href="{{ route('datasets.download', $dataset->id) }}" class="text-green-600 font-bold hover:underline">Export</a>
                            <form action="{{ route('datasets.destroy', $dataset->id) }}" method="POST" onsubmit="return confirm('Hapus data?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 font-bold hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>