<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Controller;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use B24io\Checklist\Verification;

class RulesController extends AbstractController
{
    public function __construct(
        private Verification\UseCase\AddRule\Handler $handler,
        readonly private LoggerInterface $log
    ) {
    }

    #[Route('/rules', methods: ['POST'])]
    public function addRule(Request $request): JsonResponse
    {
        $this->log->debug('rules.add', [$request->query->all()]);

        $ruleId = Uuid::v7();

        //todo mvp limitations
        $clientId = Uuid::fromString('00000000-0000-0000-0000-000000000000');
        $groupId = Uuid::fromString('00000000-0000-0000-0000-000000000000');
        $documentTypeId = Uuid::fromString('00000000-0000-0000-0000-000000000000');

        $ruleName = 'rule name';
        $ruleBody = 'rule body';
        $rulePrompt = 'rule prompt';
        $ruleWeight = 10;
        $ruleComment = 'rule comment';
        $expectedResult = true;

        $this->handler->handle(
            new Verification\UseCase\AddRule\Command(
                $ruleId,
                $clientId,
                $groupId,
                [$documentTypeId],
                Verification\Entity\RuleStatus::active,
                $ruleName,
                $ruleBody,
                $rulePrompt,
                $expectedResult,
                $ruleWeight,
                $ruleComment
            )
        );

        return $this->json(['OK'], StatusCodeInterface::STATUS_OK);
    }
}