<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace App\Service\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class MustExist extends Constraint
{
    public string $entityClass;
    public string $field;

    public string $message = 'Объект "{{ entity }}" "{{ value }}" не существует';

    public function __construct(string $entityClass, string $field = 'id', ?string $message = null, mixed $options = null, ?array $groups = null, mixed $payload = null)
    {
        if ($message) {
            $this->message = $message;
        }
        $this->entityClass = $entityClass;
        $this->field = $field;
        parent::__construct(['entityClass' => $entityClass, 'field' => $field, 'message' => $this->message] + ($options ?? []), $groups, $payload);
    }

    public function getRequiredOptions(): array
    {
        return ['entityClass'];
    }
}
