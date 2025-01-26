<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Infrastructure\Console;

use B24io\Checklist\Services\Models\RuleChecker;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use B24io\Checklist\Documents;
use B24io\Checklist\Verification;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Uid\Uuid;

#[AsCommand(name: 'app:validate-document')]
class ValidateDocumentCommand extends Command
{
    public function __construct(
        private readonly Documents\UseCase\AddNewDocument\Handler $addDocumentHandler,
        private readonly Documents\Repository\DocumentRepositoryInterface $documentRepository,
        private readonly Verification\Repository\RuleRepositoryInterface $ruleRepository,
        private readonly Verification\UseCase\AddVerification\Handler $addVerificationHandler,
        private readonly Verification\Repository\VerificationStepRepositoryInterface $verificationStepRepository,
        private readonly RuleChecker $ruleChecker,
        private readonly Filesystem $filesystem,
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
                'path to markdown file for validation'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fileName = (string)$input->getOption('file');
        if (!$this->filesystem->exists($fileName)) {
            $output->writeln(sprintf('<error>file «%s» not found</error>', $fileName));
            return Command::INVALID;
        }


        $output->writeln(['', '<info>Правила для проверки</info>',]);
        $groupId = Uuid::fromString('00000000-0000-0000-1000-000000000000');
        $rules = $this->ruleRepository->getByRuleGroupId($groupId);
        foreach ($rules as $rule) {
            $output->writeln(
                sprintf(
                    '%s | %s | %s',
                    $rule->getId(),
                    $rule->getName(),
                    $rule->getRule()
                )
            );
        }
        $output->writeln('');


        $documentId = Uuid::v7();
        $clientId = Uuid::fromString('00000000-0000-0000-0000-000000000000');
        $documentTypeId = Uuid::fromString('00000001-0000-0000-0000-000000000000');

        $this->addDocumentHandler->handle(
            new Documents\UseCase\AddNewDocument\Command(
                $documentId,
                $clientId,
                $documentTypeId,
                $this->filesystem->readFile($fileName)
            )
        );

        $document = $this->documentRepository->getById($documentId);
        $output->writeln(sprintf('<info>document loaded, id: %s </info>', $document->getId()->toRfc4122()));

        $verificationId = Uuid::v7();
        $this->addVerificationHandler->handle(
            new Verification\UseCase\AddVerification\Command(
                $verificationId,
                $clientId,
                [$documentId],
                $groupId,
                Verification\Entity\LanguageModel::gpt4oMini20240718
            )
        );

        $output->writeln(['', '<info>verification command registered</info>']);


        $steps = $this->verificationStepRepository->getByVerificationId($verificationId);

        foreach ($steps as $step) {
            $output->writeln(
                sprintf(
                    'step id %s | verification id %s |rule id %s ',
                    $step->getId()->toRfc4122(),
                    $step->getVerificationId()->toRfc4122(),
                    $step->getRuleId()->toRfc4122()
                )
            );

            $this->ruleChecker->run($step);

        }

        return Command::SUCCESS;
    }
}