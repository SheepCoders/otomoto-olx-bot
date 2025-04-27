<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dodaj wyszukiwanie</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Dodaj wyszukiwanie</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4 text-center">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('filters.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="user_email" class="block text-sm font-medium text-gray-700">E-mail</label>
                <input type="email" name="user_email" required class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-md">
            </div>

            <div>
                <label for="site" class="block text-sm font-medium text-gray-700">Serwis</label>
                <select name="site" required class="mt-1 block w-full rounded-md border-gray-300 shadow-md p-2">
                    <option value="olx">OLX</option>
                    <option value="otomoto">Otomoto</option>
                </select>
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Kategoria</label>
                <select name="category" required class="mt-1 block w-full rounded-md border-gray-300 shadow-md p-2">
                    <option value="ciezarowe">Ciężarowe</option>
                    <option value="budowlane">Budowlane</option>
                </select>
            </div>

            <div class="flex space-x-4">
                <div class="w-1/2">
                    <label for="price_from" class="block text-sm font-medium text-gray-700">Cena od</label>
                    <input type="number" name="price_from" step="0.01" class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-md">
                </div>
                <div class="w-1/2">
                    <label for="price_to" class="block text-sm font-medium text-gray-700">Cena do</label>
                    <input type="number" name="price_to" step="0.01" class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-md">
                </div>
            </div>

            <div class="flex space-x-4">
                <div class="w-1/2">
                    <label for="year_from" class="block text-sm font-medium text-gray-700">Rok od</label>
                    <input type="number" name="year_from" class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-md">
                </div>
                <div class="w-1/2">
                    <label for="year_to" class="block text-sm font-medium text-gray-700">Rok do</label>
                    <input type="number" name="year_to" class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-md">
                </div>
            </div>

            <div>
                <button type="submit" class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md shadow">
                    Zapisz
                </button>
            </div>
            <div>
                <a href="/my-filters" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Zarądzaj filtrami</a>
            </div>
        </form>
    </div>
</body>
</html>
