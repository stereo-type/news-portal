<?php
/**
 * @package    TelegramCodeGenerator.php
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\TelegramCode;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TelegramCodeGenerator
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function generateFor(User $user): TelegramCode
    {
        $code = new TelegramCode();
        $code->setUser($user);
        $code->setCode(random_int(100000, 999999));
        $code->setExpiresAt((new \DateTime())->modify('+10 minutes'));

        $this->entityManager->persist($code);
        $this->entityManager->flush();

        return $code;
    }
}