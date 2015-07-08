<?php

namespace OldSound\RabbitMqBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to purge a queue
 */
class PurgeConsumerCommand extends ConsumerCommand
{
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Consumer Name')
             ->setDescription('Purge a consumer\'s queue')
             ->addOption('no-confirmation', null, InputOption::VALUE_NONE, 'Whether it must be confirmed before purging');

        $this->setName('rabbitmq:purge');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $noConfirmation = (bool) $input->getOption('no-confirmation');

        if (!$noConfirmation && $input->isInteractive()) {
            $confirmation = $this->getHelper('dialog')->askConfirmation($output, sprintf('<question>Are you sure you wish to purge "%s" queue? (y/n)</question>', $input->getArgument('name')), false);
            if (!$confirmation) {
                $output->writeln('<error>Purging cancelled!</error>');

                return 1;
            }
        }

        $this->consumer = $this->getContainer()
            ->get(sprintf($this->getConsumerService(), $input->getArgument('name')));
        $this->consumer->purge($input->getArgument('name'));
    }
}
