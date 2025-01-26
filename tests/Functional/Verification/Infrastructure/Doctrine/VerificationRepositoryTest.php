<?php

namespace B24io\Checklist\Tests\Functional\Verification\Infrastructure\Doctrine;

use B24io\Checklist\Documents\Entity\Document;
use B24io\Checklist\Documents\Repository\DocumentRepositoryInterface;
use B24io\Checklist\Services\Flusher;
use B24io\Checklist\Verification\Entity\LanguageModel;
use B24io\Checklist\Verification\Entity\ProcessingStatus;
use B24io\Checklist\Verification\Entity\Rule;
use B24io\Checklist\Verification\Entity\RuleStatus;
use B24io\Checklist\Verification\Entity\Verification;
use B24io\Checklist\Verification\Repository\RuleRepositoryInterface;
use B24io\Checklist\Verification\Repository\VerificationRepositoryInterface;
use Carbon\CarbonImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class VerificationRepositoryTest extends KernelTestCase
{
    public function testSave(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        /**
         * @var VerificationRepositoryInterface $verificationRepo
         */
        $verificationRepo = $container->get(VerificationRepositoryInterface::class);
        /**
         * @var Flusher $flusher
         */
        $flusher = $container->get(Flusher::class);

        $verificationId = Uuid::v7();
        $clientId = Uuid::fromString('00000000-0000-0000-0000-000000000000');
        $documentIds = [Uuid::v7(), Uuid::v7(), Uuid::v7()];
        $ruleGroupId = Uuid::v7();

        $verificationRepo->save(
            new Verification(
                $verificationId,
                $clientId,
                new CarbonImmutable(),
                new CarbonImmutable(),
                $documentIds,
                $ruleGroupId,
                ProcessingStatus::new,
                LanguageModel::gpt4oMini20240718
            )
        );
        $flusher->flush();


        $saved = $verificationRepo->getById($verificationId);
        $this->assertEquals($documentIds, $saved->getDocumentIds());
        $this->assertEquals($ruleGroupId, $saved->getRuleGroupId());
        $this->assertEquals(ProcessingStatus::new, $saved->getProcessingStatus());
    }

    protected function setUp(): void
    {
    }
}
