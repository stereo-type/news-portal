<?php

/**
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Mercure;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class NewsLoadedPublisher
{
    private HubInterface $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }

    /**
     * @throws \JsonException
     */
    public function publish(int $count): void
    {
        $update = new Update(
            topics: 'news/loaded',
            data: json_encode(['count' => $count], JSON_THROW_ON_ERROR),
        );

        $this->hub->publish($update);
    }
}
