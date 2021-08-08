<?php

namespace App\Model\Manager;

use App\Entity\User;
use App\Exception\AccessDeniedException;
use App\Exception\ApiException;
use App\Model\DTO\DTOInterface;
use App\Model\DTO\User\ApproveEmailDTO;
use App\Model\DTO\User\ChangePasswordDTO;
use App\Model\DTO\User\ChangeUserDTO;
use App\Model\Model\EntityInterface;
use App\Repository\UserRepository;
use App\Util\DTOExporter\DTOExporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @var string
     */
    protected string $appUrl;

    /**
     * @var string
     */
    protected string $signingKey;

    /**
     * @var TranslatorInterface
     */
    protected TranslatorInterface $translator;
    private CompanyClientManagerInterface $companyClientManager;

    /**
     * UserManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param Security $security
     * @param DTOExporterInterface $userDtoExporter
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param ResetPasswordHelperInterface $resetPasswordHelper
     * @param MailerInterface $mailer
     * @param TranslatorInterface $translator
     * @param CompanyClientManagerInterface $companyClientManager
     * @param string $appName
     * @param string $appEmail
     * @param string $appUrl
     * @param string $signingKey
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        Security $security,
        DTOExporterInterface $userDtoExporter,
        UserPasswordEncoderInterface $userPasswordEncoder,
        ResetPasswordHelperInterface $resetPasswordHelper,
        MailerInterface $mailer,
        TranslatorInterface $translator,
        CompanyClientManagerInterface $companyClientManager,
        string $appName,
        string $appEmail,
        string $appUrl,
        string $signingKey
    ) {
        parent::__construct($entityManager, $userRepository, $security, $userDtoExporter);
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->appName = $appName;
        $this->appEmail = trim($appEmail);
        $this->appUrl = $appUrl;
        $this->signingKey = $signingKey;
        $this->companyClientManager = $companyClientManager;
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function loadUserByUsername(string $username)
    {
        return $this->entityRepository->loadUserByUsername($username);
    }

    /**
     * @inheritdoc
     */
    public function registration(DTOInterface $data): EntityInterface
    {
        /** @var User $user */
        $user = parent::create($data);
        $confirmationLink = $this->appUrl . '#/approve-email/' . $user->getId() . '/' . urlencode($this->createToken($user));
        $email = (new TemplatedEmail())
            ->from(new Address($this->appEmail, $this->appName))
            ->to($user->getEmail())
            ->subject($this->translator->trans('Confirm your account on %appName%',['%appName%' => $this->appName]))
            ->htmlTemplate('user/registration_email.html.twig')
            ->context(
                [
                    'confirmationLink' => $confirmationLink,
                    'userName' => $user->getFullName(),
                    'appName' => $this->appName
                ]
            );
        $this->mailer->send($email);

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function approveEmail(ApproveEmailDTO $approveEmailDTO): ?User
    {
        /** @var User $user */
        $user = $this->entityRepository->findOneBy(['id' => $approveEmailDTO->getId(), 'status' => User::STATUS_OFF]);
        if ($user) {
            $checkToken = $this->createToken($user);
            if (hash_equals($checkToken, urldecode($approveEmailDTO->getToken()))) {
                $user->setStatus(User::STATUS_ON);
                $this->save($user);
            }
        }

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function processSendingPasswordResetEmail(string $emailFormData)
    {
        /** @var User $user */
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
                $resetLink = $this->appUrl . '#/reset-password/' . urlencode($resetToken->getToken());
                $email = (new TemplatedEmail())
                    ->from(new Address($this->appEmail, $this->appName))
                    ->to($user->getEmail())
                    ->subject($this->translator->trans('Your password reset request'))
                    ->htmlTemplate('reset_password/email.html.twig')
                    ->context(
                        [
                            'resetLink' => $resetLink,
                            'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
                            'userName' => $user->getFullName()
                        ]
                    );
                $this->mailer->send($email);
            } catch (ResetPasswordExceptionInterface $e) {
                throw new ApiException(
                    $this->translator->trans(
                        'There was a problem handling your password reset request - %reason%',
                        ['%reason%' => $e->getReason()]
                    )
                );
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
     * @inheritdoc
     */
    public function resetPassword(ChangePasswordDTO $changePasswordDTO)
    {
        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($changePasswordDTO->getToken());
        } catch (ResetPasswordExceptionInterface $e) {
            throw new ApiException(
                $this->translator->trans(
                    'There was a problem validating your reset request - %reason%',
                    ['%reason%' => $e->getReason()]
                )
            );
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
     * @inheritDoc
     */
    public function changeAccount(ChangeUserDTO $data): EntityInterface
    {
        /** @var User $user */
        $user = $this->find($this->security->getUser()->getId());
        if ($data->getPassword() && $data->getNewPassword()) {
            $password = $this->userPasswordEncoder->encodePassword($user, $data->getNewPassword());
            $user->setPassword($password);
        }
        $user
            ->setPhone($data->getPhone())
            ->setFullName($data->getFullName())
            ->setNickname($data->getNickname());
        $this->save($user);
        $this->companyClientManager->changeClientDetails($user);

        return $user;
    }

    /**
     * @param UserInterface $user
     * @return string
     */
    protected function createToken(UserInterface $user): string
    {
        $encodedData = json_encode([$user->getId(), $user->getEmail()]);

        return base64_encode(hash_hmac('sha256', $encodedData, $this->signingKey, true));
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
