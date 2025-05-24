<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace App\Service\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class UniqueValue extends Constraint
{
    public string $entityClass;
    public string $field;

    public string $message = 'Объект "{{ value }}" уже используется';

    public function __construct(string $entityClass, string $field, ?string $message = null, mixed $options = null, ?array $groups = null, mixed $payload = null)
    {
        $this->entityClass = $entityClass;
        $this->field = $field;
        if ($message) {
            $this->message = $message;
        }
        parent::__construct(['entityClass' => $entityClass, 'field' => $field, 'message' => $this->message] + ($options ?? []), $groups, $payload);
    }

    public function getRequiredOptions(): array
    {
        return ['entityClass'];
    }
}
