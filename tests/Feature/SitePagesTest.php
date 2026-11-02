<?php

namespace Tests\Feature;

use Tests\TestCase;

class SitePagesTest extends TestCase
{
    public function test_public_pages_are_available(): void
    {
        $home = $this->get(route('home'))
            ->assertOk()
            ->assertSee('КБ «Прочность»', false)
            ->assertSee('Инженерные расчёты, компьютерное моделирование и статистический анализ.', false)
            ->assertSee('Что мы делаем?', false)
            ->assertSee('Кто мы?', false)
            ->assertSee('js/beam-analysis.js', false)
            ->assertSee('+7 (900) 000-00-00', false);
        $this->assertSame(1, substr_count($home->getContent(), '<h1'));

        $strength = $this->get(route('strength'))
            ->assertOk()
            ->assertSee('Расчёты на прочность и жёсткость', false)
            ->assertSee('data-scene="strength"', false)
            ->assertSee('js/scenes/strength.js', false)
            ->assertSee('Расчёт элементов БПЛА', false);
        $this->assertSame(1, substr_count($strength->getContent(), '<h1'));

        $statistics = $this->get(route('statistics'))
            ->assertOk()
            ->assertSee('Статистические расчёты', false)
            ->assertSee('not so big data', false)
            ->assertSee('js/scenes/statistics.js', false);
        $this->assertSame(1, substr_count($statistics->getContent(), '<h1'));

        $technology = $this->get(route('technology'))
            ->assertOk()
            ->assertSee('Листовая штамповка', false)
            ->assertSee('Операции гибки', false)
            ->assertSee('Дополнительная возможность', false)
            ->assertSee('data-mode="laser"', false)
            ->assertSee('js/scenes/technology.js', false);
        $this->assertSame(1, substr_count($technology->getContent(), '<h1'));

        $contacts = $this->get(route('contacts'))
            ->assertOk()
            ->assertSee('+7 (900) 000-00-00', false)
            ->assertSee('info@example.ru', false)
            ->assertDontSee('Не указан', false);
        $this->assertSame(1, substr_count($contacts->getContent(), '<h1'));
    }

    public function test_contact_form_rejects_invalid_input(): void
    {
        $response = $this->from(route('contacts'))->post(route('contacts.store'), [
            'name' => '',
            'contact' => 'нет',
            'message' => 'коротко',
        ]);

        $response->assertRedirect(route('contacts'));
        $response->assertSessionHasErrors(['name', 'contact', 'message']);
        $response->assertSessionMissing('form_notice');
    }

    public function test_contact_form_does_not_report_successful_delivery(): void
    {
        $response = $this->from(route('contacts'))->post(route('contacts.store'), [
            'name' => 'Иван',
            'contact' => 'ivan@example.com',
            'message' => 'Нужен расчёт кронштейна на прочность.',
        ]);

        $response->assertRedirect(route('contacts'));
        $response->assertSessionHas('form_notice', function (string $notice): bool {
            return str_contains($notice, 'не отправлено')
                && ! str_contains(mb_strtolower($notice), 'успеш');
        });
    }
}
