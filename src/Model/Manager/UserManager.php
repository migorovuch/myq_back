<?php

namespace App\Model\Manager;

use App\Entity\User;
use App\Exception\ApiException;
use App\Model\DTO\DTOInterface;
use App\Model\DTO\User\ChangePasswordDTO;
use App\Model\Model\EntityInterface;
use App\Repository\UserRepository;
use App\Util\DTOExporter\DTOExporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * Class UserManager.
 */
class UserManager extends AbstractCRUDManager implements UserManagerInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    protected $userPasswordEncoder;

    /**
     * @var ResetPasswordHelperInterface
     */
    protected $resetPasswordHelper;

    /**
     * @var MailerInterface
     */
    protected $mailer;
    /**
     * @var string
     */
    protected $appName;
    /**
     * @var string
     */
    protected $appEmail;

    /**
     * UserManager constructor.
     *
     * @param EntityManagerInterface       $entityManager
     * @param UserRepository               $userRepository
     * @param Security                     $security
     * @param DTOExporterInterface         $userDtoExporter
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param ResetPasswordHelperInterface $resetPasswordHelper
     * @param MailerInterface              $mailer
     * @param string                       $appName
     * @param string                       $appEmail
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        Security $security,
        DTOExporterInterface $userDtoExporter,
        UserPasswordEncoderInterface $userPasswordEncoder,
        ResetPasswordHelperInterface $resetPasswordHelper,
        MailerInterface $mailer,
        string $appName,
        string $appEmail
    ) {
        parent::__construct($entityManager, $userRepository, $security, $userDtoExporter);
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->mailer = $mailer;
        $this->appName = $appName;
        $this->appEmail = $appEmail;
    }

    /**
     * {@inheritdoc}
     */
    public function googleAuthentication(DTOInterface $data)
    {
        $user = $this->findBy(['googleTockenId' => $data->getGoogleTockenId()]);
        if (!$user) {
            $user = new User();
            /** @var User $user */
            $user = $this->DTOExporter->exportDTO($user, $data);
            $user->setPassword(uniqid());
        } else {
            $user = $user[0];
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername(string $username)
    {
        return $this->entityRepository->loadUserByUsername($username);
    }

    /**
     * {@inheritdoc}
     */
    public function processSendingPasswordResetEmail(string $emailFormData)
    {
        $user = $this->findOneBy(
            [
                'email' => $emailFormData,
            ]
        );
        $resetToken = null;
        // Do not reveal whether a user account was found or not.
        if ($user) {
            try {
                $resetToken = $this->resetPasswordHelper->generateResetToken($user);
                $email = (new TemplatedEmail())
                    ->from(new Address($this->appEmail, $this->appName))
                    ->to($user->getEmail())
                    ->subject('Your password reset request')
                    ->htmlTemplate('reset_password/email.html.twig')
                    ->context(
                        [
                            'resetToken' => $resetToken,
                            'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
                        ]
                    );
                $this->mailer->send($email);
            } catch (ResetPasswordExceptionInterface $e) {
                throw new ApiException(sprintf('There was a problem handling your password reset request - %s', $e->getReason()));
            } catch (\Throwable $exception) {
                if ($resetToken) {
                    $this->resetPasswordHelper->removeResetRequest($resetToken->getToken());
                    throw $exception;
                }
            }
        }

        return $this->resetPasswordHelper->getTokenLifetime();
    }

    /**
     * {@inheritdoc}
     */
    public function resetPassword(ChangePasswordDTO $changePasswordDTO)
    {
        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($changePasswordDTO->getToken());
        } catch (ResetPasswordExceptionInterface $e) {
            throw new ApiException(sprintf('There was a problem validating your reset request - %s', $e->getReason()));
        }

        // A password reset token should be used only once, remove it.
        $this->resetPasswordHelper->removeResetRequest($changePasswordDTO->getToken());

        // Encode the plain password, and set it.
        $encodedPassword = $this->userPasswordEncoder->encodePassword(
            $user,
            $changePasswordDTO->getPassword()
        );

        $user->setPassword($encodedPassword);
        $this->save($user);
    }

    /**
     * @param User $entity
     * @param DTOInterface $dto
     * @param bool $setNullProperty
     * @return EntityInterface
     */
    protected function prepareEntity(
        EntityInterface $entity,
        DTOInterface $dto,
        bool $setNullProperty = true
    ): EntityInterface {
        $oldStatus = $entity->getStatus();
        $entity = parent::prepareEntity($entity, $dto, $setNullProperty);
        $entity->setPassword($this->userPasswordEncoder->encodePassword($entity, $entity->getPassword()));
        if (!$this->security->isGranted(User::ROLE_ADMIN)) {
            $entity->setStatus($oldStatus);
        }

        return $entity;
    }
}
