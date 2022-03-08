<?php

namespace Test\Feature;

use DateTime;
use DateInterval;
use App\Models\Rota;
use App\Models\Shift;
use App\Models\Staff;
use App\DTO\SingleManning;
use App\Functions\SingleManningCalculator;

class SingleManningCalculatorTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function when_a_single_staff_member_works_a_shift_they_should_get_single_manning_time()
    {
        $startTime = new DateTime();
        $now = new DateTime();
        $endTime = $now->add(new DateInterval('PT6H'));

        $rota = new Rota(
            new DateTime(),
            [
                new Shift($startTime, $endTime, new Staff('Black', 'Widow')),
            ]
        );

        $expected = [
            new SingleManning($startTime->format('d-m-Y'), 360),
        ];

        $singleManning = SingleManningCalculator::calculateSingleManning($rota);

        $this->assertEquals($expected, $singleManning);
        $this->assertInstanceOf(SingleManning::class, $singleManning[0]);
    }

    /** @test */
    public function when_two_staff_members_work_on_the_same_day_and_dont_overlap_they_should_get_single_manning_time()
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
                new Shift($firstShiftStartTime, $firstShiftEndTime, new Staff('Black', 'Widow')),
                new Shift($secondShiftStartTime, $secondShiftEndTime, new Staff('Thor', 'Odinson')),
            ]
        );

        $expected = [
            new SingleManning($firstShiftStartTime->format('d-m-Y'), 360),
        ];

        $singleManning = SingleManningCalculator::calculateSingleManning($rota);

        $this->assertEquals($expected, $singleManning);
    }

    /** @test */
    public function when_two_staff_members_work_on_the_same_day_and_do_overlap_it_calculates_the_correct_single_manning_time()
    {
        $firstShiftStartTime = new DateTime();
        $now = new DateTime();
        $firstShiftEndTime = $now->add(new DateInterval('PT2H'));

        $now = new DateTime();
        $secondShiftStartTime = $now->add(new DateInterval('PT1H'));
        $now = new DateTime();
        $secondShiftEndTime = $now->add(new DateInterval('PT3H'));

        $rota = new Rota(
            new DateTime(),
            [
                new Shift($firstShiftStartTime, $firstShiftEndTime, new Staff('Gamor', 'Zen Whoberi')),
                new Shift($secondShiftStartTime, $secondShiftEndTime, new Staff('Logan', 'Wolverine')),
            ]
        );

        $expected = [
            new SingleManning($firstShiftStartTime->format('d-m-Y'), 120),
        ];

        $singleManning = SingleManningCalculator::calculateSingleManning($rota);

        $this->assertEquals($expected, $singleManning);
    }

    /** @test */
    public function when_two_staff_members_work_different_day_will_return_two_single_manning_objects_for_two_days()
    {
        $firstShiftStartTime = new DateTime();
        $now = new DateTime();
        $firstShiftEndTime = $now->add(new DateInterval('PT2H'));

        $now = new DateTime();
        $secondShiftStartTime = $now->add(new DateInterval('P1D'));
        $now = new DateTime();
        $now->add(new DateInterval('P1D'));
        $secondShiftEndTime = $now->add(new DateInterval('PT3H'));

        $rota = new Rota(
            new DateTime(),
            [
                new Shift($firstShiftStartTime, $firstShiftEndTime, new Staff('Gamor', 'Zen Whoberi')),
                new Shift($secondShiftStartTime, $secondShiftEndTime, new Staff('Logan', 'Wolverine')),
            ]
        );

        $expected = [
            new SingleManning($firstShiftStartTime->format('d-m-Y'), 120),
            new SingleManning($secondShiftStartTime->format('d-m-Y'), 180),
        ];

        $singleManning = SingleManningCalculator::calculateSingleManning($rota);

        $this->assertEquals($expected, $singleManning);
    }

    /** @test */
    public function blah()
    {
        $firstShiftStartTime = new DateTime();
        $now = new DateTime();
        $firstShiftEndTime = $now->add(new DateInterval('PT2H'));

        $now = new DateTime();
        $secondShiftStartTime = $now->add(new DateInterval('P1D'));
        $now = new DateTime();
        $now->add(new DateInterval('P1D'));
        $secondShiftEndTime = $now->add(new DateInterval('PT3H'));

        $rota = new Rota(
            new DateTime(),
            [
                new Shift($firstShiftStartTime, $firstShiftEndTime, new Staff('Gamor', 'Zen Whoberi')),
                new Shift($secondShiftStartTime, $secondShiftEndTime, new Staff('Logan', 'Wolverine')),
            ]
        );

        $expected = [
            new SingleManning($firstShiftStartTime->format('d-m-Y'), 120),
            new SingleManning($secondShiftStartTime->format('d-m-Y'), 180),
        ];

        $singleManning = SingleManningCalculator::calculateSingleManning($rota);

        $this->assertEquals($expected, $singleManning);
    }
}
