<?php

namespace App\Http\Controllers;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('pages.contacts');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'contact' => ['required', 'string', 'max:180', $this->phoneOrEmail()],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ], [
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
        ]);

        // Получатель не задан, почта смотрит на log/example. Обращение не сохраняем и не отправляем.
        return redirect()
            ->route('contacts')
            ->withInput()
            ->with('form_notice', 'Сообщение не отправлено и никуда не передано: приём обращений ещё не настроен.');
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
