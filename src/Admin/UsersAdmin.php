<?php
/**
 * @package    UsersAdmin.php
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class UsersAdmin extends AbstractAdmin
{
    use AdminTrait;


    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('email')
            ->add('password')
            ->add('isVerified');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('email')
            ->add('isVerified', null, [
                'label' => 'Verified',
            ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('email')
            ->add('isVerified', null, [
                'editable' => true,
                'label' => 'Verified',
            ])
            ->add('registeredAt', null, [
                'label' => 'Registered at',
            ])
            ->add('lastLoginAt', null, [
                'label' => 'Last login at',
            ]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('email')
            ->add('isVerified')
            ->add('registeredAt', null, ['label' => 'Registered at'])
            ->add('lastLoginAt', null, ['label' => 'Last login at'])
            ->add('telegramCodes', null, [
                'label' => 'Коды Telegram',
                'template' => 'admin/fields/telegram_codes_show.html.twig',
            ]);
    }
}