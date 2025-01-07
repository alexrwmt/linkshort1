<?php

namespace Tests\Feature;

use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('urls.index');
    }

    public function test_can_create_short_url(): void
    {
        $response = $this->post('/urls', [
            'url' => 'https://example.com',
            'title' => 'Test URL'
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/');

        $this->assertDatabaseHas('urls', [
            'original_url' => 'https://example.com',
            'title' => 'Test URL'
        ]);

        $url = Url::first();
        $this->assertNotNull($url->short_code);
        $this->assertEquals(0, $url->views);
    }

    public function test_url_validation(): void
    {
        $response = $this->post('/urls', [
            'url' => 'not-a-url',
            'title' => 'Test URL'
        ]);

        $response->assertSessionHasErrors('url');
    }

    public function test_redirect_works_and_increments_views(): void
    {
        // Создаем тестовую ссылку
        $url = Url::create([
            'original_url' => 'https://example.com',
            'short_code' => 'test123',
            'views' => 0
        ]);

        // Проверяем редирект
        $response = $this->get('/~test123');
        $response->assertRedirect('https://example.com');

        // Проверяем увеличение счетчика просмотров
        $this->assertEquals(1, $url->fresh()->views);
    }

    public function test_invalid_short_code_returns_404(): void
    {
        $response = $this->get('/~nonexistent');
        $response->assertNotFound();
    }

    // public function test_latest_urls_shown_on_homepage(): void
    // {
    //     // Создаем несколько тестовых ссылок
    //     $urls = [];
    //     for ($i = 1; $i <= 12; $i++) {
    //         $urls[] = Url::create([
    //             'original_url' => "https://example{$i}.com",
    //             'short_code' => "test{$i}",
    //             'title' => "Test URL {$i}",
    //             'views' => $i
    //         ]);
    //     }

    //     $response = $this->get('/');

    //     // Проверяем, что отображаются только последние 10 ссылок
    //     $response->assertViewHas('latestUrls', function ($latestUrls) {
    //         return $latestUrls->count() === 10;
    //     });

    //     // Проверяем, что ссылки отсортированы по дате создания (последние первые)
    //     $response->assertSee('Test URL 12');
    //     $response->assertSee('Test URL 3');
    //     $response->assertDontSee('Test URL 1');
    // }

    public function test_unique_short_code_generation(): void
    {
        // Создаем первую ссылку
        $url1 = Url::create([
            'original_url' => 'https://example1.com',
            'short_code' => 'abc123',
            'views' => 0
        ]);

        // Создаем вторую ссылку и проверяем, что код уникальный
        $url2 = Url::create([
            'original_url' => 'https://example2.com',
            'short_code' => Url::generateUniqueShortCode(),
            'views' => 0
        ]);

        $this->assertNotEquals($url1->short_code, $url2->short_code);
    }
} 