<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Service\Validator\Constraints as CustomAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Email не может быть пустым')]
    #[Assert\Email(message: 'Некорректный email адрес')]
    #[CustomAssert\UniqueValue(entityClass: User::class, field: 'email', message: 'Email "{{ value }}" уже используется')]
    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    #[Assert\NotBlank(message: 'Пароль не может быть пустым')]
    #[Assert\Length(
        min: 6,
        minMessage: 'Пароль должен содержать минимум {{ limit }} символов'
    )]
    #[ORM\Column]
    private string $password;

    #[ORM\Column(options: ['default' => false])]
    private bool $isVerified = false;

    #[ORM\Column]
    private \DateTimeImmutable $registeredAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastLoginAt = null;

    /**
     * @var Collection<int, TelegramCode>
     */
    #[ORM\OneToMany(targetEntity: TelegramCode::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $telegramCodes;

    public function __construct()
    {
        $this->telegramCodes = new ArrayCollection();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): void
    {
        $this->isVerified = $isVerified;
    }

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?\DateTimeImmutable $lastLoginAt): void
    {
        $this->lastLoginAt = $lastLoginAt;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getRegisteredAt(): \DateTimeImmutable
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(\DateTimeImmutable $registeredAt): void
    {
        $this->registeredAt = $registeredAt;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return Collection<int,TelegramCode>
     */
    public function getTelegramCodes(): Collection
    {
        return $this->telegramCodes;
    }

    /**
     * @param Collection<int,TelegramCode> $telegramCodes
     */
    public function setTelegramCodes(Collection $telegramCodes): void
    {
        $this->telegramCodes = $telegramCodes;
    }

    public function addTelegramCode(TelegramCode $telegramCode): static
    {
        if (!$this->telegramCodes->contains($telegramCode)) {
            $this->telegramCodes->add($telegramCode);
            $telegramCode->setUser($this);
        }

        return $this;
    }

    public function removeTelegramCode(TelegramCode $telegramCode): static
    {
        if ($this->telegramCodes->removeElement($telegramCode)) {
            // set the owning side to null (unless already changed)
            if ($telegramCode->getUser() === $this) {
                $telegramCode->setUser(null);
            }
        }

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }
}
