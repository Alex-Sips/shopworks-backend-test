<?php

namespace App\Functions;

use DateInterval;
use App\Models\Rota;
use App\Models\Shift;
use App\DTO\SingleManning;

class SingleManningCalculator
{
    public static function calculateSingleManning(Rota $rota): array
    {
        // Order by the day as the key and the shifts by time started
        $groupShiftsByDay = $rota->groupShiftsByDayAndStartDate();
        $singleManningList = [];

        // Iterate through the days
        foreach ($groupShiftsByDay as $day => $shift) {
            // Get the amount of single manned minutes in that day
            $singleManningHours = self::calculateSingleManningMinutes($shift);

            $singleManningList[] = new SingleManning($day, $singleManningHours);
        }

        return $singleManningList;
    }

    public static function calculateSingleManningMinutes(array $shifts)
    {
        $singleManning = 0;

        // If there is only one shift then return the single manned hours for that day
        if (1 === count($shifts)) {
            $singleManning += self::getTotalTime($shifts[0]->end_time->diff($shifts[0]->start_time));

            return $singleManning;
        }

        // If there are multiple iterate through the shifts
        for ($i = 0; $i < count($shifts); ++$i) {
            // Get the current shiift
            $currentShift = $shifts[$i];

            // If it is the last started shift and worked during someone else shift end the loop
            if ($currentShift === end($shifts) && $currentShift->end_time <= $shifts[$i - 1]->end_time) {
                break;
            }

            // If it is the last shift of the day calculate the amount of time spent solo
            if ($currentShift === end($shifts)) {
                // If they started the shift by themselves calculate the time they worked
                $previousShift = $shifts[$i - 1];
                if ($currentShift->start_time <= $previousShift->end_time) {
                    $hoursSpentSolo = self::getTotalTime($currentShift->end_time->diff($previousShift->end_time));
                    $singleManning += $hoursSpentSolo;

                    continue;
                }
                //Else calculate the amount of time they worked
                $hoursSpentSolo = self::getTotalTime($currentShift->end_time->diff($currentShift->start_time));
                $singleManning += $hoursSpentSolo;

                continue;
            }

            $nextShift = $shifts[$i + 1];
            // If the current shift ends before the next one starts calculate the amount of time worked
            if ($currentShift->end_time <= $nextShift->start_time) {
                $hoursSpentSolo = self::getTotalTime($currentShift->start_time->diff($currentShift->end_time));
                $singleManning += $hoursSpentSolo;

                continue;
            }

            // Else calculate the amount of time worked
            $singleManning += self::hoursSpentWorkingAlone($currentShift, $nextShift);
        }

        return $singleManning;
    }

    private static function getTotalTime(DateInterval $interval): int
    {
        return ($interval->d * 24 * 60) + ($interval->h * 60) + $interval->i;
    }

    private static function hoursSpentWorkingAlone(Shift $currentShift, Shift $nextShift): int
    {
        // Hours spent working alone before another shift started
        $hoursSpentSoloBeforeOtherArrived = self::getTotalTime($currentShift->start_time->diff($nextShift->start_time));

        // If there shift over lapped with someone elses and went on longer
        if ($currentShift->end_time > $nextShift->end_time) {
            // Time spend working alone after the last person left
            $hoursSpentSoloAfterOtherLeft = self::getTotalTime($nextShift->end_time->diff($currentShift->end_time));

            return $hoursSpentSoloBeforeOtherArrived + $hoursSpentSoloAfterOtherLeft;
        }

        return $hoursSpentSoloBeforeOtherArrived;
    }
}
