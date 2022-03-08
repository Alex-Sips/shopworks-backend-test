<?php

namespace Test\Unit;

use DateTime;
use DateInterval;
use App\Models\Rota;
use App\Models\Shift;

class RotaTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function a_rota_can_be_created_without_a_shift()
    {
        $rota = new Rota(new DateTime(), []);

        $this->assertInstanceOf(Rota::class, $rota);
    }

    /** @test */
    public function a_rota_can_be_created_with_a_shift()
    {
        $startTime = new DateTime();
        $now = new DateTime();
        $endTime = $now->add(new DateInterval('PT6H'));

        $rota = new Rota(
            new DateTime(),
            [
                new Shift($startTime, $endTime, 'Black Widow'),
            ]
        );

        $this->assertInstanceOf(Rota::class, $rota);
        $this->assertInstanceOf(Shift::class, $rota->shifts[0]);
    }

    /** @test */
    public function you_can_group_a_rota_shifts_by_start_time()
    {
        $firstShiftStartTime = new DateTime();
        $now = new DateTime();
        $firstShiftEndTime = $now->add(new DateInterval('PT3H'));

        $now = new DateTime();
        $secondShiftStartTime = $now->add(new DateInterval('PT3H'));
        $secondShiftEndTime = $now->add(new DateInterval('PT3H'));

        $rota = new Rota(
            new DateTime(),
            [
                new Shift($firstShiftStartTime, $firstShiftEndTime, 'Black Widow'),
                new Shift($secondShiftStartTime, $secondShiftEndTime, 'Thor Odinson'),
            ]
        );

        $this->assertInstanceOf(Rota::class, $rota);

        $groupedShifts = $rota->groupShiftsByDayAndStartDate();
        $shifts = $groupedShifts[$firstShiftStartTime->format('d-m-Y')];
        $this->assertEquals(array_keys($groupedShifts)[0], $firstShiftStartTime->format('d-m-Y'));
        $this->assertInstanceOf(Shift::class, $shifts[0]);
        $this->assertInstanceOf(Shift::class, $shifts[1]);
    }
}
