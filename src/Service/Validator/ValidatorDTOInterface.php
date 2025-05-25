<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace App\Service\Validator;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ValidatorDTOInterface
{
    public function validateDTO(object $dto, bool $partial = false): ConstraintViolationListInterface;
}
