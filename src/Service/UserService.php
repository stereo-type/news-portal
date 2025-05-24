<?php
/**
 * @package    UserService.php
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{


    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function createUser(User $user): User
    {
        $user->setIsVerified(false);
        $user->setRegisteredAt(new \DateTimeImmutable());
        $user->setRoles(['ROLE_USER']);


        // TODO: generate code + send to queue




        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

}