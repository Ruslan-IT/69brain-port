<?php

namespace Tests\Feature;

use App\Filament\Resources\ServiceResource\Pages\CreateService;
use App\Filament\Resources\ServiceResource\Pages\EditService;
use App\Models\HomePage;
use App\Models\Service;
use App\Models\SiteOption;
use App\Models\User;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CmsContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_the_admin_panel(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/glavnaya')->assertRedirect('/admin/login');
    }

    public function test_admin_pages_render_for_an_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['password_hash_web' => $user->getAuthPassword()])
            ->get('/admin/glavnaya')
            ->assertOk()
            ->assertSee('Главная страница', false);

        $this->actingAs($user)
            ->withSession(['password_hash_web' => $user->getAuthPassword()])
            ->get('/admin/napravleniya')
            ->assertOk();

        $this->actingAs($user)
            ->withSession(['password_hash_web' => $user->getAuthPassword()])
            ->get('/admin/kontakty')
            ->assertOk();

        $this->actingAs($user)
            ->withSession(['password_hash_web' => $user->getAuthPassword()])
            ->get('/admin/nastroyki')
            ->assertOk();

        $service = Service::query()->where('key', 'strength')->firstOrFail();

        $edit = $this->actingAs($user)
            ->withSession(['password_hash_web' => $user->getAuthPassword()])
            ->get('/admin/napravleniya/'.$service->id.'/edit');

        $edit->assertOk()
            ->assertSee('Расчёт элементов БПЛА', false)
            ->assertSee('fi-body-has-sidebar-fully-collapsible-on-desktop', false)
            ->assertSee('fi-width-full', false);

        $html = $edit->getContent();
        $contentAt = strpos($html, 'Содержание');
        $seoAt = strpos($html, 'SEO-настройки');

        $this->assertNotFalse($contentAt);
        $this->assertNotFalse($seoAt);
        $this->assertLessThan($seoAt, $contentAt);
        $this->assertMatchesRegularExpression('/isCollapsed:\s*true[\s\S]{0,4000}SEO-настройки/', $html);
    }

    public function test_public_content_follows_the_saved_records(): void
    {
        $home = HomePage::query()->firstOrFail();
        $home->update([
            'h1' => 'Проверочный заголовок',
            'who_text' => 'Новая команда инженеров.',
            'seo_title' => 'Особый заголовок',
            'seo_description' => 'Особое описание',
            'works' => array_merge($home->works, [[
                'title' => 'Проверочная работа',
                'body' => '<p>Новый текст блока.</p><script>alert(1)</script>',
                'link_label' => 'Скрытая ссылка',
                'link_url' => 'javascript:alert(1)',
            ]]),
        ]);

        $response = $this->get('/');
        $response->assertOk()
            ->assertSee('<h1>Проверочный заголовок</h1>', false)
            ->assertSee('<title>Особый заголовок</title>', false)
            ->assertSee('Особое описание', false)
            ->assertSee('Новая команда инженеров.', false)
            ->assertSee('Проверочная работа', false)
            ->assertSee('Новый текст блока.', false)
            ->assertDontSee('<script>', false)
            ->assertDontSee('javascript:', false);
        $this->assertSame(1, substr_count($response->getContent(), '<h1'));

        $options = SiteOption::query()->firstOrFail();
        $options->update([
            'phone' => '+7 (900) 111-22-33',
            'email' => 'desk@example.ru',
            'footer_links' => [
                ['label' => 'Карта сайта', 'url' => '/'],
            ],
            'copyright' => 'КБ «Прочность»',
        ]);

        $this->get('/')
            ->assertSee('+7 (900) 111-22-33', false)
            ->assertSee('desk@example.ru', false)
            ->assertSee('Карта сайта', false);

        $this->get('/kontakty')
            ->assertSee('+7 (900) 111-22-33', false)
            ->assertSee('desk@example.ru', false)
            ->assertDontSee('Не указан', false);

        $strength = Service::query()->where('key', 'strength')->firstOrFail();
        $blocks = $strength->blocks;
        $blocks[] = [
            'title' => 'Новый расчётный блок',
            'body' => '<p>Дополнительный текст блока.</p>',
            'extra' => null,
            'link_label' => null,
            'link_url' => null,
            'icon' => null,
            'anchor' => 'novyj-blok',
            'nav' => null,
        ];
        $strength->update([
            'h1' => 'Новый заголовок прочности',
            'seo_title' => 'SEO прочности',
            'seo_description' => 'Описание прочности',
            'blocks' => $blocks,
        ]);

        $this->get('/raschety-na-prochnost')
            ->assertOk()
            ->assertSee('<h1>Новый заголовок прочности</h1>', false)
            ->assertSee('<title>SEO прочности</title>', false)
            ->assertSee('Описание прочности', false)
            ->assertSee('Новый расчётный блок', false)
            ->assertSee('Дополнительный текст блока.', false);

        Service::query()->create([
            'title' => 'Проверочное направление',
            'h1' => 'Проверочное направление',
            'slug' => 'proverka-napravleniya',
            'excerpt' => 'Короткое описание нового направления.',
            'lead' => 'Текст новой страницы.',
            'blocks' => [],
            'is_published' => true,
            'card_link_label' => 'Подробнее',
        ]);

        $this->get('/proverka-napravleniya')
            ->assertOk()
            ->assertSee('Текст новой страницы.', false)
            ->assertDontSee('three.module', false);

        $this->get('/')
            ->assertSee('Проверочное направление', false)
            ->assertSee('Короткое описание нового направления.', false);

        Service::query()->where('key', 'statistics')->update(['is_published' => false]);

        $this->get('/statisticheskie-raschety')->assertNotFound();
        $this->get('/')->assertDontSee('Качество данных, гипотезы, регрессия, ANOVA', false);

        $this->get('/raschety-na-prochnost')->assertOk();
        $this->get('/tehnologicheskie-raschety')->assertOk();
    }

    public function test_visualization_switch_still_controls_the_scripts(): void
    {
        SiteSettings::put(SiteSettings::VISUALIZATION, 'static');

        $home = $this->get('/')->assertOk();
        $home->assertSee('images/viz/home-original.png', false);
        $home->assertDontSee('beam-analysis.js', false);
        $home->assertDontSee('three.module', false);
        $home->assertDontSee('gsap.min.js', false);
        $home->assertDontSee('<canvas', false);

        $strength = $this->get('/raschety-na-prochnost')->assertOk();
        $strength->assertSee('images/viz/strength.png', false);
        $strength->assertDontSee('js/scenes/strength.js', false);
        $strength->assertDontSee('<canvas', false);

        SiteSettings::put(SiteSettings::HOMEPAGE, 'new');
        SiteSettings::put(SiteSettings::VISUALIZATION, 'animated');

        $this->get('/')
            ->assertOk()
            ->assertSee('js/scenes/home.js', false)
            ->assertSee('data-scene="home"', false)
            ->assertDontSee('beam-analysis.js', false);
    }

    public function test_renaming_a_direction_stays_on_the_same_edit_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['password_hash_web' => $user->getAuthPassword()]);

        $keyed = Service::query()->where('key', 'strength')->firstOrFail();
        $custom = Service::query()->create([
            'title' => 'Отдельное направление',
            'slug' => 'otdelnoe-napravlenie',
            'h1' => 'Отдельное направление',
            'is_published' => true,
        ]);

        foreach ([
            [$keyed, 'Новое название прочности', 'raschety-na-prochnost', '/raschety-na-prochnost'],
            [$custom, 'Другое отдельное направление', 'otdelnoe-napravlenie', '/otdelnoe-napravlenie'],
        ] as [$service, $title, $slug, $path]) {
            Livewire::test(EditService::class, ['record' => $service->id])
                ->set('data.title', $title)
                ->assertSet('data.slug', $slug)
                ->call('save')
                ->assertHasNoFormErrors();

            $service->refresh();

            $this->assertSame($title, $service->title);
            $this->assertSame($slug, $service->slug);
            $this->get('/admin/napravleniya/'.$service->id.'/edit')->assertOk();
            $this->get($path)->assertOk();
        }

        Livewire::test(EditService::class, ['record' => $custom->id])
            ->set('data.slug', 'Не адрес')
            ->call('save')
            ->assertHasFormErrors(['slug']);
    }

    public function test_create_form_builds_the_address_from_the_title_until_it_is_edited(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['password_hash_web' => $user->getAuthPassword()]);

        $title = 'Лазерная резка листа';

        Livewire::test(CreateService::class)
            ->set('data.title', $title)
            ->assertSet('data.slug', Service::uniqueSlug($title))
            ->set('data.slug', 'svoi-adres')
            ->set('data.title', 'Другое название')
            ->assertSet('data.slug', 'svoi-adres');
    }

    public function test_new_direction_slug_does_not_repeat_an_existing_address(): void
    {
        $slug = Service::uniqueSlug('Расчёты на прочность и жёсткость');

        $this->assertNotSame('raschety-na-prochnost', $slug);
        $this->assertNotContains($slug, ['admin', 'enb', 'kontakty']);
    }
}
