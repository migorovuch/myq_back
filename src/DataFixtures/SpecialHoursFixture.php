<?php

namespace App\DataFixtures;

use App\Entity\SpecialHours;
use App\Model\DTO\SpecialHours\RangeDTO;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use DateTime;

class SpecialHoursFixture extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $defultRanges = [new RangeDTO('06:00', '15:00')];

        $schedule1 = $this->getReference(ScheduleFixture::SCHEDULE_1);
        $schedule2 = $this->getReference(ScheduleFixture::SCHEDULE_2);

        $specialHours1 = new SpecialHours();
        $specialHours1
            ->setStartDate((new DateTime())->modify("-1 week"))
            ->setEndDate((new DateTime())->modify("+1 year"))
            ->setRanges($defultRanges)
            ->setSchedule($schedule1)
            ->setRepeatDay(0);
        $manager->persist($specialHours1);

        $specialHours2 = new SpecialHours();
        $specialHours2
            ->setStartDate((new DateTime())->modify("-1 week"))
            ->setEndDate((new DateTime())->modify("+1 year"))
            ->setRanges($defultRanges)
            ->setSchedule($schedule1)
            ->setRepeatDay(1);
        $manager->persist($specialHours2);

        $specialHours3 = new SpecialHours();
        $specialHours3
            ->setStartDate((new DateTime())->modify("-1 week"))
            ->setEndDate((new DateTime())->modify("+1 year"))
            ->setRanges($defultRanges)
            ->setSchedule($schedule1)
            ->setRepeatDay(2);
        $manager->persist($specialHours3);

        $specialHours4 = new SpecialHours();
        $specialHours4
            ->setStartDate((new DateTime())->modify("-1 week"))
            ->setEndDate((new DateTime())->modify("+1 year"))
            ->setRanges($defultRanges)
            ->setSchedule($schedule1)
            ->setRepeatDay(3);
        $manager->persist($specialHours4);

        $specialHours5 = new SpecialHours();
        $specialHours5
            ->setStartDate((new DateTime())->modify("-1 week"))
            ->setEndDate((new DateTime())->modify("+1 year"))
            ->setRanges($defultRanges)
            ->setSchedule($schedule1)
            ->setRepeatDay(4);
        $manager->persist($specialHours5);

        $specialHours6 = new SpecialHours();
        $specialHours6
            ->setStartDate((new DateTime())->modify("-1 week"))
            ->setEndDate((new DateTime())->modify("+1 year"))
            ->setRanges($defultRanges)
            ->setSchedule($schedule2)
            ->setRepeatDay(0);
        $manager->persist($specialHours6);

        $specialHours7 = new SpecialHours();
        $specialHours7
            ->setStartDate((new DateTime())->modify("-1 week"))
            ->setEndDate((new DateTime())->modify("+1 year"))
            ->setRanges($defultRanges)
            ->setSchedule($schedule2)
            ->setRepeatDay(6);
        $manager->persist($specialHours7);

        $specialHours8 = new SpecialHours();
        $specialHours8
            ->setStartDate((new DateTime())->modify("-1 week"))
            ->setEndDate((new DateTime())->modify("+1 year"))
            ->setRanges($defultRanges)
            ->setRepeatCondition(SpecialHours::REPEAT_ONCE_A_MONTH)
            ->setSchedule($schedule2)
            ->setRepeatDate(new DateTime('2021-05-27'));
        $manager->persist($specialHours8);

        $manager->flush();
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return [
            ScheduleFixture::class,
        ];
    }
}
