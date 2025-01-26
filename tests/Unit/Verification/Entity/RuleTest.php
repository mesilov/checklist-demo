<?php

namespace B24io\Checklist\Tests\Unit\Verification\Entity;

use B24io\Checklist\Verification\Entity\Rule;
use B24io\Checklist\Verification\Entity\RuleStatus;
use Carbon\CarbonImmutable;
use DomainException;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use Throwable;

class RuleTest extends TestCase
{
    public function testCreateWithEmptyDocumentTypes(): void
    {
        $this->expectException(DomainException::class);
        new Rule(
            Uuid::v4(),
            Uuid::v7(),
            Uuid::v7(),
            [],
            new CarbonImmutable(),
            new CarbonImmutable(),
            RuleStatus::active,
            'rule name',
            'rule body',
            'prompt template',
            true
        );
    }

    #[DataProvider('isDocumentTypeSupportedDataProvider')]
    public function testIsDocumentTypeSupported(Rule $rule, Uuid $documentTypeId, bool $result): void
    {
        $this->assertEquals(
            $result,
            $rule->isDocumentTypeSupported($documentTypeId)
        );
    }

    public static function isDocumentTypeSupportedDataProvider(): Generator
    {
        $documentTypeId = Uuid::v7();
        yield 'support - same' => [
            new Rule(
                Uuid::v7(),
                Uuid::v7(),
                Uuid::v7(),
                [$documentTypeId],
                new CarbonImmutable(),
                new CarbonImmutable(),
                RuleStatus::active,
                'rule name',
                'rule body',
                'prompt template',
                true
            ),
            $documentTypeId,
            true
        ];
        yield 'support - different' => [
            new Rule(
                Uuid::v4(),
                Uuid::v7(),
                Uuid::v7(),
                [Uuid::v7()],
                new CarbonImmutable(),
                new CarbonImmutable(),
                RuleStatus::active,
                'rule name',
                'rule body',
                'prompt template',
                true
            ),
            $documentTypeId,
            false
        ];
    }
}
