<?php

namespace Test\Unit;

use DateTime;
use DateInterval;
use App\Models\Shift;
use InvalidArgumentException;

class ShiftTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function a_shifts_end_time_must_be_after_the_start_time()
    {
        $endTime = new DateTime();
        $now = new DateTime();
        $startTime = $now->add(new DateInterval('PT6H'));

        $this->expectException(InvalidArgumentException::class);

        new Shift($startTime, $endTime, 'Black Widow');
    }

    /** @test */
    public function you_can_get_the_shifts_start_day()
    {
        $startTime = new DateTime();
        $now = new DateTime();
        $endTime = $now->add(new DateInterval('PT6H'));

        $shift = new Shift($startTime, $endTime, 'Black Widow');

        $this->assertInstanceOf(Shift::class, $shift);
        $this->assertEquals($shift->dayStarted(), $startTime->format('d-m-Y'));
    }
}
