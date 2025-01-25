<?php

namespace B24io\Checklist\Tests\Functional\Verification\UseCase\AddVerification;

use B24io\Checklist\Documents\UseCase\AddNewDocument\Command;
use B24io\Checklist\Verification\Entity\LanguageModel;
use B24io\Checklist\Verification\Entity\RuleStatus;
use B24io\Checklist\Verification\Repository\RuleRepositoryInterface;
use B24io\Checklist\Verification\Repository\VerificationRepositoryInterface;
use B24io\Checklist\Verification\Repository\VerificationStepRepositoryInterface;
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
         * @var VerificationRepositoryInterface $verificationRepo
         */
        $verificationRepo = $container->get(VerificationRepositoryInterface::class);
        /**
         * @var RuleRepositoryInterface $ruleRepo
         */
        $ruleRepo = $container->get(RuleRepositoryInterface::class);
        /**
         * @var VerificationStepRepositoryInterface $verificationStepRepo
         */
        $verificationStepRepo = $container->get(VerificationStepRepositoryInterface::class);
        /**
         * @var AddRule\Handler $addRuleHandler
         */
        $addRuleHandler = $container->get(AddRule\Handler::class);
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

        $verification = $verificationRepo->getById($addVerificationCmd->verificationId);

        //verification contains added documents
        $this->assertEquals(
            array_map(static fn($cmd) => $cmd->id, $addDocumentCmd),
            $verification->getDocumentIds()
        );

        // verification contains target rule group
        $this->assertEquals(
            $addVerificationCmd->ruleGroupId,
            $verification->getRuleGroupId()
        );

        // verification produce N verification steps
        // where N === Rules count
        $rules = $ruleRepo->getByRuleGroupId($addVerificationCmd->ruleGroupId);
        $verificationSteps = $verificationStepRepo->getByVerificationId($verification->getId());
        $this->assertEquals(
            array_map(static fn($rule) => $rule->getId(), $rules),
            array_map(static fn($step) => $step->getRuleId(), $verificationSteps)
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
        yield '1 document 1 rule' => $data;

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
                ),
                new AddRule\Command(
                    Uuid::v7(),
                    $clientId,
                    $rulesGroupId,
                    [$documentTypeId],
                    RuleStatus::active,
                    'rule 2 name',
                    'rule 2 body',
                    'prompt 2 template',
                    10,
                    'rule 2 comment'
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
        yield '1 document 2 rules' => $data;

        $documentPolicy = file_get_contents(dirname(__DIR__, 5) . '/fixtures/documents/2308282665/policy.md');
        $documentAgreement = file_get_contents(dirname(__DIR__, 5) . '/fixtures/documents/2308282665/agreement.md');

        $clientId = Uuid::v7();
        $documentPolicyTypeId = Uuid::fromString('10000000-0000-0000-0000-000000000000');
        $documentAgreementTypeId = Uuid::fromString('20000000-0000-0000-0000-000000000000');
        $rulesGroupId = Uuid::fromString('99999999-0000-0000-0000-000000000000');

        $documentPolicyId = Uuid::v7();
        $documentAgreementId = Uuid::v7();

        $data = [
            [
                new AddNewDocument\Command($documentPolicyId, $clientId, $documentPolicyTypeId, $documentPolicy),
                new AddNewDocument\Command($documentAgreementId, $clientId, $documentAgreementTypeId, $documentAgreement)
            ],
            [
                new AddRule\Command(
                    Uuid::v7(),
                    $clientId,
                    $rulesGroupId,
                    [$documentPolicyTypeId, $documentAgreementTypeId],
                    RuleStatus::active,
                    'rule 1 name',
                    'rule 1 body',
                    'prompt 1 template',
                    10,
                    'rule 1 comment'
                ),
            ],
            new AddVerification\Command(
                Uuid::v7(),
                $clientId,
                [$documentPolicyId, $documentAgreementId],
                $rulesGroupId,
                LanguageModel::openAiGpt4o
            )
        ];
        yield '2 documents 1 rule' => $data;

        $documentPolicy = file_get_contents(dirname(__DIR__, 5) . '/fixtures/documents/2308282665/policy.md');
        $documentAgreement = file_get_contents(dirname(__DIR__, 5) . '/fixtures/documents/2308282665/agreement.md');

        $clientId = Uuid::v7();
        $documentPolicyTypeId = Uuid::fromString('10000000-0000-0000-0000-000000000000');
        $documentAgreementTypeId = Uuid::fromString('20000000-0000-0000-0000-000000000000');
        $rulesGroupId = Uuid::fromString('99999999-0000-0000-0000-000000000000');

        $documentPolicyId = Uuid::v7();
        $documentAgreementId = Uuid::v7();

        $data = [
            [
                new AddNewDocument\Command($documentPolicyId, $clientId, $documentPolicyTypeId, $documentPolicy),
                new AddNewDocument\Command($documentAgreementId, $clientId, $documentAgreementTypeId, $documentAgreement)
            ],
            [
                new AddRule\Command(
                    Uuid::v7(),
                    $clientId,
                    $rulesGroupId,
                    [$documentPolicyTypeId, $documentAgreementTypeId],
                    RuleStatus::active,
                    'rule 1 name - check two documents',
                    'both documents must contain word «cat»',
                    'prompt 1 template',
                    10,
                    'rule 1 comment'
                ),
                new AddRule\Command(
                    Uuid::v7(),
                    $clientId,
                    $rulesGroupId,
                    [$documentPolicyTypeId],
                    RuleStatus::active,
                    'rule 2 name - check one document',
                    'document must contain word «black cat»',
                    'prompt 2 template',
                    10,
                    'rule 2 comment'
                ),
            ],
            new AddVerification\Command(
                Uuid::v7(),
                $clientId,
                [$documentPolicyId, $documentAgreementId],
                $rulesGroupId,
                LanguageModel::openAiGpt4o
            )
        ];
        yield '2 documents 2 rules' => $data;
    }
}
