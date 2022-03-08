<?php

namespace App\Models;

use DateTime;

class Rota
{
    /** @var DateTime */
    public $week_commence_date;

    /** @var array */
    public $shifts;

    public function __construct(DateTime $weekCommenceDate, array $shifts = [])
    {
        $this->week_commence_date = $weekCommenceDate;
        $this->shifts = $shifts;
    }

    public function groupShiftsByDayAndStartDate()
    {
        $groupedShiftsByDay = [];

        usort($this->shifts, fn ($first, $second) => $first->start_time <=> $second->start_time);

        foreach ($this->shifts as $shift) {
            $day = $shift->dayStarted();
            $groupedShiftsByDay[$day][] = $shift;
        }

        return $groupedShiftsByDay;
    }
}
