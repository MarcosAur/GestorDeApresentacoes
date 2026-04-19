<?php

namespace App\Helpers;

class FormatHelper
{
    /**
     * Formata uma nota decimal para o padrão brasileiro (ex: 9,50).
     *
     * @param float|null $value
     * @param int $decimals
     * @return string
     */
    public static function formatScore(?float $value, int $decimals = 2): string
    {
        if ($value === null) {
            return '-';
        }
        return number_format($value, $decimals, ',', '.');
    }

    /**
     * Formata uma string para exibição limpa.
     *
     * @param string|null $text
     * @return string
     */
    public static function cleanString(?string $text): string
    {
        return trim(strip_tags($text ?? ''));
    }
}
