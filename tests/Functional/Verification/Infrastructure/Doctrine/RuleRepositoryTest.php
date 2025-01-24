<?php

namespace B24io\Checklist\Tests\Functional\Verification\Infrastructure\Doctrine;

use B24io\Checklist\Documents\Entity\Document;
use B24io\Checklist\Documents\Repository\DocumentRepositoryInterface;
use B24io\Checklist\Services\Flusher;
use B24io\Checklist\Verification\Entity\Rule;
use B24io\Checklist\Verification\Entity\RuleStatus;
use B24io\Checklist\Verification\Repository\RuleRepositoryInterface;
use Carbon\CarbonImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class RuleRepositoryTest extends KernelTestCase
{
    public function testSave(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        /**
         * @var RuleRepositoryInterface $ruleRepo
         */
        $ruleRepo = $container->get(RuleRepositoryInterface::class);
        /**
         * @var Flusher $flusher
         */
        $flusher = $container->get(Flusher::class);

        $rule = new Rule(
            Uuid::v7(),
            Uuid::fromString('00000000-0000-0000-0000-000000000000'),
            Uuid::fromString('00000000-0000-0000-0000-000000000000'),
            [
                Uuid::v7(),
                Uuid::v7()
            ],
            new CarbonImmutable(),
            new CarbonImmutable(),
            RuleStatus::draft,
            'rule name',
            'default rule content',
            'find default rule content',
            10,
            'comment'
        );

        $ruleRepo->save($rule);
        $flusher->flush();

        $saved = $ruleRepo->getById($rule->getId());
        $this->assertEquals($rule->getRule(), $saved->getRule());
        $this->assertEquals($rule->getName(), $saved->getName());
        $this->assertEquals($rule->getPrompt(), $saved->getPrompt());
    }

    protected function setUp(): void
    {
    }
}
