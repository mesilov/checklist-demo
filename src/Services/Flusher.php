<?php

declare(strict_types=1);

namespace B24io\Checklist\Services;


use B24io\Checklist\AggregateRootEventsEmitterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class Flusher
{
    public function __construct(
        private EntityManagerInterface $em,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function flush(AggregateRootEventsEmitterInterface ...$roots): void
    {
        $this->em->flush();
        foreach ($roots as $root) {
            $events = $root->emitEvents();
            foreach ($events as $event) {
                $this->eventDispatcher->dispatch($event);
            }
        }
    }
}
