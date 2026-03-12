<?php

namespace App\Command;

use App\Service\OrderQueueService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:process-orders')]
class ProcessOrdersCommand extends Command
{

    public function __construct(
        private OrderQueueService $queueService,
        private LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $queueLength = $this->queueService->getQueueLength();

        if ($queueLength > 10) {
            $this->logger->warning('Pizza queue is long', [
                'queue_length' => $queueLength
            ]);
        }

        $order = $this->queueService->processNextOrder();

        if (!$order) {

            $output->writeln('No orders');

            return Command::SUCCESS;
        }

        $output->writeln(
            'Order processed: ' . $order->getId()
        );

        return Command::SUCCESS;
    }
}