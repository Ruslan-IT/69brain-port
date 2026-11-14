Новая заявка с сайта КБ «Прочность»

Имя: {{ $inquiry->name }}
Телефон: {{ $inquiry->phone ?: 'не указан' }}
Электронная почта: {{ $inquiry->email ?: 'не указана' }}
Страница: {{ $inquiry->page_url ?: 'не указана' }}
Дата: {{ $inquiry->created_at?->timezone(config('app.timezone'))->format('d.m.Y H:i') }}

Сообщение:
{{ $inquiry->message }}
