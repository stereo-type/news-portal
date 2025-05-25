<?php

/**
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Admin;

use App\Entity\NewsItem;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * @extends AbstractAdmin<NewsItem>
 */
class NewsAdmin extends AbstractAdmin
{
    use AdminTrait;

    protected function configureFormFields(FormMapper $form): void
    {
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('title')
            ->add('description')
            ->add('sourceName');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('title')
            ->add('sourceName')
            ->add('description')
            ->add('url')
            ->add('publishedAt');
    }

    protected function configureShowFields(ShowMapper $show): void
    {
    }
}
