<?php

namespace App\Mail\Transport;

use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\MessageConverter;

/**
 * Доставка через встроенную PHP-функцию mail(), без SMTP и без sendmail-пайпа.
 */
class PhpMailTransport extends AbstractTransport
{
    public function __construct(
        private readonly ?string $additionalParameters = null,
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $to = implode(', ', array_map(
            static fn (Address $address): string => $address->getAddress(),
            $message->getEnvelope()->getRecipients(),
        ));

        if ($to === '') {
            throw new TransportException('PHP mail() transport requires at least one recipient.');
        }

        $subjectHeader = $email->getHeaders()->get('Subject');
        $subject = $subjectHeader ? $subjectHeader->getBodyAsString() : '';

        $raw = $email->toString();

        if (! str_contains($raw, "\r\n\r\n")) {
            throw new TransportException('Unable to parse MIME message for PHP mail().');
        }

        [$rawHeaders, $body] = explode("\r\n\r\n", $raw, 2);
        $headers = $this->headersWithoutMailFunctionArguments($rawHeaders);

        if (! $this->dispatchMail($to, $subject, $body, $headers, $this->safeAdditionalParameters())) {
            throw new TransportException(sprintf('Unable to send email to [%s] using PHP mail().', $to));
        }
    }

    /**
     * To/Subject передаются отдельными аргументами mail(); Bcc не должен уходить в заголовках.
     */
    private function headersWithoutMailFunctionArguments(string $rawHeaders): string
    {
        $lines = preg_split("/\r\n(?![ \t])/", $rawHeaders) ?: [];
        $kept = [];

        foreach ($lines as $line) {
            if ($line === '' || preg_match('/^(To|Subject|Bcc):/i', $line) === 1) {
                continue;
            }

            $kept[] = $line;
        }

        return implode("\r\n", $kept);
    }

    /**
     * @internal Overridable in tests.
     */
    protected function dispatchMail(
        string $to,
        string $subject,
        string $body,
        string $headers,
        ?string $additionalParameters,
    ): bool {
        if ($additionalParameters !== null && $additionalParameters !== '') {
            return mail($to, $subject, $body, $headers, $additionalParameters);
        }

        return mail($to, $subject, $body, $headers);
    }

    private function safeAdditionalParameters(): ?string
    {
        $parameters = trim((string) $this->additionalParameters);

        if ($parameters === '') {
            return null;
        }

        // Только безопасный envelope-sender вида -fuser@domain — без произвольных shell-аргументов.
        if (! preg_match('/^-f[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}$/i', $parameters)) {
            throw new TransportException('MAIL_ADDITIONAL_PARAMETERS must be a -f envelope sender address.');
        }

        return $parameters;
    }

    public function __toString(): string
    {
        return 'php';
    }
}
