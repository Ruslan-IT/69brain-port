<?php

namespace App\Support;

use App\Models\HomePage;
use App\Models\Service;
use App\Models\SiteOption;

class ContentCatalog
{
    /**
     * @return array<string, mixed>
     */
    public static function home(): array
    {
        return [
            'eyebrow' => 'Инженерное бюро',
            'h1' => "КБ\n«Прочность»",
            'subtitle' => null,
            'description' => 'Инженерные расчёты, компьютерное моделирование и статистический анализ.',
            'button_label' => 'Связаться с нами',
            'button_url' => '/kontakty',
            'works_title' => 'Что мы делаем?',
            'works' => [
                [
                    'title' => 'Инженерные расчёты',
                    'body' => '<p>Выполняем инженерные расчёты деталей и конструкций: на прочность, жёсткость, устойчивость, усталостную прочность конструкций. Владеем как аналитическими методами расчётов из сопротивления материалов и строительной механики, так и методом конечных элементов в ПО ANSYS.</p>',
                    'link_label' => 'Расчёты на прочность и жёсткость',
                    'link_url' => '/raschety-na-prochnost',
                ],
                [
                    'title' => 'Статистические расчёты',
                    'body' => '<p>Выполняем статистические расчёты (<span lang="en">not so big data</span>, массивы размерностью до 1000 столбцов на 500&nbsp;000 строк): анализ качества данных, проверка гипотез, поиск корреляций, регрессионный анализ, построение контрольных карт Шухарта и пр.</p>',
                    'link_label' => 'Статистические расчёты',
                    'link_url' => '/statisticheskie-raschety',
                ],
                [
                    'title' => 'Технологическая подготовка',
                    'body' => '<p>Выполняем технологическую подготовку комплекта файлов (*.DXF) и документов для лазерной резки: отрисовка 3D-модели, раскрой материала, расчёт затрат времени и материала.</p><p>Опционально: технологический расчёт операций гибки, при предоставлении технологических данных (коэффициенты пружинения, чертежи штамповой оснастки и пр.).</p>',
                    'link_label' => 'Технологические расчёты по листовой штамповке и лазерной резке',
                    'link_url' => '/tehnologicheskie-raschety',
                ],
            ],
            'who_title' => 'Кто мы?',
            'who_text' => 'Команда инженеров под руководством кандидата технических наук с опытом работы инженером по прочности в конструкторском центре Boeing.',
            'who_extra' => null,
            'directions_title' => 'Направления деятельности',
            'cta_title' => 'Есть инженерная задача?',
            'cta_text' => 'Свяжитесь с нами, чтобы обсудить задачу и возможные варианты её решения.',
            'cta_button_label' => 'Связаться с нами',
            'cta_button_url' => '/kontakty',
            'seo_title' => 'КБ «Прочность» — инженерные расчёты и моделирование',
            'seo_description' => 'Инженерное бюро КБ «Прочность»: расчёты на прочность и жёсткость, статистический анализ и технологическая подготовка лазерной резки и листовой штамповки.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function options(): array
    {
        return [
            'logo_text' => 'КБ «Прочность»',
            'logo_url' => null,
            'header_button_label' => 'Связаться с нами',
            'header_button_url' => '/kontakty',
            'footer_lead' => 'Инженерные расчёты, компьютерное моделирование и статистический анализ.',
            'footer_links' => [
                ['label' => 'Главная', 'url' => '/'],
                ['label' => 'Контакты', 'url' => '/kontakty'],
            ],
            'copyright' => 'КБ «Прочность»',
            'phone' => '+7 (900) 000-00-00',
            'email' => 'info@example.ru',
            'address' => null,
            'requisites' => null,
            'contact_extra' => null,
            'contacts_title' => 'Связь',
            'contacts_h1' => 'Контакты',
            'contacts_lead' => 'Свяжитесь с нами, чтобы обсудить задачу и возможные варианты её решения.',
            'contacts_seo_title' => 'Контакты — КБ «Прочность»',
            'contacts_seo_description' => 'Контакты КБ «Прочность». Форма обратной связи для обсуждения инженерной задачи.',
            'site_name' => 'КБ «Прочность»',
            'default_seo_title' => 'КБ «Прочность»',
            'default_seo_description' => 'Инженерное бюро КБ «Прочность»: расчёты на прочность, компьютерное моделирование и статистический анализ.',
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function serviceAttributes(): array
    {
        return [
            [
                'key' => 'strength',
                'title' => 'Расчёты на прочность и жёсткость',
                'slug' => 'raschety-na-prochnost',
                'excerpt' => 'Металлические детали, элементы БПЛА и металлоконструкции: прочность, жёсткость и устойчивость.',
                'h1' => 'Расчёты на прочность и жёсткость',
                'lead' => 'Выполняем инженерные расчёты деталей и конструкций: на прочность, жёсткость, устойчивость, усталостную прочность конструкций. Владеем как аналитическими методами расчётов из сопротивления материалов и строительной механики, так и методом конечных элементов в ПО ANSYS.',
                'card_link_label' => 'Подробнее',
                'sort' => 1,
                'is_published' => true,
                'seo_title' => 'Расчёты на прочность и жёсткость — КБ «Прочность»',
                'seo_description' => 'Расчёты на прочность, жёсткость и устойчивость металлических деталей, элементов БПЛА и металлоконструкций. Аналитические методы и метод конечных элементов в ANSYS.',
                'blocks' => [
                    [
                        'title' => 'Расчёт металлических деталей',
                        'body' => '<p>Расчёт на прочность, жёсткость и устойчивость металлических деталей.</p>',
                        'extra' => null,
                        'link_label' => null,
                        'link_url' => null,
                        'icon' => 'plate',
                        'anchor' => 'metallicheskie-detali',
                        'nav' => null,
                    ],
                    [
                        'title' => 'Расчёт элементов БПЛА',
                        'body' => '<p>Расчёт на прочность, жёсткость и устойчивость элементов БПЛА из металлических, неметаллических и композитных материалов.</p>',
                        'extra' => null,
                        'link_label' => null,
                        'link_url' => null,
                        'icon' => 'drone',
                        'anchor' => 'elementy-bpla',
                        'nav' => null,
                    ],
                    [
                        'title' => 'Расчёт металлоконструкций',
                        'body' => '<p>Расчёт на прочность металлоконструкций.</p>',
                        'extra' => null,
                        'link_label' => null,
                        'link_url' => null,
                        'icon' => 'frame',
                        'anchor' => 'metallokonstrukcii',
                        'nav' => null,
                    ],
                ],
            ],
            [
                'key' => 'statistics',
                'title' => 'Статистические расчёты',
                'slug' => 'statisticheskie-raschety',
                'excerpt' => 'Качество данных, гипотезы, регрессия, ANOVA, измерительные системы и карты Шухарта.',
                'h1' => 'Статистические расчёты',
                'lead' => 'Выполняем статистические расчёты (<span lang="en">not so big data</span>, массивы размерностью до 1000 столбцов на 500&nbsp;000 строк): анализ качества данных, проверка гипотез, поиск корреляций, регрессионный анализ, построение контрольных карт Шухарта и пр.',
                'card_link_label' => 'Подробнее',
                'sort' => 2,
                'is_published' => true,
                'seo_title' => 'Статистические расчёты — КБ «Прочность»',
                'seo_description' => 'Статистические расчёты: проверка гипотез, качество данных, регрессия, дисперсионный анализ, анализ измерительных систем и контрольные карты Шухарта.',
                'blocks' => [
                    [
                        'title' => 'Базовая статистика',
                        'body' => '<ul><li>Проверка статистических гипотез.</li><li>Поиск выбросов.</li><li>Анализ качества данных.</li></ul>',
                        'extra' => null,
                        'link_label' => null,
                        'link_url' => null,
                        'icon' => null,
                        'anchor' => 'bazovaya-statistika',
                        'nav' => null,
                    ],
                    [
                        'title' => 'Регрессионный и дисперсионный анализ',
                        'body' => '<ul><li>Регрессионный анализ.</li><li>Дисперсионный анализ (ANOVA).</li><li>Поиск зависимостей и корреляций.</li></ul>',
                        'extra' => null,
                        'link_label' => null,
                        'link_url' => null,
                        'icon' => null,
                        'anchor' => 'regressiya',
                        'nav' => null,
                    ],
                    [
                        'title' => 'Анализ измерительных систем (MSA)',
                        'body' => '<ul><li>Повторяемость.</li><li>Воспроизводимость.</li></ul>',
                        'extra' => null,
                        'link_label' => null,
                        'link_url' => null,
                        'icon' => null,
                        'anchor' => 'msa',
                        'nav' => null,
                    ],
                    [
                        'title' => 'Контрольные карты',
                        'body' => '<ul><li>Контрольные карты Шухарта.</li></ul>',
                        'extra' => null,
                        'link_label' => null,
                        'link_url' => null,
                        'icon' => null,
                        'anchor' => 'kontrolnye-karty',
                        'nav' => null,
                    ],
                ],
            ],
            [
                'key' => 'technology',
                'title' => 'Технологические расчёты по листовой штамповке и лазерной резке',
                'slug' => 'tehnologicheskie-raschety',
                'excerpt' => 'Штамповка в Deform-3D, раскрой листа и подготовка файлов DXF.',
                'h1' => 'Технологические расчёты по листовой штамповке и лазерной резке',
                'lead' => 'Выполняем технологическую подготовку комплекта файлов (*.DXF) и документов для лазерной резки: отрисовка 3D-модели, раскрой материала, расчёт затрат времени и материала.',
                'card_link_label' => 'Подробнее',
                'sort' => 3,
                'is_published' => true,
                'seo_title' => 'Технологические расчёты по листовой штамповке и лазерной резке — КБ «Прочность»',
                'seo_description' => 'Моделирование холодной листовой штамповки в Deform-3D и подготовка комплекта DXF для лазерной резки: раскрой, файлы для станка, затраты времени и материала.',
                'blocks' => [
                    [
                        'title' => 'Листовая штамповка',
                        'body' => '<p>Моделирование процессов холодной листовой штамповки в среде Deform-3D.</p>',
                        'extra' => null,
                        'link_label' => null,
                        'link_url' => null,
                        'icon' => 'stamp',
                        'anchor' => 'listovaya-shtampovka',
                        'nav' => null,
                    ],
                    [
                        'title' => 'Лазерная резка',
                        'body' => '<p>Расчёты для лазерной резки:</p><ul><li>Раскрой материала.</li><li>Подготовка файлов для станка.</li><li>Подготовка комплекта файлов DXF.</li><li>Расчёт затрат времени и материала.</li></ul>',
                        'extra' => null,
                        'link_label' => null,
                        'link_url' => null,
                        'icon' => 'laser',
                        'anchor' => 'lazernaya-rezka',
                        'nav' => null,
                    ],
                    [
                        'title' => 'Дополнительная возможность',
                        'body' => '<p>Технологический расчёт операций гибки при предоставлении необходимых технологических данных (коэффициенты пружинения, чертежи штамповой оснастки и пр.).</p>',
                        'extra' => null,
                        'link_label' => null,
                        'link_url' => null,
                        'icon' => null,
                        'anchor' => 'gibka',
                        'nav' => 'Операции гибки',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return list<Service>
     */
    public static function services(): array
    {
        return array_map(
            static fn (array $attributes): Service => new Service($attributes),
            self::serviceAttributes(),
        );
    }

    public static function service(string $key): ?Service
    {
        foreach (self::services() as $service) {
            if ($service->key === $key) {
                return $service;
            }
        }

        return null;
    }

    public static function seed(): void
    {
        if (! HomePage::query()->exists()) {
            HomePage::query()->create(self::home());
        }

        if (! SiteOption::query()->exists()) {
            SiteOption::query()->create(self::options());
        }

        foreach (self::serviceAttributes() as $attributes) {
            Service::query()->firstOrCreate(
                ['slug' => $attributes['slug']],
                $attributes,
            );
        }
    }
}
