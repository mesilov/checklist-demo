<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Entity;

use Doctrine\ORM\Mapping\Embeddable;
use Doctrine\ORM\Mapping as ORM;

#[Embeddable]
class VerificationStepResult
{
    #[ORM\Column(name: 'verification_status', type: 'string', nullable: true, enumType: VerificationStepStatus::class)]
    private ?VerificationStepStatus $stepStatus;
    #[ORM\Column(name: 'conclusion', type: 'text', nullable: true)]
    private ?string $conclusion;
    #[ORM\Column(name: 'confirmation_quote', type: 'text', nullable: true)]
    private ?string $confirmationQuote;
    #[ORM\Column(name: 'quote_position', type: 'text', nullable: true)]
    private ?string $quotePosition;
    #[ORM\Column(name: 'reasoning_steps', type: 'json', nullable: true)]
    private ?string $reasoningSteps;
    /**
     * @see https://platform.openai.com/docs/api-reference/completions/object#completions/object-system_fingerprint
     */
    #[ORM\Column(name: 'system_fingerprint', type: 'string', nullable: true)]
    private ?string $systemFingerprint;

    public function __construct(
        ?VerificationStepStatus $stepStatus,
        ?string $conclusion,
        ?string $confirmationQuote,
        ?string $quotePosition,
        ?string $reasoningSteps,
        ?string $systemFingerprint
    ) {
        $this->stepStatus = $stepStatus;
        $this->conclusion = $conclusion;
        $this->confirmationQuote = $confirmationQuote;
        $this->quotePosition = $quotePosition;
        $this->reasoningSteps = $reasoningSteps;
        $this->systemFingerprint = $systemFingerprint;
    }

    public static function init(): self
    {
        return new self(null, null, null, null, null, null);
    }
}