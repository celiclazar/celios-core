<?php

namespace Celios\Core\Support;

class Translations
{
    public static function labels(): array
    {
        return [
            'sr' => [
                'page_title' => 'Naslov stranice',
                'heading' => 'Naslov',
                'subtitle' => 'Podnaslov',
                'content' => 'Sadržaj',
                'caption' => 'Opis slike',
                'alt' => 'Alt tekst',
                'button_text' => 'Tekst dugmeta',
                'question' => 'Pitanje',
                'answer' => 'Odgovor',
                'description' => 'Opis',
                'label' => 'Naziv',
            ],

            'en' => [
                'page_title' => 'Page Title',
                'heading' => 'Heading',
                'subtitle' => 'Subtitle',
                'content' => 'Content',
                'caption' => 'Caption',
                'alt' => 'Alt Text',
                'button_text' => 'Button Text',
                'question' => 'Question',
                'answer' => 'Answer',
                'description' => 'Description',
                'label' => 'Label',
            ],

            'it' => [
                'page_title' => 'Titolo della pagina',
                'heading' => 'Titolo',
                'subtitle' => 'Sottotitolo',
                'content' => 'Contenuto',
                'caption' => 'Didascalia',
                'alt' => 'Testo Alt',
                'button_text' => 'Testo Pulsante',
                'question' => 'Domanda',
                'answer' => 'Risposta',
                'description' => 'Descrizione',
                'label' => 'Etichetta',
            ],
        ];
    }

    public static function get(string $lang, string $key): string
    {
        return self::labels()[$lang][$key] ?? $key;
    }
}
