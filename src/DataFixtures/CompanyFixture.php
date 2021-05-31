<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CompanyFixture extends Fixture implements DependentFixtureInterface
{

    const COMPANY_1 = 'company_1';

    public function load(ObjectManager $manager)
    {
        $admin = $this->getReference(UserFixtures::ADMIN_USER_REFERENCE);
        $company1 = new Company();
        $company1->setUser($admin)
            ->setAddress('test address1')
            ->setDescription('test description')
            ->setEmail('company1@gmail.com')
            ->setName('Company 1')
            ->setPhone('9823094823094')
            ->setStatus(Company::STATUS_ON);

        $manager->persist($company1);
        $manager->flush();

        $this->addReference(self::COMPANY_1, $company1);
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
