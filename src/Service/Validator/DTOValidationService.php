<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace App\Service\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class DTOValidationService implements ValidatorDTOInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validateDTO(object $dto, bool $partial = false): ConstraintViolationListInterface
    {
        $violations = $this->validator->validate($dto);

        if ($partial) {
            $violations = $this->filterPartialViolations($dto, $violations);
        }

        return $violations;
    }

    private function filterPartialViolations(object $dto, ConstraintViolationListInterface $violations): ConstraintViolationListInterface
    {
        $filtered = new ConstraintViolationList();

        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($violations as $violation) {
            $propertyPath = $violation->getPropertyPath();

            if ($accessor->isReadable($dto, $propertyPath)) {
                $propertyValue = $accessor->getValue($dto, $propertyPath);
                if (null !== $propertyValue) {
                    $filtered->add($violation);
                }
            }
        }

        return $filtered;
    }
}
