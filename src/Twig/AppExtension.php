<?php

namespace App\Twig; 

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('stars', [$this, 'stars'], ['is_safe' => ['html']]),
        ];
    }

    public function stars($note)
    {
        $html = '';
        for ($i = 0; $i < $note; $i++) {
            $html .= '<i class="fas fa-star"></i>';
        }
        for ($i = 0; $i < 5 - $note; $i++) {
            $html .= '<i class="far fa-star"></i>';
        }

        return $html;
    }
}