<?php
/**
 * @package    TelegramBotServce.php
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TelegramBotService
{
    private string $token;
    private string $chatId;

    public function __construct(
        string $token,
        string $chatId,
        private readonly HttpClientInterface $client,
    ) {
        $this->token = $token;
        $this->chatId = $chatId;
    }

    /**
     * Отправляет сообщение в Telegram
     */
    public function sendMessage(string $message): void
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";
        $client = $this->client;
        try {
            $client->request('POST', $url, [
                'json' => [
                    'chat_id' => $this->chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ],
            ]);
        } catch (\Throwable $e) {
           if($e instanceof ClientException) {
               dd($e->getMessage());
           }
            throw new \RuntimeException("Ошибка при отправке в Telegram: " . $e->getMessage());
        }
    }
}