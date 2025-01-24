<?php

namespace B24io\Checklist\Tests\Functional\Documents\Infrastructure\Doctrine;

use B24io\Checklist\Documents\Entity\Document;
use B24io\Checklist\Documents\Repository\DocumentRepositoryInterface;
use B24io\Checklist\Services\Flusher;
use Carbon\CarbonImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class DocumentRepositoryTest extends KernelTestCase
{
    public function testSave(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        /**
         * @var DocumentRepositoryInterface $documentRepo
         */
        $documentRepo = $container->get(DocumentRepositoryInterface::class);
        /**
         * @var Flusher $flusher
         */
        $flusher = $container->get(Flusher::class);

        $doc = new Document(
            Uuid::v7(),
            Uuid::v7(),
            Uuid::v7(),
            new CarbonImmutable(),
            'hello world!'
        );
        $documentRepo->save($doc);
        $flusher->flush();

        $saved = $documentRepo->getById($doc->getId());
        $this->assertEquals($doc->getText(), $saved->getText());
    }

    protected function setUp(): void
    {
    }
}
