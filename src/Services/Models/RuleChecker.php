<?php

declare(strict_types=1);

namespace B24io\Checklist\Services\Models;


use B24io\Checklist\Verification\Entity\VerificationStep;
use B24io\Checklist\Verification;
use OpenAI;
use Psr\Log\LoggerInterface;

class RuleChecker
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function run(
        OpenAI\Client $client,
        Verification\Entity\LanguageModel $languageModel,
        int $seedNumber,
        VerificationStep $step,
        bool $expectedResult,
        array $documents
    ): void {
        $this->logger->debug('RuleChecker.run', [
            'stepId' => $step->getId()->toRfc4122(),
        ]);

        $response = $client->chat()->create([
            'model' => $languageModel,
            'metadata' => [
                'verification-step-id' => $step->getId()->toRfc4122(),
            ],
            'seed' => $seedNumber,
            'store' => true,
            'messages' => [
                [
                    'role' => 'developer',
                    'content' => 'Ты юрист, специализирующийся на безопасности данных'
                ],
                [
                    'role' => 'user',
                    'content' => 'Документ: «Политика обработки персональных данных»' . PHP_EOL . PHP_EOL . $documents['Политика обработки персональных данных']
                ],
                [
                    'role' => 'user',
                    'content' => $step->getPrompt()
                ]
            ],
            'response_format' => [
                'type' => 'json_schema',
                'json_schema' => [
                    'name' => 'document_verification_response',
                    'strict' => true,
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            "final_conclusion_in_text" => [
                                "type" => ["string", "null"],
                                "description" => "The final conclusion in text format"
                            ],
                            "fragment_of_document_with_confirmation_quote" => [
                                "type" => ["string", "null"],
                                "description" => "A fragment of the document containing the confirmation of answer"
                            ],
                            "is_answer_to_question_positive" => [
                                "type" => ["boolean", "null"],
                                "description" => "Indicates whether the answer to the question is positive",
                            ],
                            "human_readable_position_of_confirmation_quote_in_document" => [
                                "type" => ["string", "null"],
                                "description" => "Human readable position of confirmation quote in document"
                            ],
                            'reasoning_steps' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'explanation' => [
                                            "type" => ["string", "null"],
                                            "description" => "explanation of reasoning step",
                                        ],
                                        'output' => [
                                            "type" => ["string", "null"],
                                            "description" => "output for reasoning step",
                                        ]
                                    ],
                                    'required' => ['explanation', 'output'],
                                    'additionalProperties' => false
                                ]
                            ],
                        ],
                        'required' => [
                            'is_answer_to_question_positive',
                            'final_conclusion_in_text',
                            'fragment_of_document_with_confirmation_quote',
                            'human_readable_position_of_confirmation_quote_in_document',
                            'reasoning_steps',
                        ],
                        'additionalProperties' => false
                    ]
                ]
            ]

        ]);


        dump($response->toArray());
    }
}

