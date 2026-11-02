<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class SafeHtml
{
    public static function clean(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        if (! str_contains($html, '<')) {
            $plain = trim(strip_tags($html));

            return $plain === '' ? '' : nl2br(e($html), false);
        }

        $html = strip_tags($html, '<p><br><strong><b><em><i><ul><ol><li><a><span>');

        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="utf-8"><body>'.$html.'</body>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $body = $document->getElementsByTagName('body')->item(0);

        if (! $body instanceof DOMElement) {
            return '';
        }

        self::sanitize($body);

        $result = '';

        foreach ($body->childNodes as $child) {
            $result .= $document->saveHTML($child);
        }

        $result = trim($result);

        if (trim(html_entity_decode(strip_tags($result))) === '') {
            return '';
        }

        return $result;
    }

    private static function sanitize(DOMNode $node): void
    {
        if ($node->hasChildNodes()) {
            for ($index = $node->childNodes->length - 1; $index >= 0; $index--) {
                $child = $node->childNodes->item($index);

                if ($child instanceof DOMNode) {
                    self::sanitize($child);
                }
            }
        }

        if (! $node instanceof DOMElement) {
            return;
        }

        $allowed = match (strtolower($node->tagName)) {
            'a' => ['href'],
            'span' => ['lang'],
            default => [],
        };

        $values = [];

        if ($node->hasAttributes()) {
            $remove = [];

            foreach ($node->attributes as $attribute) {
                $name = strtolower($attribute->name);

                if (! in_array($name, $allowed, true)) {
                    $remove[] = $attribute->name;

                    continue;
                }

                $values[$name] = $attribute->value;
            }

            foreach ($remove as $name) {
                $node->removeAttribute($name);
            }
        }

        if (strtolower($node->tagName) === 'a') {
            $href = SiteUrl::href($values['href'] ?? null);

            if ($href === null) {
                $node->removeAttribute('href');
            } else {
                $node->setAttribute('href', $href);
            }
        }

        if (strtolower($node->tagName) === 'span') {
            $lang = $values['lang'] ?? '';

            if (preg_match('/^[a-zA-Z]{2,8}(?:-[a-zA-Z0-9]{2,8})?$/', $lang) !== 1) {
                $node->removeAttribute('lang');
            }
        }
    }
}
