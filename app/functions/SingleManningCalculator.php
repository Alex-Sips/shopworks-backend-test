<?php

namespace App\Functions;

use DateInterval;
use App\Models\Rota;
use App\Models\Shift;
use App\DTO\SingleManning;

class SingleManningCalculator
{
    public static function calculateSingleManning(Rota $rota)
    {
        $groupShiftsByDay = $rota->groupShiftsByDayAndStartDate();
        $singleManningList = [];

        foreach ($groupShiftsByDay as $day => $shift) {
            $singleManningHours = self::calculateSingleManningInShiftsList($shift);

            $singleManningList[] = new SingleManning($day, $singleManningHours);
        }

        return $singleManningList;
    }

    public static function calculateSingleManningInShiftsList(array $shifts)
    {
        $singleManning = 0;

        if (1 === count($shifts)) {
            $singleManning += self::getTotalTime($shifts[0]->end_time->diff($shifts[0]->start_time));

            return $singleManning;
        }

        for ($i = 0; $i < count($shifts); ++$i) {
            $currentShift = $shifts[$i];

            if ($currentShift === end($shifts)) {
                $previousShift = $shifts[$i - 1];
                $hoursSpentSolo = self::getTotalTime($currentShift->end_time->diff($previousShift->end_time));
                $singleManning += $hoursSpentSolo;

                continue;
            }

            $nextShift = $shifts[$i + 1];

            if ($currentShift->end_time <= $nextShift->start_time) {
                $hoursSpentSolo = self::getTotalTime($currentShift->end_time->diff($currentShift->start_time));
                $singleManning += $hoursSpentSolo;

                continue;
            }

            $singleManning += self::hoursSpentWorkingAlone($currentShift, $nextShift);
            $currentShiftEndTime = $currentShift->end_time;
        }

        return $singleManning;
    }

    private static function getTotalTime(DateInterval $interval): int
    {
        return ($interval->d * 24 * 60) + ($interval->h * 60) + $interval->i;
    }

    private static function hoursSpentWorkingAlone(Shift $currentShift, Shift $nextShift): int
    {
        $hoursSpentSoloBeforeOtherArrived = self::getTotalTime($currentShift->start_time->diff($nextShift->start_time));
        $hoursSpentSoloAfterOtherLeft = self::getTotalTime($nextShift->end_time->diff($currentShift->end_time));

        if ($currentShift->end_time > $nextShift->end_time) {
            $hoursSpentSoloAfterOtherLeft = self::getTotalTime($nextShift->end_time->diff($currentShift->end_time));

            return $hoursSpentSoloBeforeOtherArrived + $hoursSpentSoloAfterOtherLeft;
        }

        return $hoursSpentSoloBeforeOtherArrived;
    }
}
