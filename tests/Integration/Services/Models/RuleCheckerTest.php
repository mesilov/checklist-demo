<?php

namespace B24io\Checklist\Tests\Integration\Services\Models;

use B24io\Checklist\Services\Models\RuleChecker;
use B24io\Checklist\Verification\Entity\LanguageModel;
use B24io\Checklist\Verification\Entity\ProcessingStatus;
use B24io\Checklist\Verification\Entity\Rule;
use B24io\Checklist\Verification\Entity\RuleStatus;
use B24io\Checklist\Verification\Entity\VerificationStep;
use Carbon\CarbonImmutable;
use DomainException;
use Generator;
use OpenAI;
use OpenAI\Client;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Uid\Uuid;
use Throwable;

class RuleCheckerTest extends TestCase
{
    private Client $apiClient;

    public function testCall(): void
    {
        $ruleChecker = new RuleChecker(new NullLogger());
        $step = new VerificationStep(
            Uuid::v7(),
            new CarbonImmutable(),
            null,
            Uuid::v7(),
            Uuid::v7(),
            Uuid::v7(),
            ProcessingStatus::new,
            '
Проверь текст в документе «Политика обработки персональных данных» в формате markdown. Убедись, что в документе указано
наименование юридического лица, которое обрабатывает персональные данные
'
        );

        $documentPolicy = file_get_contents(dirname(__DIR__, 4) . '/fixtures/documents/2308282665/policy.md');
        $documents = [
            'Политика обработки персональных данных' => $documentPolicy
        ];

        $seedNumber = 10;
        // забираем из правила
        $expectedResult = true;


        $ruleChecker->run(
            $this->apiClient,
            LanguageModel::gpt4oMini20240718,
            $seedNumber,
            $step,
            $expectedResult,
            $documents
        );
    }


    protected function setUp(): void
    {
        $this->apiClient = OpenAI::client($_ENV['OPEN_AI_KEY']);
    }
}
