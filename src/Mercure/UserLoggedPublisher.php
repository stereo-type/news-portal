<?php

/**
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Mercure;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class UserLoggedPublisher
{
    private HubInterface $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }

    public function publish(string $email): void
    {
        $update = new Update(
            topics: 'user/logged',
            data: json_encode([
                'email' => $email,
            ], JSON_THROW_ON_ERROR),
        );

        $this->hub->publish($update);
    }
}
