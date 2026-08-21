<?php

namespace Tests\Feature;

use App\Models\{Page, User};
use App\Services\SiteCache;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CmsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_public_home_and_seo_endpoints_are_available(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Performance e Confiança')
            ->assertSee('<meta name="description"', false)
            ->assertSee('<meta property="og:type" content="website">', false)
            ->assertSee('<meta property="og:locale" content="pt_BR">', false)
            ->assertSee('twitter:card', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('AutoPartsStore', false)
            ->assertSee('name="keywords"', false);
        $this->get('/api/v1/pages')->assertOk()->assertJsonPath('success', true);
        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml');
    }

    public function test_page_seo_fields_are_saved_and_rendered(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        $page = Page::where('slug', 'inicio')->first();
        $this->actingAs($user)->put(route('admin.pages.update', $page), [
            'title' => $page->title,
            'slug' => $page->slug,
            'status' => 'published',
            'template' => $page->template ?: 'default',
            'sections_json' => json_encode($page->sections->map(fn ($section) => [
                'type' => $section->type,
                'active' => $section->active,
                'content' => $section->content,
                'settings' => $section->settings,
            ])->values()),
            'seo' => [
                'title' => 'SEO Chiappa Teste',
                'description' => 'Descrição exclusiva para busca.',
                'keywords' => 'freio, suspensão',
                'og_title' => 'OG Chiappa',
                'og_description' => 'Compartilhe a oficina.',
                'twitter_card' => 'summary_large_image',
                'indexable' => '1',
                'followable' => '1',
            ],
        ])->assertRedirect();

        $this->get('/')
            ->assertOk()
            ->assertSee('<title>SEO Chiappa Teste</title>', false)
            ->assertSee('Descrição exclusiva para busca.', false)
            ->assertSee('content="OG Chiappa"', false)
            ->assertSee('freio, suspensão', false);
    }

    public function test_admin_login_and_page_editor_are_protected(): void
    {
        $this->get('/chiappa-admin')
            ->assertOk()
            ->assertSee('<title>Entrar | Chiappa CMS</title>', false)
            ->assertSee('assets/favicon.svg', false)
            ->assertSee('<form', false);
        $this->get('/admin')->assertRedirect('/chiappa-admin');
        $token = csrf_token();
        $this->withSession(['_token' => $token])
            ->post('/chiappa-admin', ['_token' => $token, 'email' => 'admin@example.com', 'password' => 'change-me'])
            ->assertRedirect('/admin')
            ->assertHeader('Location', '/admin');
        $this->actingAs(User::where('email', 'admin@example.com')->first())->get('/admin/pages')->assertOk()->assertSee('Páginas');
    }

    public function test_authenticated_users_are_sent_to_admin_not_home(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        $this->actingAs($user)->get('/chiappa-admin')->assertRedirect('/admin');
    }

    public function test_login_ignores_public_intended_url(): void
    {
        $token = csrf_token();
        $this->withSession(['_token' => $token, 'url.intended' => '/'])
            ->post('/chiappa-admin', ['_token' => $token, 'email' => 'admin@example.com', 'password' => 'change-me'])
            ->assertRedirect('/admin')
            ->assertHeader('Location', '/admin');
    }

    public function test_users_are_managed_on_dedicated_pages(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $this->actingAs($admin)->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Novo usuário')
            ->assertSee('Administrador')
            ->assertDontSee('Salvar', false);
        $this->actingAs($admin)->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee('Criar usuário');
        $this->actingAs($admin)->get(route('admin.users.edit', $admin))
            ->assertOk()
            ->assertSee('Salvar alterações');
    }

    public function test_saving_a_page_busts_public_cache(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        $page = Page::where('slug', 'inicio')->first();
        app(SiteCache::class)->page('inicio');
        $this->actingAs($user)->put(route('admin.pages.update', $page), [
            'title' => $page->title,
            'slug' => $page->slug,
            'status' => 'published',
            'template' => $page->template ?: 'default',
            'sections_json' => json_encode([[
                'type' => 'hero',
                'active' => true,
                'content' => ['title' => 'Cache atualizado agora', 'text' => 'ok', 'image' => '/assets/hero.jpg'],
                'settings' => [],
            ]]),
        ])->assertRedirect();
        $this->get('/')->assertOk()->assertSee('Cache atualizado agora');
    }

    public function test_page_editor_shows_image_upload_and_icon_preview(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        $page = Page::where('slug', 'inicio')->first();
        $this->actingAs($user)
            ->get(route('admin.pages.edit', $page))
            ->assertOk()
            ->assertSee('image-picker', false)
            ->assertSee('Enviar', false)
            ->assertSee('icon-field-preview', false);
    }

    public function test_admin_can_upload_image_from_editor(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        $file = UploadedFile::fake()->image('pastilha.jpg', 20, 20);
        $response = $this->actingAs($user)
            ->postJson(route('admin.media.store'), ['file' => $file])
            ->assertOk()
            ->assertJsonPath('success', true);
        $url = $response->json('data.url');
        $this->assertNotEmpty($url);
        $this->assertFileExists(public_path(ltrim($url, '/')));
        @unlink(public_path(ltrim($url, '/')));
    }

    public function test_saving_a_page_downloads_remote_images(): void
    {
        Http::fake([
            'https://cdn.example.com/hero.png' => Http::response(
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='),
                200,
                ['Content-Type' => 'image/png']
            ),
        ]);
        $user = User::where('email', 'admin@example.com')->first();
        $page = Page::where('slug', 'inicio')->first();
        $this->actingAs($user)->put(route('admin.pages.update', $page), [
            'title' => $page->title,
            'slug' => $page->slug,
            'status' => $page->status,
            'template' => $page->template ?: 'default',
            'sections_json' => json_encode([[
                'type' => 'hero',
                'active' => true,
                'content' => ['title' => 'Hero local', 'image' => 'https://cdn.example.com/hero.png'],
                'settings' => [],
            ]]),
        ])->assertRedirect();
        $image = $page->fresh()->sections()->first()->content['image'];
        $this->assertStringStartsWith('/assets/uploads/', $image);
        $this->assertFileExists(public_path(ltrim($image, '/')));
        @unlink(public_path(ltrim($image, '/')));
    }
}
