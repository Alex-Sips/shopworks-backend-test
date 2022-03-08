<?php

namespace App\Models;

class Staff
{
    /** @var string */
    public $first_name;

    /** @var string */
    public $surname;

    public function __construct(string $firstName, string $surname)
    {
        $this->first_name = $firstName;
        $this->surname = $surname;
    }
}
