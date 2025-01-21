<?php

declare(strict_types=1);

namespace B24io\Checklist\Controller;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class ManagementController extends AbstractController
{
    public function __construct(
       readonly private LoggerInterface                    $log
    )
    {
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/draft-inadarei-api-health-check
     */
    #[Route('/health', methods: ['GET'])]
    public function healthCheck(Request $request): JsonResponse
    {
        $this->log->debug('healthCheck', [$request->query->all()]);
        $serviceMetadata = [
            'serviceId' => 'app',
            'description' => 'checklist-demo',
        ];
        $statusCode = StatusCodeInterface::STATUS_OK;
        try {
            $state = [
                'status' => 'pass',
            ];
        } catch (Throwable $exception) {
            $statusCode = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
            $state = [
                'status' => 'fail',
                'output' => $exception->getMessage(),
            ];
        }

        return $this->json(array_merge($serviceMetadata, $state), $statusCode);
    }
}