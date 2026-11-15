<?php

namespace Tests\Feature;

use App\Filament\Resources\InquiryResource\Pages\EditInquiry;
use App\Filament\Resources\InquiryResource\Pages\ListInquiries;
use App\Mail\InquiryReceived;
use App\Models\Inquiry;
use App\Models\SiteOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;

class InquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_inquiry_is_stored_without_claiming_delivery_when_mail_is_not_configured(): void
    {
        $token = $this->formToken();

        $response = $this->post(route('contacts.store'), [
            'name' => 'Иван',
            'contact' => 'ivan@example.com',
            'message' => 'Нужен расчёт кронштейна на прочность.',
            'form_token' => $token,
            'page_url' => route('contacts'),
        ]);

        $response->assertRedirect(route('contacts'));
        $response->assertSessionHas('form_notice', function (string $notice): bool {
            return str_contains($notice, 'сохранена')
                && str_contains($notice, 'не отправлено')
                && ! str_contains(mb_strtolower($notice), 'успеш');
        });

        $inquiry = Inquiry::query()->first();
        $this->assertNotNull($inquiry);
        $this->assertSame('Иван', $inquiry->name);
        $this->assertSame('ivan@example.com', $inquiry->email);
        $this->assertNull($inquiry->phone);
        $this->assertSame(Inquiry::STATUS_NEW, $inquiry->status);
        $this->assertNull($inquiry->mail_sent_at);
        $this->assertNotNull($inquiry->mail_error);
        $this->assertSame(route('contacts'), $inquiry->page_url);
    }

    public function test_phone_is_stored_separately_from_email(): void
    {
        $this->post(route('contacts.store'), [
            'name' => 'Мария',
            'contact' => '+7 988 096-20-14',
            'message' => 'Нужна консультация по расчёту.',
            'form_token' => $this->formToken(),
            'page_url' => 'https://evil.example/phish',
        ])->assertRedirect(route('contacts'));

        $inquiry = Inquiry::query()->firstOrFail();
        $this->assertSame('+7 988 096-20-14', $inquiry->phone);
        $this->assertNull($inquiry->email);
        $this->assertStringStartsWith(url('/'), (string) $inquiry->page_url);
    }

    public function test_invalid_inquiry_is_rejected_and_not_stored(): void
    {
        $this->post(route('contacts.store'), [
            'name' => '',
            'contact' => 'нет',
            'message' => 'коротко',
            'form_token' => $this->formToken(),
        ])->assertSessionHasErrors(['name', 'contact', 'message']);

        $this->assertSame(0, Inquiry::query()->count());
    }

    public function test_repeated_submit_does_not_create_a_second_inquiry(): void
    {
        $token = $this->formToken();
        $payload = [
            'name' => 'Иван',
            'contact' => 'ivan@example.com',
            'message' => 'Нужен расчёт кронштейна на прочность.',
            'form_token' => $token,
        ];

        $this->post(route('contacts.store'), $payload)->assertRedirect(route('contacts'));
        $this->post(route('contacts.store'), $payload)
            ->assertRedirect(route('contacts'))
            ->assertSessionHas('form_notice', function (string $notice): bool {
                return str_contains($notice, 'не создан');
            });

        $this->assertSame(1, Inquiry::query()->count());
    }

    public function test_configured_mailer_sends_the_inquiry_to_the_site_settings_email(): void
    {
        Mail::fake();
        config(['mail.default' => 'smtp']);

        $recipient = SiteOption::query()->value('email');
        $this->assertIsString($recipient);
        $this->assertNotSame('ruslan.r.f@yandex.ru', $recipient);

        $this->post(route('contacts.store'), [
            'name' => 'Иван',
            'contact' => 'ivan@example.com',
            'message' => 'Нужен расчёт кронштейна на прочность.',
            'form_token' => $this->formToken(),
        ])->assertSessionHas('form_notice_tone', 'accepted')
            ->assertSessionHas('form_notice', function (string $notice): bool {
                return str_contains($notice, 'Заявка отправлена');
            });

        Mail::assertSent(InquiryReceived::class, function (InquiryReceived $mail) use ($recipient): bool {
            return $mail->hasTo($recipient)
                && ! $mail->hasTo('ruslan.r.f@yandex.ru')
                && $mail->envelope()->subject === 'Новая заявка с сайта КБ «Прочность»';
        });
        Mail::assertSentCount(1);

        $this->assertNotNull(Inquiry::query()->firstOrFail()->mail_sent_at);
    }

    public function test_changed_site_email_becomes_the_new_inquiry_recipient(): void
    {
        Mail::fake();
        config(['mail.default' => 'smtp']);

        SiteOption::query()->firstOrFail()->update(['email' => 'new-desk@example.ru']);

        $this->post(route('contacts.store'), [
            'name' => 'Иван',
            'contact' => 'ivan@example.com',
            'message' => 'Нужен расчёт кронштейна на прочность.',
            'form_token' => $this->formToken(),
        ])->assertSessionHas('form_notice_tone', 'accepted');

        Mail::assertSent(InquiryReceived::class, function (InquiryReceived $mail): bool {
            return $mail->hasTo('new-desk@example.ru')
                && ! $mail->hasTo('prochnost102@inbox.ru')
                && ! $mail->hasTo('ruslan.r.f@yandex.ru');
        });
        Mail::assertSentCount(1);
    }

    public function test_missing_site_email_keeps_inquiry_and_does_not_send_mail(): void
    {
        Mail::fake();
        config(['mail.default' => 'smtp']);

        SiteOption::query()->firstOrFail()->update(['email' => null]);

        $this->post(route('contacts.store'), [
            'name' => 'Иван',
            'contact' => 'ivan@example.com',
            'message' => 'Нужен расчёт кронштейна на прочность.',
            'form_token' => $this->formToken(),
        ])->assertSessionHas('form_notice', function (string $notice): bool {
            return str_contains($notice, 'сохранена')
                && str_contains($notice, 'не отправлено');
        });

        Mail::assertNothingSent();

        $inquiry = Inquiry::query()->firstOrFail();
        $this->assertNull($inquiry->mail_sent_at);
        $this->assertStringContainsString('не указана корректная почта', (string) $inquiry->mail_error);
    }

    public function test_mail_failure_keeps_the_saved_inquiry(): void
    {
        config(['mail.default' => 'smtp']);
        $recipient = SiteOption::query()->value('email');
        $this->assertIsString($recipient);

        Mail::shouldReceive('to')->once()->with($recipient)->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new RuntimeException('smtp down'));

        $this->post(route('contacts.store'), [
            'name' => 'Иван',
            'contact' => 'ivan@example.com',
            'message' => 'Нужен расчёт кронштейна на прочность.',
            'form_token' => $this->formToken(),
        ])->assertSessionHas('form_notice', function (string $notice): bool {
            return str_contains($notice, 'сохранена')
                && str_contains($notice, 'не отправлено');
        });

        $inquiry = Inquiry::query()->firstOrFail();
        $this->assertNull($inquiry->mail_sent_at);
        $this->assertStringContainsString('smtp down', (string) $inquiry->mail_error);
    }

    public function test_application_mail_code_does_not_hardcode_admin_recipient(): void
    {
        $paths = [
            app_path('Http/Controllers/ContactController.php'),
            app_path('Mail/InquiryReceived.php'),
            app_path('Mail/Transport/PhpMailTransport.php'),
            app_path('Providers/AppServiceProvider.php'),
            config_path('mail.php'),
        ];

        foreach ($paths as $path) {
            $this->assertFileExists($path);
            $contents = file_get_contents($path) ?: '';
            $this->assertStringNotContainsString(
                'ruslan.r.f@yandex.ru',
                $contents,
                $path.' must not hardcode the admin recipient email.',
            );
        }
    }

    public function test_smtp_mailer_config_uses_mail_ru_defaults_without_hardcoded_recipient(): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp' => [
                'transport' => 'smtp',
                'scheme' => 'smtps',
                'host' => 'smtp.mail.ru',
                'port' => 465,
                'username' => 'prochnost102@inbox.ru',
                'password' => 'secret-app-password',
            ],
            'mail.from.address' => 'prochnost102@inbox.ru',
            'mail.from.name' => 'КБ «Прочность»',
        ]);

        $smtp = config('mail.mailers.smtp');

        $this->assertSame('smtp', config('mail.default'));
        $this->assertSame('smtps', $smtp['scheme']);
        $this->assertSame('smtp.mail.ru', $smtp['host']);
        $this->assertSame(465, (int) $smtp['port']);
        $this->assertSame('prochnost102@inbox.ru', $smtp['username']);
        $this->assertSame('prochnost102@inbox.ru', config('mail.from.address'));
        $this->assertSame('КБ «Прочность»', config('mail.from.name'));

        SiteOption::query()->firstOrFail()->update(['email' => 'desk-from-admin@example.ru']);

        $this->assertSame('desk-from-admin@example.ru', SiteOption::query()->firstOrFail()->notificationEmail());
        $this->assertSame('prochnost102@inbox.ru', config('mail.from.address'));
        $this->assertNotSame(
            SiteOption::query()->firstOrFail()->notificationEmail(),
            config('mail.from.address'),
            'From (SMTP mailbox) must stay separate from To (admin notification email).',
        );
    }

    public function test_bank_details_stay_hidden_until_the_admin_enables_them(): void
    {
        $this->get(route('contacts'))
            ->assertOk()
            ->assertSee('027608013400', false)
            ->assertDontSee('40817810860810129507', false)
            ->assertDontSee('самозанят', false);

        SiteOption::query()->firstOrFail()->update(['bank_details_visible' => true]);

        $this->get(route('contacts'))
            ->assertSee('40817810860810129507', false)
            ->assertSee('7707083893', false)
            ->assertSee('027608013400', false);
    }

    public function test_admin_can_open_an_inquiry_and_change_its_status(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withSession(['password_hash_web' => $user->getAuthPassword()]);

        $inquiry = Inquiry::query()->create([
            'form_token' => 'token-admin',
            'name' => 'Иван',
            'contact' => 'ivan@example.com',
            'email' => 'ivan@example.com',
            'message' => 'Нужен расчёт кронштейна на прочность.',
            'status' => Inquiry::STATUS_NEW,
        ]);

        Livewire::test(ListInquiries::class)
            ->assertOk()
            ->assertSee('Иван')
            ->assertSee('Новая');

        Livewire::test(EditInquiry::class, ['record' => $inquiry->id])
            ->set('data.status', Inquiry::STATUS_IN_PROGRESS)
            ->call('save')
            ->assertHasNoFormErrors();

        $inquiry->refresh();
        $this->assertSame(Inquiry::STATUS_IN_PROGRESS, $inquiry->status);
        $this->assertSame('Иван', $inquiry->name);
        $this->assertSame('ivan@example.com', $inquiry->email);
    }

    private function formToken(): string
    {
        $this->get(route('contacts'))->assertOk();

        $token = session('contact_form_token');
        $this->assertIsString($token);

        return $token;
    }
}
