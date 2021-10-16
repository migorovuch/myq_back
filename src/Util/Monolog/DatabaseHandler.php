<?php

namespace App\Util\Monolog;

use App\Entity\Log;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\Exception\Exception as SerializerException;
use JMS\Serializer\SerializerInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Symfony\Component\Security\Core\Security;

class DatabaseHandler extends AbstractProcessingHandler
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected Security $security,
        protected SerializerInterface $serializer
    ) {
        parent::__construct();
    }

    /**
     * @param array $record
     */
    protected function write(array $record): void
    {
        $log = new Log();
        try {
            $log->setContext($this->serializer->serialize($record['context'], 'json'));
        } catch (SerializerException $exception) {
            $log->setContext('Serialization data exception: '.$exception->getMessage());
        }
        try {
            $log->setExtra($this->serializer->serialize($record['extra'], 'json'));
        } catch (SerializerException $exception) {
            $log->setExtra('Serialization data exception: '.$exception->getMessage());
        }
        $log->setMessage($record['message']);
        $log->setLevel($record['level']);
        $log->setLevelName($record['level_name']);
        $log->setChannel($record['channel']);
        $log->setFormatted($record['formatted']);

        $user = $this->security->getUser();

        if ($user instanceof User) {
            $log->setUserId($user->getId());
        }

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}
