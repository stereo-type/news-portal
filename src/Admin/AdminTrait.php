<?php

/**
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Admin;

trait AdminTrait
{
    protected function configureBatchActions(array $actions): array
    {
        unset($actions['delete']);

        return $actions;
    }

    protected function configureActionButtons(array $buttonList, string $action, ?object $object = null): array
    {
        unset($buttonList['create']);

        return $buttonList;
    }
}
