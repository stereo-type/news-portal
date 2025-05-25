<?php

/**
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Command;

use App\Service\NewsService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**php bin/console app:import-news*/
#[AsCommand(
    name: 'app:import-news',
    description: 'Импортирует свежие новости из внешнего API.',
)]
class ImportNewsCommand extends Command
{
    public function __construct(
        private readonly NewsService $service,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->service->loadNews();
        $output->writeln(sprintf('Импортировано %d новостей.', $result['totalLoaded']));
        $output->writeln(sprintf('Ошибок: %d', $result['errorsCount']));
        if (!empty($result['errors'])) {
            foreach ($result['errors'] as $error) {
                $output->writeln(sprintf('Ошибка: %s', $error));
            }
        }

        return Command::SUCCESS;
    }
}
