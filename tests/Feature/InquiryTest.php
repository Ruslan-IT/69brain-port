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

    public function test_configured_mailer_sends_the_inquiry_to_the_contact_email(): void
    {
        Mail::fake();
        config(['mail.default' => 'smtp']);

        $this->post(route('contacts.store'), [
            'name' => 'Иван',
            'contact' => 'ivan@example.com',
            'message' => 'Нужен расчёт кронштейна на прочность.',
            'form_token' => $this->formToken(),
        ])->assertSessionHas('form_notice_tone', 'accepted')
            ->assertSessionHas('form_notice', function (string $notice): bool {
                return str_contains($notice, 'Заявка отправлена');
            });

        Mail::assertSent(InquiryReceived::class, function (InquiryReceived $mail): bool {
            return $mail->hasTo('prochnost102@inbox.ru')
                && $mail->envelope()->subject === 'Новая заявка с сайта КБ «Прочность»';
        });

        $this->assertNotNull(Inquiry::query()->firstOrFail()->mail_sent_at);
    }

    public function test_mail_failure_keeps_the_saved_inquiry(): void
    {
        config(['mail.default' => 'smtp']);
        Mail::shouldReceive('to')->once()->with('prochnost102@inbox.ru')->andReturnSelf();
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
