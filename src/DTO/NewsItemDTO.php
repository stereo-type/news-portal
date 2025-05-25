<?php

/**
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class NewsItemDTO
{
    #[Assert\NotBlank(message: 'Заголовок не может быть пустым')]
    private string $title;

    #[Assert\NotBlank(message: 'Описание не может быть пустым')]
    private string $description;

    #[Assert\NotBlank(message: 'Контент не может быть пустым')]
    private string $content;
    #[Assert\NotBlank(message: 'Ссылка не может быть пустой')]
    private string $url;

    #[Assert\NotBlank(message: 'Картинка не может быть пустой')]
    private string $image;

    #[Assert\NotBlank(message: 'Дата обязательна')]
    private \DateTimeImmutable $publishedAt;

    #[Assert\NotBlank(message: 'Название источника обязательно')]
    private string $sourceName;

    #[Assert\NotBlank(message: 'Ссылка на источник обязательно')]
    private string $sourceUrl;

    public static function fromArray(array $data): self
    {
        $instance = new self();
        foreach ($data as $property => $value) {
            if (property_exists($instance, $property)) {
                if ('publishedAt' === $property) {
                    $instance->publishedAt = new \DateTimeImmutable($value);
                } else {
                    $instance->{$property} = $value;
                }
            } elseif ('source' === $property) {
                $instance->sourceName = $value['name'];
                $instance->sourceUrl = $value['url'];
            }
        }

        return $instance;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function getPublishedAt(): \DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeImmutable $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getSourceName(): string
    {
        return $this->sourceName;
    }

    public function setSourceName(string $sourceName): void
    {
        $this->sourceName = $sourceName;
    }

    public function getSourceUrl(): string
    {
        return $this->sourceUrl;
    }

    public function setSourceUrl(string $sourceUrl): void
    {
        $this->sourceUrl = $sourceUrl;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}
