<?php

namespace App\Http\Controllers;

use App\Mail\InquiryReceived;
use App\Models\Inquiry;
use App\Support\SiteContent;
use Closure;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class ContactController extends Controller
{
    public function show(): View
    {
        if (! is_string(session('contact_form_token')) || session('contact_form_token') === '') {
            session(['contact_form_token' => Str::random(40)]);
        }

        return view('pages.contacts');
    }

    public function store(Request $request): RedirectResponse
    {
        if (filled($request->input('company_website'))) {
            return redirect()
                ->route('contacts')
                ->with('form_notice', 'Проверьте поля формы. Заявка не сохранена.');
        }

        $inquiriesReady = Schema::hasTable('inquiries');
        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'contact' => ['required', 'string', 'max:180', $this->phoneOrEmail()],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];

        if ($inquiriesReady) {
            $rules['form_token'] = ['required', 'string', 'max:64'];
            $rules['page_url'] = ['nullable', 'string', 'max:255'];
        }

        $data = $request->validate($rules, [
            'name.required' => 'Укажите имя.',
            'name.string' => 'Имя должно быть строкой.',
            'name.max' => 'Имя не должно быть длиннее 120 символов.',
            'contact.required' => 'Укажите телефон или электронную почту.',
            'contact.string' => 'Контакт должен быть строкой.',
            'contact.max' => 'Контакт не должен быть длиннее 180 символов.',
            'message.required' => 'Напишите сообщение.',
            'message.string' => 'Сообщение должно быть строкой.',
            'message.min' => 'Сообщение должно содержать хотя бы 10 символов.',
            'message.max' => 'Сообщение не должно быть длиннее 5000 символов.',
            'form_token.required' => 'Откройте страницу контактов ещё раз и отправьте форму повторно.',
        ]);

        if (! $inquiriesReady) {
            return redirect()
                ->route('contacts')
                ->withInput()
                ->with('form_notice', 'Сообщение не отправлено и никуда не передано: приём обращений ещё не настроен.');
        }

        $sessionToken = session('contact_form_token');

        if (! is_string($sessionToken) || ! hash_equals($sessionToken, $data['form_token'])) {
            return $this->backToForm('Откройте страницу контактов ещё раз и отправьте форму повторно. Заявка не создана.');
        }

        $contact = trim($data['contact']);
        $email = filter_var($contact, FILTER_VALIDATE_EMAIL) ? $contact : null;
        $phone = $email === null ? $contact : null;

        try {
            $inquiry = Inquiry::query()->create([
                'form_token' => $data['form_token'],
                'name' => trim($data['name']),
                'contact' => $contact,
                'phone' => $phone,
                'email' => $email,
                'message' => trim($data['message']),
                'page_url' => $this->pageUrl($data['page_url'] ?? null),
                'status' => Inquiry::STATUS_NEW,
            ]);
        } catch (UniqueConstraintViolationException) {
            session()->forget('contact_form_token');

            return $this->backToForm('Эта заявка уже принята. Повторно она не создавалась.');
        } catch (Throwable $exception) {
            Log::error('Не удалось сохранить заявку с сайта.', ['exception' => $exception]);

            return $this->backToForm('Заявку не удалось сохранить. Сообщение не доставлено.');
        }

        session()->forget('contact_form_token');
        $delivered = $this->notify($inquiry);

        if ($delivered) {
            return redirect()
                ->route('contacts')
                ->with('form_notice_tone', 'accepted')
                ->with('form_notice', 'Заявка отправлена. Мы получили сообщение и ответим по указанному контакту.');
        }

        return $this->backToForm('Заявка сохранена. Письмо на почту не отправлено, но сообщение записано и не потеряется.');
    }

    private function notify(Inquiry $inquiry): bool
    {
        $recipient = SiteContent::options()->notificationEmail();

        if ($recipient === null) {
            $inquiry->update(['mail_error' => 'В контактах не указана почта для уведомления.']);
            Log::warning('Заявка сохранена, но почта для уведомления не задана.', ['inquiry_id' => $inquiry->id]);

            return false;
        }

        if (! $this->mailCanBeDelivered()) {
            $inquiry->update([
                'mail_error' => 'Почтовый сервер не настроен: MAIL_MAILER='.config('mail.default').'.',
            ]);
            Log::warning('Заявка сохранена, письмо не отправлялось: почтовый сервер не настроен.', [
                'inquiry_id' => $inquiry->id,
                'mailer' => config('mail.default'),
            ]);

            return false;
        }

        try {
            Mail::to($recipient)->send(new InquiryReceived($inquiry));
            $inquiry->update([
                'mail_sent_at' => now(),
                'mail_error' => null,
            ]);

            return true;
        } catch (Throwable $exception) {
            $inquiry->update([
                'mail_error' => Str::limit($exception->getMessage(), 500),
            ]);
            Log::error('Заявка сохранена, но письмо не отправилось.', [
                'inquiry_id' => $inquiry->id,
                'exception' => $exception,
            ]);

            return false;
        }
    }

    private function mailCanBeDelivered(): bool
    {
        return ! in_array(config('mail.default'), ['log', 'array'], true);
    }

    private function pageUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '' || ! str_starts_with($url, url('/'))) {
            return url()->current();
        }

        return $url;
    }

    private function backToForm(string $notice): RedirectResponse
    {
        return redirect()
            ->route('contacts')
            ->with('form_notice', $notice);
    }

    private function phoneOrEmail(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $value = trim((string) $value);

            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return;
            }

            $digits = preg_replace('/\D+/', '', $value) ?? '';

            if (strlen($digits) >= 6 && preg_match('/^[\d\s+\-().]+$/u', $value)) {
                return;
            }

            $fail('Укажите телефон или электронную почту.');
        };
    }
}
