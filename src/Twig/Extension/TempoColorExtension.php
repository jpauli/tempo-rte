<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\TempoColorExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TempoColorExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('tempo_color', [TempoColorExtensionRuntime::class, 'colorize'], ['is_safe' => ['html']]),
        ];
    }
}