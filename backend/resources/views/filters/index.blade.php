<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Moje filtry</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-3xl">
        <h1 class="text-2xl font-bold mb-6 text-center">Twoje wyszukiwania</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-6 text-center">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="/my-filters" class="mb-6">
            @csrf
            <div class="flex space-x-4">
                <input type="email" name="email" placeholder="Wpisz swój email" value="{{ $email ?? '' }}" required
                    class="flex-1 block w-full rounded-md border-gray-300 shadow-md p-2">
                <button type="submit"
                    class="py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md shadow">
                    Pokaż filtry
                </button>
            </div>
            <div>
                <a href="/" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Strona główna</a>
            </div>
        </form>

        @if(isset($filters) && count($filters))
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left">Serwis</th>
                            <th class="px-4 py-2 text-left">Kategoria</th>
                            <th class="px-4 py-2 text-left">Cena od</th>
                            <th class="px-4 py-2 text-left">Cena do</th>
                            <th class="px-4 py-2 text-left">Rok od</th>
                            <th class="px-4 py-2 text-left">Rok do</th>
                            <th class="px-4 py-2 text-left">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filters as $filter)
                            <tr class="border-b" id="filter-{{ $filter->id }}">
                                <td class="px-4 py-2">{{ ucfirst($filter->site) }}</td>
                                <td class="px-4 py-2">{{ ucfirst($filter->category) }}</td>
                                <td class="px-4 py-2">{{ $filter->price_from }}</td>
                                <td class="px-4 py-2">{{ $filter->price_to }}</td>
                                <td class="px-4 py-2">{{ $filter->year_from }}</td>
                                <td class="px-4 py-2">{{ $filter->year_to }}</td>
                                <td class="px-4 py-2">
                                    <button type="button"
                                        onclick="deleteFilter({{ $filter->id }})"
                                        class="py-1 px-3 bg-red-500 hover:bg-red-600 text-white rounded-md text-sm">
                                        Usuń
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif(isset($filters))
            <p class="text-center text-gray-600">Brak filtrów dla tego adresu email.</p>
        @endif

    </div>

    <script>
        function deleteFilter(id) {
            if (!confirm('Na pewno usunąć ten filtr?')) {
                return;
            }

            fetch(`/my-filters/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Błąd podczas usuwania filtra.');
                }
                return response.json();
            })
            .then(data => {
                alert('Filtr został usunięty.');
                const row = document.getElementById('filter-' + id);
                if (row) {
                    row.remove();
                }
            })
            .catch(error => {
                alert('Wystąpił błąd: ' + error.message);
            });
        }
    </script>

</body>
</html>
