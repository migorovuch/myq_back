<?php

namespace App\DataFixtures;

use App\Entity\Booking;
use App\Entity\CompanyClient;
use App\Entity\Schedule;
use App\Entity\User;
use DateInterval;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BookingFixture extends Fixture implements DependentFixtureInterface
{
    const BOOKINGS_COUNT = 10;

    public function load(ObjectManager $manager)
    {
        /** @var User $user */
        $user = $this->getReference(UserFixtures::USER_USER_REFERENCE);
        /** @var Schedule $schedule */
        $schedule = $this->getReference(ScheduleFixture::SCHEDULE_1);

        $days = [-3, -2, -1, 0, 1, 2, 3, 4];
        $hours = [6, 7, 8, 9, 10, 11, 12, 13, 14];
        $minutes = [0, 30];
        $createdBookingsDates = [];

        $generateStartDate = function () use ($days, $hours, $minutes) {
            $start = new DateTime();
            $startDayKey = random_int(0, \count($days) - 1);
            $startHoursKey = random_int(0, \count($hours) - 1);
            $startMinuteKey = random_int(0, \count($minutes) - 1);

            return $start->modify($days[$startDayKey].' day')->setTime($hours[$startHoursKey],
                $minutes[$startMinuteKey]);
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

        for ($i = 1; $i <= self::BOOKINGS_COUNT; ++$i) {
            $startDate = $selectStartDate($createdBookingsDates);
            $endDate = clone $startDate;
            $endDate = $endDate->add(new DateInterval('PT30M'));
            $clientNumber = random_int(1, CompanyClientFixture::CLIENTS_COUNT);
            /** @var CompanyClient $client */
            $client = $this->getReference(CompanyClientFixture::COMPANY_CLIENT_.$clientNumber);
            $booking = new Booking();
            $booking
                ->setSchedule($schedule)
                ->setCustomerComment('some comment '.$i)
                ->setStart($startDate)
                ->setEnd($endDate)
                ->setStatus(Booking::STATUS_ACCEPTED)
                ->setClient($client)
                ->setTitle($client->getName());
            $manager->persist($booking);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CompanyClientFixture::class,
            ScheduleFixture::class,
        ];
    }
}
