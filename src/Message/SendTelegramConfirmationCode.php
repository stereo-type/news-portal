<?php
/**
 * @package    SendTelegramConfirmationCode.php
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Message;

final class SendTelegramConfirmationCode
{
    public function __construct(
        private string $email,
        private string $code
    ) {}

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}