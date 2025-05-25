<?php

/**
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Mercure;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class UserCreatedPublisher
{
    private HubInterface $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }

    public function publish(int $userId, string $username): void
    {
        $update = new Update(
            topics: 'user/created',
            data: json_encode([
                'id' => $userId,
                'email' => $username,
            ], JSON_THROW_ON_ERROR),
        );

        $this->hub->publish($update);
    }
}
