<?php
declare(strict_types=1);

namespace B24io\Checklist;


use Symfony\Contracts\EventDispatcher\Event;

interface AggregateRootEventsEmitterInterface
{
    /**
     * @return Event[]
     */
    public function emitEvents(): array;
}