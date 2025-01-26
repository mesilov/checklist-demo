<?php

namespace B24io\Checklist\Tests\Functional\Verification\UseCase\AddRule;

use B24io\Checklist\Verification\Entity\RuleStatus;
use B24io\Checklist\Verification\Repository\RuleRepositoryInterface;
use B24io\Checklist\Verification\UseCase\AddRule;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class HandlerTest extends KernelTestCase
{
    public function testSave(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        /**
         * @var AddRule\Handler $addRuleHandler
         */
        $addRuleHandler = $container->get(AddRule\Handler::class);
        /**
         * @var RuleRepositoryInterface $ruleRepo
         */
        $ruleRepo = $container->get(RuleRepositoryInterface::class);

        $ruleId = Uuid::v7();
        $clientId = Uuid::fromString('00000000-0000-0000-0000-000000000000');
        $groupId = Uuid::fromString('00000000-0000-0000-0000-000000000000');
        $documentTypeIds = [
            Uuid::fromString('00000000-0000-0000-0000-000000000000')
        ];

        $ruleName = 'rule name';
        $ruleBody = 'rule body';
        $prompt = 'prompt';
        $weight = 10;
        $comment = 'comment';
        $ruleStatus = RuleStatus::active;
        $expectedResult = true;

        $addRuleHandler->handle(
            new AddRule\Command(
                $ruleId,
                $clientId,
                $groupId,
                $documentTypeIds,
                $ruleStatus,
                $ruleName,
                $ruleBody,
                $prompt,
                $expectedResult,
                $weight,
                $comment
            )
        );

        $saved = $ruleRepo->getById($ruleId);
        $this->assertEquals($ruleName, $saved->getName());
        $this->assertEquals($ruleBody, $saved->getRule());
        $this->assertEquals($prompt, $saved->getPrompt());
        $this->assertEquals($weight, $saved->getWeight());
        $this->assertEquals($comment, $saved->getComment());
        $this->assertEquals($expectedResult, $saved->getExpectedResult());
    }
}
