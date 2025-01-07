<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\Request;

class UrlController extends Controller
{
    public function index()
    {
        // Получаем 10 последних ссылок
        $latestUrls = Url::latest()
            ->take(10)
            ->get();

        return view('urls.index', compact('latestUrls'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'title' => 'nullable|string|max:255',
        ]);

        $url = Url::create([
            'title' => $validated['title'],
            'original_url' => $validated['url'],
            'short_code' => Url::generateUniqueShortCode(),
            'views' => 0,
        ]);

        return back()->with('success', 'Ссылка успешно сокращена: ' . route('url.redirect', $url->short_code));
    }

    public function redirect($shortCode)
    {
        $url = Url::where('short_code', $shortCode)->firstOrFail();
        $url->increment('views');
        
        return redirect($url->original_url);
    }
} 