<?php

namespace App\DTO;

class SingleManning
{
    /** @var string */
    public $day;

    /** @var string */
    public $numberOfHours;

    public function __construct(string $day, string $numberOfHours)
    {
        $this->day = $day;
        $this->numberOfHours = $numberOfHours;
    }
}
