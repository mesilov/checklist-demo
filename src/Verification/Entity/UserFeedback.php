<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Entity;

use Doctrine\ORM\Mapping\Embeddable;
use Doctrine\ORM\Mapping as ORM;

#[Embeddable]
class UserFeedback
{
    #[ORM\Column(name: 'rating', type: 'smallint', nullable: true, enumType: UserRating::class)]
    private ?UserRating $rating;
    #[ORM\Column(name: 'comment', type: 'text', nullable: true)]
    private ?string $comment;

    /**
     * @param UserRating|null $rating
     * @param string|null $comment
     */
    public function __construct(?UserRating $rating, ?string $comment)
    {
        $this->rating = $rating;
        $this->comment = $comment;
    }

    public function getRating(): ?UserRating
    {
        return $this->rating;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public static function init(): self
    {
        return new self(null, null);
    }
}