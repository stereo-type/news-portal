<?php
/**
 * @package    LoginTypeForm.php
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginTypeForm  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Email',
                'attr' => ['class' => 'form-control', 'autofocus' => true],
                'label_attr' => [
                    'class' => 'form-label',
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Пароль',
                'attr' => ['class' => 'form-control'],
                'label_attr' => [
                    'class' => 'form-label',
                ],
            ])
            ->add('login', SubmitType::class, [
                'label' => 'Войти',
                'attr' => ['class' => 'btn btn-primary w-100'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}