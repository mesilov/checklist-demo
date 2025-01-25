<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Controller;

use B24io\Checklist\Verification\Entity\LanguageModel;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use B24io\Checklist\Verification;

class VerificationController extends AbstractController
{
    public function __construct(
        private Verification\UseCase\AddVerification\Handler $handler,
        readonly private LoggerInterface $log
    ) {
    }

    #[Route('/verifications', methods: ['POST'])]
    public function addRule(Request $request): JsonResponse
    {
        $this->log->debug('verifications.add', [$request->query->all()]);

        $ruleId = Uuid::v7();

        //todo mvp limitations
        $clientId = Uuid::fromString('00000000-0000-0000-0000-000000000000');
        $groupId = Uuid::fromString('00000000-0000-0000-0000-000000000000');
        $documentId = Uuid::fromString('00000000-1111-0000-0000-000000000000');

        $this->handler->handle(
            new Verification\UseCase\AddVerification\Command(
                $ruleId,
                $clientId,
                [$documentId],
                $groupId,
                LanguageModel::openAiGpt4o
            )
        );

        return $this->json(['OK'], StatusCodeInterface::STATUS_OK);
    }
}