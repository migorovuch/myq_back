<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\CompanyClient;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CompanyClientFixture extends Fixture implements DependentFixtureInterface
{
    const CLIENTS_COUNT = 10;
    const COMPANY_CLIENT_ = 'company_client_';

    public function load(ObjectManager $manager)
    {
        /** @var User $user1 */
        $user1 = $this->getReference(UserFixtures::USER_USER_REFERENCE);
        /** @var Company $company1 */
        $company1 = $this->getReference(CompanyFixture::COMPANY_1);

        $companyClient = new CompanyClient();
        $companyClient
            ->setCompany($company1)
            ->setStatus(CompanyClient::STATUS_ON)
            ->setPseudonym($user1->getFullName())
            ->setUser($user1);
        $manager->persist($companyClient);
        $manager->flush();

        $this->addReference(self::COMPANY_CLIENT_.'1', $companyClient);

        for ($i = 2; $i <= self::CLIENTS_COUNT; ++$i) {
            $companyClient = new CompanyClient();
            $companyClient
                ->setCompany($company1)
                ->setStatus(CompanyClient::STATUS_ON)
                ->setName('Test User name'.$i)
                ->setPseudonym('Test User name'.$i)
                ->setPhone('11111'.$i);
            $manager->persist($companyClient);
            $manager->flush();

            $this->addReference(self::COMPANY_CLIENT_.$i, $companyClient);
        }
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            CompanyFixture::class,
        ];
    }
}
