<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Entity;

use Doctrine\ORM\Mapping\Embeddable;
use Doctrine\ORM\Mapping as ORM;

#[Embeddable]
class TokensUsage
{
    #[ORM\Column(name: 'input', type: 'integer', nullable: true)]
    private ?int $input;
    #[ORM\Column(name: 'output', type: 'integer', nullable: true)]
    private ?int $output;

    public function __construct(
        int $input,
        int $output
    ) {
        $this->input = $input;
        $this->output = $output;
    }
}