<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\RichEditor;

class RichText
{
    public static function make(string $name): RichEditor
    {
        return RichEditor::make($name)
            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link'])
            ->fileAttachments(false)
            ->linkProtocols(['http', 'https', 'mailto', 'tel']);
    }
}
