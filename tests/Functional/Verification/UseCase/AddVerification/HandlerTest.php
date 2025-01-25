<?php

namespace B24io\Checklist\Tests\Functional\Verification\UseCase\AddVerification;

use B24io\Checklist\Documents\UseCase\AddNewDocument\Command;
use B24io\Checklist\Verification\Entity\LanguageModel;
use B24io\Checklist\Verification\Entity\RuleStatus;
use B24io\Checklist\Verification\Repository\RuleRepositoryInterface;
use B24io\Checklist\Verification\Repository\VerificationRepositoryInterface;
use B24io\Checklist\Verification\UseCase\AddRule;
use B24io\Checklist\Verification\UseCase\AddVerification;
use B24io\Checklist\Documents\UseCase\AddNewDocument;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class HandlerTest extends KernelTestCase
{
    /**
     * @param Command[] $addDocumentCmd
     * @param AddRule\Command[] $addRuleCmd
     * @param AddVerification\Command $addVerificationCmd
     * @return void
     */
    #[DataProvider('samplesDataProvider')]
    public function testAddVerification(
        array $addDocumentCmd,
        array $addRuleCmd,
        AddVerification\Command $addVerificationCmd
    ): void {
        self::bootKernel();
        $container = static::getContainer();

        /**
         * @var AddRule\Handler $addRuleHandler
         */
        $addRuleHandler = $container->get(AddRule\Handler::class);
        /**
         * @var VerificationRepositoryInterface $verificationRepo
         */
        $verificationRepo = $container->get(VerificationRepositoryInterface::class);
        /**
         * @var AddNewDocument\Handler $addDocumentHandler
         */
        $addDocumentHandler = $container->get(AddNewDocument\Handler::class);

        // add documents
        foreach ($addDocumentCmd as $cmd) {
            $addDocumentHandler->handle($cmd);
        }
        // add rules
        foreach ($addRuleCmd as $cmd) {
            $addRuleHandler->handle($cmd);
        }
        // add verification
        /**
         * @var AddVerification\Handler $addVerificationHandler
         */
        $addVerificationHandler = $container->get(AddVerification\Handler::class);
        $addVerificationHandler->handle($addVerificationCmd);

        $verification = $verificationRepo->getById($addVerificationCmd->id);

        //verification contains added documents
        $this->assertEquals(
            array_map(static fn($cmd) => $cmd->id, $addDocumentCmd),
            $verification->getDocumentIds()
        );
    }

    public static function samplesDataProvider(): \Generator
    {
        $filename = dirname(__DIR__, 5) . '/fixtures/documents/2308282665/policy.md';
        $documentText = file_get_contents($filename);

        $clientId = Uuid::v7();
        $documentTypeId = Uuid::fromString('11111111-0000-0000-0000-000000000000');
        $rulesGroupId = Uuid::fromString('99999999-0000-0000-0000-000000000000');

        $documentId = Uuid::v7();

        $data = [
            [
                new AddNewDocument\Command($documentId, $clientId, $documentTypeId, $documentText)
            ],
            [
                new AddRule\Command(
                    Uuid::v7(),
                    $clientId,
                    $rulesGroupId,
                    [$documentTypeId],
                    RuleStatus::active,
                    'rule 1 name',
                    'rule 1 body',
                    'prompt 1 template',
                    10,
                    'rule 1 comment'
                )
            ],
            new AddVerification\Command(
                Uuid::v7(),
                $clientId,
                [$documentId],
                $rulesGroupId,
                LanguageModel::openAiGpt4o
            )
        ];
        yield 'one document one rule' => $data;
    }
}
