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
                <input type="email" name="user_email" required
                    class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-md">
            </div>

            <div>
                <label for="parent_category" class="block text-sm font-medium text-gray-700">Kategoria główna</label>
                <select id="parent_category" name="parent_category" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-md p-2">
                    <option value="motoryzacja">Motoryzacja</option>
                    <option value="elektronika">Elektronika</option>
                </select>
            </div>

            <div>
                <label for="site" class="block text-sm font-medium text-gray-700">Serwis</label>
                <select id="site" name="site" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-md p-2">
                    <option value="olx">OLX</option>
                    <option value="otomoto">Otomoto</option>
                </select>
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Kategoria</label>
                <select name="category" required class="mt-1 block w-full rounded-md border-gray-300 shadow-md p-2"
                    id="category">
                    <!-- Motoryzacja categories -->
                    <option value="osobowe" class="motoryzacja">Osobowe</option>
                    <option value="ciezarowe" class="motoryzacja">Ciężarowe</option>
                    <option value="budowlane" class="motoryzacja">Budowlane</option>
                    <option value="dostawcze" class="motoryzacja">Dostawcze</option>
                    <option value="motocykle" class="motoryzacja">Motocykle</option>
                    <option value="przyczepy" class="motoryzacja">Przyczepy</option>
                    <option value="rolnicze" class="rolnicze">Rolnicze</option>
                    <!-- Elektronika categories -->
                    <option value="komputery" class="elektronika">Komputery</option>
                    <option value="telefony" class="elektronika">Telefony</option>
                    <option value="podzespoly" class="elektronika">Podzespoły i części</option>
                </select>
            </div>

            <div id="search-text-wrapper">
                <label for="search_text" class="block text-sm font-medium text-gray-700">Wyszukiwany tekst</label>
                <input type="text" name="search_text"
                    class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-md">
            </div>

            <div class="flex space-x-4">
                <div class="w-1/2">
                    <label for="price_from" class="block text-sm font-medium text-gray-700">Cena od</label>
                    <input type="number" name="price_from" step="0.01"
                        class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-md">
                </div>
                <div class="w-1/2">
                    <label for="price_to" class="block text-sm font-medium text-gray-700">Cena do</label>
                    <input type="number" name="price_to" step="0.01"
                        class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-md">
                </div>
            </div>

            <div id="year-wrapper" class="flex space-x-4">
                <div class="w-1/2">
                    <label for="year_from" class="block text-sm font-medium text-gray-700">Rok od</label>
                    <input type="number" name="year_from"
                        class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-md">
                </div>
                <div class="w-1/2">
                    <label for="year_to" class="block text-sm font-medium text-gray-700">Rok do</label>
                    <input type="number" name="year_to"
                        class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-md">
                </div>
            </div>

            <div>
                <button type="submit"
                    class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md shadow">
                    Zapisz
                </button>
            </div>
            <div>
                <a href="/my-filters" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Zarądzaj
                    filtrami</a>
            </div>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const parentCategorySelect = document.getElementById('parent_category');
        const siteSelect = document.getElementById('site');
        const searchTextWrapper = document.getElementById('search-text-wrapper');
        const categorySelect = document.getElementById('category');
        const yearWrapper = document.getElementById('year-wrapper');
        const otomotoOption = document.querySelector('option[value="otomoto"]');

        function toggleSearchText() {
            if (parentCategorySelect.value === 'elektronika') {
                yearWrapper.style.display = 'none';
            } else {
                yearWrapper.style.display = 'flex';
            }
        }

        function toggleOtomoto() {
            if (parentCategorySelect.value === 'motoryzacja') {
                siteSelect.disabled = false;
                siteSelect.value = 'olx';
                otomotoOption.style.display = 'block';
            } else {
                siteSelect.disabled = false;
                siteSelect.value = 'olx';
                otomotoOption.style.display = 'none';
            }
        }

        function toggleCategories() {
            const motoryzacjaCategories = document.querySelectorAll('.motoryzacja');
            const elektronikaCategories = document.querySelectorAll('.elektronika');
            const rolniczeCategory = document.querySelector('option[value="rolnicze"]');

            if (parentCategorySelect.value === 'elektronika') {
                motoryzacjaCategories.forEach(option => {
                    option.style.display = 'none';
                });
                elektronikaCategories.forEach(option => {
                    option.style.display = 'block';
                });
                categorySelect.value = 'komputery';
            } else if (parentCategorySelect.value === 'motoryzacja') {
                elektronikaCategories.forEach(option => {
                    option.style.display = 'none';
                });
                motoryzacjaCategories.forEach(option => {
                    option.style.display = 'block';
                });
                rolniczeCategory.style.display = siteSelect.value === 'otomoto' ? 'block' : 'none';
                categorySelect.value = 'osobowe';
            }
        }

        parentCategorySelect.addEventListener('change', function () {
            toggleSearchText();
            toggleOtomoto();
            toggleCategories();
        });

        siteSelect.addEventListener('change', function () {
            toggleCategories();
        });

        toggleSearchText();
        toggleOtomoto();
        toggleCategories();
    });
</script>
</body>

</html>