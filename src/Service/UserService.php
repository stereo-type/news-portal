<?php

/**
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Mercure\UserCreatedPublisher;
use App\Message\SendTelegramConfirmationCode;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TelegramCodeGenerator $codeGenerator,
        private MessageBusInterface $bus,
        private UserCreatedPublisher $publisher,
        private LoggerInterface $logger,
    ) {
    }

    public function createUser(User $user): User
    {
        $user->setIsVerified(false);
        $user->setRegisteredAt(new \DateTimeImmutable());
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $code = $this->codeGenerator->generateFor($user);
        $this->bus->dispatch(new SendTelegramConfirmationCode($user->getEmail(), $code->getCode()));

        try {
            $this->publisher->publish($user->getId(), $user->getEmail());
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }

        return $user;
    }

    public function verifyUser(int $userId, string $code): void
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            throw new UserNotFoundException('Пользователь не найден');
        }
        if ($user->isVerified()) {
            throw new \InvalidArgumentException('Пользователь уже подтвержден');
        }
        $codes = $user->getTelegramCodes();

        $currentCode = null;
        foreach ($codes as $c) {
            if ($c->getCode() === $code) {
                $currentCode = $c;
                break;
            }
        }
        if (null === $currentCode) {
            throw new \InvalidArgumentException('Не верный код подтверждения');
        }

        if (null !== $currentCode->getUsedAt()) {
            throw new \InvalidArgumentException('Код уже использован');
        }
        if ($currentCode->getExpiresAt() < new \DateTimeImmutable()) {
            throw new \InvalidArgumentException('Время действия кода вышло');
        }

        $currentCode->setUsedAt(new \DateTimeImmutable());
        $user->setIsVerified(true);

        $this->entityManager->flush();
    }
}
