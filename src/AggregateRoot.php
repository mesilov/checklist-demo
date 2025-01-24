<?php

namespace B24io\Checklist;

class AggregateRoot implements AggregateRootEventsEmitterInterface
{
    protected array $events = [];

    public function emitEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}
