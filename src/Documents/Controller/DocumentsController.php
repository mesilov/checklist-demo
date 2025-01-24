<?php

declare(strict_types=1);

namespace B24io\Checklist\Documents\Controller;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use B24io\Checklist\Documents;

class DocumentsController extends AbstractController
{
    public function __construct(
        private Documents\UseCase\AddNewDocument\Handler $handler,
        readonly private LoggerInterface $log
    ) {
    }

    #[Route('/document', methods: ['GET'])]
    public function healthCheck(Request $request): JsonResponse
    {
        $this->log->debug('healthCheck', [$request->query->all()]);

        $docId = Uuid::v7();

        //todo mvp limitations
        $clientId = Uuid::fromString('00000000-0000-0000-0000-000000000000');
        $documentTypeId = Uuid::fromString('00000000-0000-0000-0000-000000000000');

        $text ='hello';
        $this->handler->handle(new Documents\UseCase\AddNewDocument\Command(
            $docId,
            $clientId,
            $documentTypeId,
            $text
        ));

        return $this->json(['OK'], StatusCodeInterface::STATUS_OK);
    }
}