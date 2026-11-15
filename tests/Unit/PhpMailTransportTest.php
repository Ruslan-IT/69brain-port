<?php

namespace Tests\Unit;

use App\Mail\Transport\PhpMailTransport;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Tests\TestCase;

class PhpMailTransportTest extends TestCase
{
    public function test_transport_sends_through_php_mail_with_utf8_subject_and_body(): void
    {
        $calls = [];

        $transport = new class($calls) extends PhpMailTransport
        {
            public function __construct(private array &$calls)
            {
                parent::__construct('-finfo@prochnost102.ru');
            }

            protected function dispatchMail(
                string $to,
                string $subject,
                string $body,
                string $headers,
                ?string $additionalParameters,
            ): bool {
                $this->calls[] = compact('to', 'subject', 'body', 'headers', 'additionalParameters');

                return true;
            }
        };

        $email = (new Email)
            ->from(new Address('info@prochnost102.ru', 'КБ «Прочность»'))
            ->to('desk@example.ru')
            ->subject('Новая заявка с сайта КБ «Прочность»')
            ->text("Имя: Иван\nСообщение: нужен расчёт");

        $transport->send($email, Envelope::create($email));

        $this->assertCount(1, $calls);
        $this->assertSame('desk@example.ru', $calls[0]['to']);
        $this->assertSame('-finfo@prochnost102.ru', $calls[0]['additionalParameters']);
        $this->assertStringContainsString('=?utf-8?', strtolower($calls[0]['subject']));
        $this->assertStringContainsString('charset=utf-8', strtolower($calls[0]['headers']));
        $this->assertStringContainsString('quoted-printable', strtolower($calls[0]['headers']));
        $this->assertStringContainsString('info@prochnost102.ru', $calls[0]['headers']);
        $this->assertSame(
            "Имя: Иван\nСообщение: нужен расчёт",
            str_replace("\r\n", "\n", quoted_printable_decode($calls[0]['body'])),
        );
        $this->assertStringNotContainsString('ruslan.r.f@yandex.ru', strtolower(json_encode($calls[0], JSON_UNESCAPED_UNICODE)));
    }

    public function test_transport_failure_raises_transport_exception(): void
    {
        $transport = new class extends PhpMailTransport
        {
            public function __construct()
            {
                parent::__construct(null);
            }

            protected function dispatchMail(
                string $to,
                string $subject,
                string $body,
                string $headers,
                ?string $additionalParameters,
            ): bool {
                return false;
            }
        };

        $email = (new Email)
            ->from('info@prochnost102.ru')
            ->to('desk@example.ru')
            ->subject('Test')
            ->text('body');

        $this->expectException(TransportException::class);
        $transport->send($email, Envelope::create($email));
    }

    public function test_php_mailer_is_registered_in_laravel(): void
    {
        config([
            'mail.default' => 'php',
            'mail.mailers.php' => [
                'transport' => 'php',
                'additional_parameters' => '-finfo@prochnost102.ru',
            ],
            'mail.from.address' => 'info@prochnost102.ru',
            'mail.from.name' => 'КБ «Прочность»',
        ]);

        $transport = app('mail.manager')->mailer('php')->getSymfonyTransport();

        $this->assertInstanceOf(PhpMailTransport::class, $transport);
        $this->assertSame('php', (string) $transport);
    }
}
