<?php

namespace App\DataFixtures;

use App\Entity\Booking;
use App\Entity\Schedule;
use App\Entity\User;
use DateInterval;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use DateTime;

class BookingFixture extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        /** @var User $user */
        $user = $this->getReference(UserFixtures::USER_USER_REFERENCE);
        /** @var Schedule $schedule */
        $schedule = $this->getReference(ScheduleFixture::SCHEDULE_1);

        $days = [-3, -2, -1, 0, 1, 2, 3, 4];
        $hours = [9, 10, 11, 12, 13, 14, 15, 16, 17];
        $minutes = [0, 30];
        $createdBookingsDates = [];

        $generateStartDate = function () use ($days, $hours, $minutes) {
            $start = new DateTime();
            $startDayKey = random_int(0, count($days) - 1);
            $startHoursKey = random_int(0, count($hours) - 1);
            $startMinuteKey = random_int(0, count($minutes) - 1);

            return $start->modify($days[$startDayKey].' day')->setTime($hours[$startHoursKey], $minutes[$startMinuteKey]);
        };

        $selectStartDate = function (&$createdBookingsDates) use ($generateStartDate) {
            while (true) {
                $startDate = $generateStartDate();
                if (!isset($createdBookingsDates[$startDate->format('Y-m-d H:i')])) {
                    $createdBookingsDates[$startDate->format('Y-m-d H:i')] = 1;

                    return $startDate;
                }
            }
        };

        for ($i = 1; $i <= 10; $i++) {
            $startDate = $selectStartDate($createdBookingsDates);
            $endDate = clone $startDate;
            $endDate = $endDate->add(new DateInterval('PT30M'));
            $booking = new Booking();
            $booking
                ->setSchedule($schedule)
                ->setCustomerComment('some comment ' . $i)
                ->setStart($startDate)
                ->setEnd($endDate)
                ->setStatus(Booking::STATUS_ACCEPTED)
                ->setTitle('Title ' . $i);
            if (random_int(0, 1)) {
                $booking
                    ->setUser($user)
                    ->setUserName($user->getUsername())
                    ->setUserPhone($user->getPhone());
            } else {
                $booking
                    ->setUserName('Test User name' . $i)
                    ->setUserPhone('11111' . $i);
            }
            $manager->persist($booking);
        }
        $manager->flush();

    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            ScheduleFixture::class,
        ];
    }
}
