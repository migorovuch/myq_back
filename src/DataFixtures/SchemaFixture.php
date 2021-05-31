<?php

namespace App\DataFixtures;

use App\Entity\Schedule;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SchemaFixture extends Fixture implements DependentFixtureInterface
{

    const SCHEDULE_1 = 'schedule_1';
    const SCHEDULE_2 = 'schedule_2';

    public function load(ObjectManager $manager)
    {
        $company1 = $this->getReference(CompanyFixture::COMPANY_1);

        $schedule1 = new Schedule();
        $schedule1
            ->setName('Schedule 1')
            ->setDescription('schedule 1 test description')
            ->setCompany($company1);
        $manager->persist($schedule1);

        $schedule2 = new Schedule();
        $schedule2
            ->setName('Schedule 1')
            ->setDescription('schedule 1 test description')
            ->setCompany($company1)
            ->setBookingDuration(0)
            ->setMinBookingTime(20)
            ->setMaxBookingTime(40);
        $manager->persist($schedule2);

        $manager->flush();

        $this->addReference(self::SCHEDULE_1, $schedule1);
        $this->addReference(self::SCHEDULE_2, $schedule2);
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return [
            CompanyFixture::class
        ];
    }
}
