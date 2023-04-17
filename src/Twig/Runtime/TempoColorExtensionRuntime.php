<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class TempoColorExtensionRuntime implements RuntimeExtensionInterface
{
    private const COLORS = ["bleu" => "blue", "blanc" => "#FFEBCD", "rouge" => "red"];
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function colorize($value): string
    {
        if (isset(self::COLORS[$value])) {
            return sprintf("<span style='color:%s'>%s</span>", self::COLORS[$value], $value);
        } else {
            return $value;
        }
    }
}