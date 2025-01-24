<?php

declare(strict_types=1);

namespace B24io\Checklist\Verification\Entity;

enum UserRating: int
{
    case flawless = 5;  // maximum accuracy and correctness.
    case accurate = 4;  // generally correct with minor inaccuracies.
    case average = 3;  // partially correct with missing or incorrect details.
    case faulty = 2;  // contains significant errors or omissions.
    case incorrect = 1; //completely wrong or unrelated to the query.
}