<?php

/**
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

readonly class IconService
{
    public function __construct(
        private Environment $twig,
        private KernelInterface $kernel,
    ) {
    }

    public function render(string $name, int $size = 24, string $classes = '', array $attributes = []): string
    {
        $path = $this->kernel->getProjectDir() . '/templates';
        $relPath = '/icons/' . $name . '.html.twig';
        $fullPath = $path . $relPath;
        if (file_exists($path . $relPath)) {
            return $this->twig->render(
                $relPath,
                ['size' => $size, 'classes' => $classes, 'attributes' => implode(' ', $attributes)]
            );
        }

        throw new FileNotFoundException("Icon '$name' not found in templates/icons directory.", path: $fullPath);
    }
}
