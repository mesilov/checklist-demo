<?php

namespace B24io\Checklist\Tests\Functional\Documents\UseCase\AddNewDocument;

use B24io\Checklist\Documents\Repository\DocumentRepositoryInterface;
use B24io\Checklist\Documents\UseCase\AddNewDocument;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class HandlerTest extends KernelTestCase
{
    public function testSave(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        /**
         * @var AddNewDocument\Handler $addNewDocumentHandler
         */
        $addNewDocumentHandler = $container->get(AddNewDocument\Handler::class);
        /**
         * @var DocumentRepositoryInterface $documentRepo
         */
        $documentRepo = $container->get(DocumentRepositoryInterface::class);

        $docId = Uuid::v7();
        $clientId = Uuid::fromString('00000000-0000-0000-0000-000000000000');
        $documentTypeId = Uuid::fromString('00000000-0000-0000-0000-000000000000');

        $filename = dirname(__DIR__, 5) . '/fixtures/documents/2308282665/policy.md';
        $documentText = file_get_contents($filename);

        $addNewDocumentHandler->handle(
            new AddNewDocument\Command(
                $docId,
                $clientId,
                $documentTypeId,
                $documentText
            )
        );

        $saved = $documentRepo->getById($docId);
        $this->assertEquals($documentText, $saved->getText());
    }
}
