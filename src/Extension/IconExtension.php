<?php

/**
 * @copyright 03.06.2024 Zhalyaletdinov Vyacheslav evil_tut@mail.ru
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Extension;

use App\Service\IconService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IconExtension extends AbstractExtension
{
    public function __construct(private readonly IconService $iconService)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('icon', [$this, 'iconRender'], ['is_safe' => ['html']]),
        ];
    }

    public function iconRender(mixed ...$params): string
    {
        if (empty($params)) {
            throw new \InvalidArgumentException('Invalid parameters');
        }
        $icon = $params[0];
        $size = $params[1] ?? 24;
        $classes = $params[2] ?? '';
        $attributes = $params[3] ?? [];

        return $this->iconService->render($icon, $size, $classes, $attributes);
    }
}
