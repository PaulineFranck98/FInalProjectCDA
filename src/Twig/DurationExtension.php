<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DurationExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('duration_label', [$this, 'getDurationLabel']),
        ];
    }

    public function getDurationLabel($onSiteTime, string $format = 'long'): string
    {
        $labels = [
            '1'  => ['short' => '1h',  'long' => '1h'],
            '2'  => ['short' => '2h',  'long' => '2h'],
            '3'  => ['short' => '3h',  'long' => '3h'],
            '12' => ['short' => '12h', 'long' => '1/2 journée'],
            '24' => ['short' => '24h', 'long' => '1 journée et +'],
        ];

        if (isset($labels[$onSiteTime])) {
            return $labels[$onSiteTime][$format] ?? $labels[$onSiteTime]['long'];
        }
        return (string)$onSiteTime;
    }
}