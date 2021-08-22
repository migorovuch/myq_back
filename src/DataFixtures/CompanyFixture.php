<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CompanyFixture extends Fixture implements DependentFixtureInterface
{

    const COMPANY_1 = 'company_1';
    const COMPANY_2 = 'company_2';

    public function load(ObjectManager $manager)
    {
        $companyUser1 = $this->getReference(UserFixtures::COMPANY_USER_REFERENCE);
        $company1 = new Company();
        $company1->setUser($companyUser1)
            ->setAddress('test address1')
            ->setDescription('test description')
            ->setEmail('company1@gmail.com')
            ->setName('Company 1')
            ->setPhone('9823094823094')
            ->setStatus(Company::STATUS_ON);

        $manager->persist($company1);

        $companyUser2 = $this->getReference(UserFixtures::COMPANY_USER_REFERENCE2);
        $company2 = new Company();
        $company2->setUser($companyUser2)
            ->setAddress('test address2')
            ->setDescription('test description2')
            ->setEmail('company2@gmail.com')
            ->setName('Company 2')
            ->setPhone('9823094823094')
            ->setStatus(Company::STATUS_ON);

        $manager->persist($company2);

        $manager->flush();

        $this->addReference(self::COMPANY_1, $company1);
        $this->addReference(self::COMPANY_2, $company2);
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
