<?php

namespace App\Util\Adapter\JMS;

use App\Exception\EntryNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class RelationsHandler.
 */
class RelationsHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * RelationsHandler constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param $relation
     *
     * @return array|mixed
     */
    public function serializeRelation(JsonSerializationVisitor $visitor, $relation)
    {
        if ($relation instanceof \Traversable) {
            $relation = iterator_to_array($relation);
        }
        if (\is_array($relation)) {
            return array_map([$this, 'getSingleEntityRelation'], $relation);
        }

        return $this->getSingleEntityRelation($relation);
    }

    /**
     * @param JsonDeserializationVisitor $visitor
     * @param $relation
     * @param array $type
     *
     * @return array|object
     */
    public function deserializeRelation(JsonDeserializationVisitor $visitor, $relation, array $type)
    {
        $className = $type['params'][0]['name'] ?? null;
        $required = !((isset($type['params'][1]) && $type['params'][1] === 'notrequired'));
        if (!class_exists($className)) {
            throw new \InvalidArgumentException('Class name should be explicitly set for deserialization');
        }
        $metadata = $this->manager->getClassMetadata($className);
        if (!\is_array($relation)) {
            return $this->deserializeIdentifier($className, $relation, $required);
        }
        $single = false;
        if ($metadata->isIdentifierComposite) {
            $single = true;
            foreach ($metadata->getIdentifierFieldNames() as $idName) {
                $single = $single && \array_key_exists($idName, $relation);
            }
        }
        if ($single) {
            return $this->deserializeIdentifier($className, $relation, $required);
        }
        $objects = [];
        foreach ($relation as $idSet) {
            $objects[] = $this->deserializeIdentifier($className, $idSet, $required);
        }

        return $objects;
    }

    /**
     * @param $relation
     *
     * @return array|mixed
     */
    private function getSingleEntityRelation($relation)
    {
        $metadata = $this->manager->getClassMetadata(\get_class($relation));
        $ids = $metadata->getIdentifierValues($relation);
        if (1 === \count($metadata->getIdentifierFieldNames())) {
            $ids = array_shift($ids);
        }

        return $ids;
    }

    /**
     * @param string $className
     * @param mixed $identifier
     * @param bool $required
     * @return object
     */
    private function deserializeIdentifier(string $className, string $identifier, bool $required= true)
    {
        /*if (method_exists($this->manager, 'getReference')) {
            return $this->manager->getReference($className, $identifier);
        }*/
        $instance = $this->manager->find($className, $identifier);
        if (!$instance && $required) {
            throw new EntryNotFoundException("Relation {$className}:{$identifier} not found");
        }

        return $instance;
    }
}
