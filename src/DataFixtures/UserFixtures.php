<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFixtures.
 */
class UserFixtures extends Fixture
{
    const ADMIN_USER_REFERENCE = 'admin-user';
    const USER_USER_REFERENCE = 'user';

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $userPasswordEncoder;

    /**
     * UserFixtures constructor.
     *
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $defaultPassword = '12345678';
        //create admin user
        $admin = new User();
        $admin->setEmail('admin@site.com')
            ->setNickname('admin1')
            ->setRoles([User::ROLE_USER, User::ROLE_ADMIN])
            ->setPassword($this->userPasswordEncoder->encodePassword($admin, $defaultPassword))
            ->setStatus(User::STATUS_ON)
            ->setPhone('123212131');
        $manager->persist($admin);
        // create regular user
        $user = new User();
        $user->setEmail('user1@site.com')
            ->setRoles([User::ROLE_USER])
            ->setPassword($this->userPasswordEncoder->encodePassword($user, $defaultPassword))
            ->setNickname('user1')
            ->setStatus(User::STATUS_ON)
            ->setPhone('22333112344');
        $manager->persist($user);

        $manager->flush();

        $this->addReference(self::ADMIN_USER_REFERENCE, $admin);
        $this->addReference(self::USER_USER_REFERENCE, $user);
    }
}
