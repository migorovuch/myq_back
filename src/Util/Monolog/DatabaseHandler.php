<?php

namespace App\Util\Monolog;

use App\Entity\Log;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Symfony\Component\Security\Core\Security;

class DatabaseHandler extends AbstractProcessingHandler
{

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected Security $security
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
        $log->setContext($record['context']);
        $log->setLevel($record['level']);
        $log->setLevelName($record['level_name']);
        $log->setChannel($record['channel']);
        $log->setExtra($record['extra']);
        $log->setFormatted($record['formatted']);

        $user = $this->security->getUser();

        if ($user instanceof User) {
            $log->setUser($user);
        }

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}
