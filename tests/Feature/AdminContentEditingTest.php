<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageContacts;
use App\Filament\Pages\ManageHome;
use App\Filament\Pages\ManageSettings;
use App\Filament\Resources\InquiryResource\Pages\EditInquiry;
use App\Filament\Resources\ServiceResource\Pages\EditService;
use App\Models\Inquiry;
use App\Models\Service;
use App\Models\SiteOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminContentEditingTest extends TestCase
{
    use RefreshDatabase;

    public function test_contacts_accept_common_russian_phone_formats(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withSession(['password_hash_web' => $user->getAuthPassword()]);

        $phones = [
            '+7 (988) 096-20-14',
            '+79880962014',
            '8 (988) 096-20-14',
            '+7 988 096-20-14',
        ];

        foreach ($phones as $phone) {
            Livewire::test(ManageContacts::class)
                ->set('data.phone', $phone)
                ->call('save')
                ->assertHasNoFormErrors();

            $this->assertSame($phone, SiteOption::query()->value('phone'));
            $this->get('/')->assertSee($phone, false);
            $this->get('/kontakty')->assertSee($phone, false);
        }
    }

    public function test_contacts_reject_an_invalid_phone_and_keep_the_previous_value(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withSession(['password_hash_web' => $user->getAuthPassword()]);

        SiteOption::query()->firstOrFail()->update(['phone' => '+7 988 096-20-14']);

        Livewire::test(ManageContacts::class)
            ->set('data.phone', 'не номер')
            ->call('save')
            ->assertHasFormErrors(['phone']);

        $this->assertSame('+7 988 096-20-14', SiteOption::query()->value('phone'));
    }

    public function test_settings_can_save_a_phone_with_parentheses(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withSession(['password_hash_web' => $user->getAuthPassword()]);

        Livewire::test(ManageSettings::class)
            ->set('data.phone', '+7 (988) 096-20-14')
            ->set('data.email', 'prochnost102@inbox.ru')
            ->call('save')
            ->assertHasNoFormErrors();

        $options = SiteOption::query()->firstOrFail();
        $this->assertSame('+7 (988) 096-20-14', $options->phone);
        $this->assertSame('prochnost102@inbox.ru', $options->email);
    }

    public function test_contacts_can_update_contact_fields_and_keep_bank_details_hidden(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withSession(['password_hash_web' => $user->getAuthPassword()]);

        Livewire::test(ManageContacts::class)
            ->set('data.phone', '+7 (988) 096-20-14')
            ->set('data.email', 'prochnost102@inbox.ru')
            ->set('data.inn', '027608013400')
            ->set('data.address', 'Ставрополь')
            ->set('data.contacts_h1', 'Контакты')
            ->set('data.bank_details_visible', false)
            ->set('data.bank_account', '40817810860810129507')
            ->call('save')
            ->assertHasNoFormErrors();

        Livewire::test(ManageContacts::class)
            ->assertSet('data.phone', '+7 (988) 096-20-14')
            ->assertSet('data.email', 'prochnost102@inbox.ru')
            ->assertSet('data.inn', '027608013400')
            ->assertSet('data.address', 'Ставрополь')
            ->assertSet('data.bank_account', '40817810860810129507')
            ->assertSet('data.bank_details_visible', false);

        $this->get('/kontakty')
            ->assertSee('+7 (988) 096-20-14', false)
            ->assertSee('prochnost102@inbox.ru', false)
            ->assertSee('027608013400', false)
            ->assertSee('Ставрополь', false)
            ->assertDontSee('40817810860810129507', false);
    }

    public function test_home_and_direction_editors_keep_saved_text_and_urls(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withSession(['password_hash_web' => $user->getAuthPassword()]);

        Livewire::test(ManageHome::class)
            ->set('data.h1', 'Обновлённый заголовок')
            ->set('data.button_url', '/kontakty')
            ->set('data.seo_title', 'SEO главной')
            ->call('save')
            ->assertHasNoFormErrors();

        Livewire::test(ManageHome::class)
            ->assertSet('data.h1', 'Обновлённый заголовок')
            ->assertSet('data.button_url', '/kontakty')
            ->assertSet('data.seo_title', 'SEO главной');

        $this->get('/')
            ->assertSee('Обновлённый заголовок', false)
            ->assertSee('<title>SEO главной</title>', false);

        $service = Service::query()->where('key', 'strength')->firstOrFail();

        Livewire::test(EditService::class, ['record' => $service->id])
            ->set('data.title', 'Расчёты на прочность и жёсткость')
            ->set('data.h1', 'Новый H1 прочности')
            ->set('data.seo_title', 'SEO прочности')
            ->set('data.excerpt', 'Короткое описание карточки')
            ->call('save')
            ->assertHasNoFormErrors();

        Livewire::test(EditService::class, ['record' => $service->id])
            ->assertSet('data.h1', 'Новый H1 прочности')
            ->assertSet('data.seo_title', 'SEO прочности')
            ->assertSet('data.excerpt', 'Короткое описание карточки');

        $this->get('/raschety-na-prochnost')
            ->assertSee('Новый H1 прочности', false)
            ->assertSee('<title>SEO прочности</title>', false);
    }

    public function test_invalid_urls_and_emails_are_rejected_in_admin_forms(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withSession(['password_hash_web' => $user->getAuthPassword()]);

        Livewire::test(ManageHome::class)
            ->set('data.button_url', 'javascript:alert(1)')
            ->call('save')
            ->assertHasFormErrors(['button_url']);

        Livewire::test(ManageContacts::class)
            ->set('data.email', 'не-почта')
            ->call('save')
            ->assertHasFormErrors(['email']);

        Livewire::test(ManageSettings::class)
            ->set('data.logo_url', 'ftp://example.com')
            ->call('save')
            ->assertHasFormErrors(['logo_url']);
    }

    public function test_inquiry_status_can_be_changed_without_losing_contact_fields(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withSession(['password_hash_web' => $user->getAuthPassword()]);

        $inquiry = Inquiry::query()->create([
            'form_token' => 'token-admin-audit',
            'name' => 'Аудит',
            'contact' => '+7 988 000-00-00',
            'phone' => '+7 988 000-00-00',
            'email' => null,
            'message' => 'Проверка раздела заявок.',
            'page_url' => 'http://localhost/kontakty',
            'status' => Inquiry::STATUS_NEW,
        ]);

        Livewire::test(EditInquiry::class, ['record' => $inquiry->id])
            ->set('data.status', Inquiry::STATUS_IN_PROGRESS)
            ->call('save')
            ->assertHasNoFormErrors();

        $inquiry->refresh();
        $this->assertSame(Inquiry::STATUS_IN_PROGRESS, $inquiry->status);
        $this->assertSame('Аудит', $inquiry->name);
        $this->assertSame('+7 988 000-00-00', $inquiry->phone);
        $this->assertSame('Проверка раздела заявок.', $inquiry->message);
    }
}
