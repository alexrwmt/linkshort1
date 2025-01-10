<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сокращение ссылок</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8 text-center">Shortiki.URL</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('url.store') }}" method="POST" class="max-w-lg mx-auto">
            @csrf
            <div class="mb-4">
                <label for="url" class="block text-gray-700 font-bold mb-2">URL для сокращения</label>
                <input type="url" name="url" id="url" required
                    class="w-full px-3 py-2 border rounded-lg @error('url') border-red-500 @enderror"
                    placeholder="https://example.com">
                @error('url')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="title" class="block text-gray-700 font-bold mb-2">Название (необязательно)</label>
                <input type="text" name="title" id="title"
                    class="w-full px-3 py-2 border rounded-lg @error('title') border-red-500 @enderror"
                    placeholder="Моя ссылка">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
                Сократить
            </button>
        </form>

        <!-- Добавляем таблицу с последними ссылками -->
        @if($latestUrls->isNotEmpty())
            <div class="mt-12">
                <h2 class="text-2xl font-bold mb-4">Последние сокращенные ссылки</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg overflow-hidden">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">Название</th>
                                <th class="px-4 py-2 text-left">Оригинальная ссылка</th>
                                <th class="px-4 py-2 text-left">Короткая ссылка</th>
                                <th class="px-4 py-2 text-center">Просмотры</th>
                                <th class="px-4 py-2 text-left">Дата создания</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($latestUrls as $url)
                                <tr class="border-t">
                                    <td class="px-4 py-2">
                                        {{ $url->title ?: 'Без названия' }}
                                    </td>
                                    <td class="px-4 py-2">
                                        <a href="{{ $url->original_url }}" 
                                           class="text-blue-500 hover:underline"
                                           target="_blank"
                                           title="{{ $url->original_url }}">
                                            {{ Str::limit($url->original_url, 50) }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('url.redirect', $url->short_code) }}" 
                                           class="text-blue-500 hover:underline"
                                           target="_blank">
                                            {{ route('url.redirect', $url->short_code) }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $url->views }}
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ $url->created_at->format('d.m.Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</body>
</html> 