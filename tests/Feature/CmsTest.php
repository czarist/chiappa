<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $this->get('/')->assertOk()->assertSee('Performance e Confiança');
        $this->get('/api/v1/pages')->assertOk()->assertJsonPath('success', true);
        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml');
    }

    public function test_admin_login_and_page_editor_are_protected(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('<title>Entrar | Chiappa CMS</title>', false)
            ->assertSee('assets/favicon.svg', false)
            ->assertSee('<form', false);
        $this->get('/admin')->assertRedirect('/login');
        $token = csrf_token();
        $this->withSession(['_token' => $token])
            ->post('/login', ['_token' => $token, 'email' => 'admin@example.com', 'password' => 'change-me'])
            ->assertRedirect('/admin')
            ->assertHeader('Location', '/admin');
        $this->actingAs(User::where('email', 'admin@example.com')->first())->get('/admin/pages')->assertOk()->assertSee('Páginas');
    }
}
