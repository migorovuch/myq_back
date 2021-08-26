<?php

namespace App\Util\Monolog;

use App\Entity\Log;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
        $log->setMessage($record['message']);
        $log->setContext($this->serializer->serialize($record['context'],'json'));
        $log->setLevel($record['level']);
        $log->setLevelName($record['level_name']);
        $log->setChannel($record['channel']);
        $log->setExtra($this->serializer->serialize($record['extra'],'json'));
        $log->setFormatted($record['formatted']);

        $user = $this->security->getUser();

        if ($user instanceof User) {
            $log->setUser($user);
        }

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}
