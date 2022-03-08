<?php

namespace App\Models;

use DateTime;
use InvalidArgumentException;

class Shift
{
    /** @var DateTime */
    public $start_time;

    /** @var  DateTime */
    public $end_time;

    /** This would usally be done with a relationship and then storing the id instead of the whole object */
    /** @var string */
    public $staff_name;

    public function __construct(DateTime $startTime, DateTime $endTime, string $staffName)
    {
        if ($endTime < $startTime) {
            throw new InvalidArgumentException('A Shift has to have a greater end time then start time');
        }

        $this->start_time = $startTime;
        $this->end_time = $endTime;
        $this->staff_name = $staffName;
    }

    public function dayStarted(): string
    {
        return $this->start_time->format('d-m-Y');
    }
}
