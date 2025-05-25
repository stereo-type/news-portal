<?php

/**
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\SendTelegramConfirmationCode;
use App\Service\TelegramBotService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendTelegramConfirmationCodeHandler
{
    public function __construct(
        private TelegramBotService $botService,
    ) {
    }

    public function __invoke(SendTelegramConfirmationCode $message): void
    {
        $email = $message->getEmail();
        $code = $message->getCode();
        $text = "<b>[$email]</b>\n\nКод подтверждения: <code>$code</code>";
        $this->botService->sendMessage($text);
    }
}
