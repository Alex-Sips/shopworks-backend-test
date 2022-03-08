<?php

namespace Test\Feature;

use DateTime;
use DateInterval;
use App\Models\Rota;
use App\Models\Shift;
use App\DTO\SingleManning;
use App\Functions\SingleManningCalculator;

class SingleManningCalculatorTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function given_a_single_staff_member_works_a_shift_they_should_get_single_manning_time()
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

        $expected = [
            new SingleManning($startTime->format('d-m-Y'), 360),
        ];

        $singleManning = SingleManningCalculator::calculateSingleManning($rota);

        $this->assertEquals($expected, $singleManning);
        $this->assertInstanceOf(SingleManning::class, $singleManning[0]);
    }

    /** @test */
    public function given_two_staff_members_work_on_the_same_day_and_dont_overlap_they_should_get_single_manning_time()
    {
        $firstShiftStartTime = new DateTime();
        $now = new DateTime();
        $firstShiftEndTime = $now->add(new DateInterval('PT3H'));

        $now = new DateTime();
        $secondShiftStartTime = $now->add(new DateInterval('PT3H'));
        $now = new DateTime();
        $secondShiftEndTime = $now->add(new DateInterval('PT6H'));

        $rota = new Rota(
            new DateTime(),
            [
                new Shift($firstShiftStartTime, $firstShiftEndTime, 'Black Widow'),
                new Shift($secondShiftStartTime, $secondShiftEndTime, 'Thor Odinson'),
            ]
        );

        $expected = [
            new SingleManning($firstShiftStartTime->format('d-m-Y'), 360),
        ];

        $singleManning = SingleManningCalculator::calculateSingleManning($rota);

        $this->assertEquals($expected, $singleManning);
        $this->assertInstanceOf(SingleManning::class, $singleManning[0]);
    }

    /** @test */
    public function given_two_staff_members_work_on_the_same_day_and_do_overlap_it_calculates_the_correct_single_manning_time()
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
                new Shift($firstShiftStartTime, $firstShiftEndTime, 'Gamor'),
                new Shift($secondShiftStartTime, $secondShiftEndTime, 'Logan'),
            ]
        );

        $expected = [
            new SingleManning($firstShiftStartTime->format('d-m-Y'), 120),
        ];

        $singleManning = SingleManningCalculator::calculateSingleManning($rota);

        $this->assertEquals($expected, $singleManning);
        $this->assertInstanceOf(SingleManning::class, $singleManning[0]);
    }

    /** @test */
    public function given_two_staff_members_work_different_day_will_return_two_single_manning_objects_for_two_days()
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
                new Shift($firstShiftStartTime, $firstShiftEndTime, 'Gamor'),
                new Shift($secondShiftStartTime, $secondShiftEndTime, 'Wolverine'),
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
    public function given_two_staff_members_shifts_do_not_overlap_at_all_reutrns_the_correct_single_manning_minutes()
    {
        $firstShiftStartTime = new DateTime();
        $now = new DateTime();
        $firstShiftEndTime = $now->add(new DateInterval('PT2H'));

        $now = new DateTime();
        $secondShiftStartTime = $now->add(new DateInterval('PT3H'));
        $now = new DateTime();
        $secondShiftEndTime = $now->add(new DateInterval('PT6H'));

        $rota = new Rota(
            new DateTime(),
            [
                new Shift($firstShiftStartTime, $firstShiftEndTime, 'Black Widow'),
                new Shift($secondShiftStartTime, $secondShiftEndTime, 'Thor Odinson'),
            ]
        );

        $expected = [
            new SingleManning($firstShiftStartTime->format('d-m-Y'), 300),
        ];

        $singleManning = SingleManningCalculator::calculateSingleManning($rota);

        $this->assertEquals($expected, $singleManning);
        $this->assertInstanceOf(SingleManning::class, $singleManning[0]);
    }

    /** @test */
    public function given_two_staff_members_shifts_overlap_and_the_first_shift_starts_before_and_ends_after_reutrns_the_correct_single_manning_minutes()
    {
        $firstShiftStartTime = new DateTime();
        $now = new DateTime();
        $firstShiftEndTime = $now->add(new DateInterval('PT4H'));

        $now = new DateTime();
        $secondShiftStartTime = $now->add(new DateInterval('PT1H'));
        $now = new DateTime();
        $secondShiftEndTime = $now->add(new DateInterval('PT3H'));

        $rota = new Rota(
            new DateTime(),
            [
                new Shift($firstShiftStartTime, $firstShiftEndTime, 'Black Widow'),
                new Shift($secondShiftStartTime, $secondShiftEndTime, 'Thor Odinson'),
            ]
        );

        $expected = [
            new SingleManning($firstShiftStartTime->format('d-m-Y'), 119),
        ];

        $singleManning = SingleManningCalculator::calculateSingleManning($rota);

        $this->assertEquals($expected, $singleManning);
        $this->assertInstanceOf(SingleManning::class, $singleManning[0]);
    }
}
