<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Infrastructure\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use B24io\Checklist\Verification;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Uid\Uuid;
use League\Csv\Reader;


#[AsCommand(name: 'app:rules:load')]
class LoadRulesCommand extends Command
{
    public function __construct(
        private readonly Verification\UseCase\AddRule\Handler $addRuleHandler,
        private readonly Verification\Repository\RuleRepositoryInterface $ruleRepository,
        private Filesystem $filesystem,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'file',
                null,
                InputOption::VALUE_REQUIRED,
                'path to csv-file with validation rules'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fileName = (string)$input->getOption('file');
        if (!$this->filesystem->exists($fileName)) {
            $output->writeln(sprintf('<error>file «%s» not found</error>', $fileName));
            return Command::INVALID;
        }

        //load the CSV document from a file path
        $csv = Reader::createFromPath($fileName, 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();


        $clientId = Uuid::fromString('00000000-0000-0000-0000-000000000000');
        $groupId = Uuid::fromString('00000000-0000-0000-1000-000000000000');
        $documentTypeId = Uuid::fromString('00000001-0000-0000-0000-000000000000');

        foreach ($records as $record) {
            $this->addRuleHandler->handle(
                new Verification\UseCase\AddRule\Command(
                    Uuid::v7(),
                    $clientId,
                    $groupId,
                    [$documentTypeId],
                    Verification\Entity\RuleStatus::active,
                    $record['name'],
                    $record['rule'],
                    $record['prompt'],
                    (int)$record['weight'],
                    $record['comment'],
                )
            );
        }

        // rules
        $rules = $this->ruleRepository->getByRuleGroupId($groupId);

        foreach ($rules as $rule) {
            $output->writeln(
                sprintf(
                    '%s | %s',
                    $rule->getId()->toString(),
                    $rule->getName()
                )
            );
        }


        return Command::SUCCESS;
    }
}