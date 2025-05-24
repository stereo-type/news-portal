<?php

/**
 * @copyright  2024 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace App\Service\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MustExistValidator extends ConstraintValidator
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof MustExist) {
            throw new UnexpectedTypeException($constraint, MustExist::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $repository = $this->entityManager->getRepository($constraint->entityClass);

        $criteria = [$constraint->field => $value];

        $entity = $repository->findOneBy($criteria);

        if (!$entity) {
            $this->context->buildViolation($constraint->message)
                          ->setParameter('{{ entity }}', $constraint->entityClass)
                          ->setParameter('{{ value }}', (string)$value)
                          ->addViolation();
        }
    }
}
