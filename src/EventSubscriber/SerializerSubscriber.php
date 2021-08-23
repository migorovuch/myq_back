<?php

namespace App\EventSubscriber;

use App\Exception\ValidationFailedException;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SerializerSubscriber.
 */
class SerializerSubscriber implements EventSubscriberInterface
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;
    /**
     * @var Security
     */
    protected $security;

    /**
     * SerializerSubscriber constructor.
     *
     * @param ValidatorInterface $validator
     * @param Security           $security
     */
    public function __construct(ValidatorInterface $validator, Security $security)
    {
        $this->validator = $validator;
        $this->security = $security;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ['event' => Events::POST_DESERIALIZE, 'method' => 'onPostDeserialize'],
        ];
    }

    /**
     * @param ObjectEvent $event
     *
     * @return ObjectEvent
     */
    public function onPostDeserialize(ObjectEvent $event)
    {
        $object = $event->getObject();
        $validationGroups = [];
        if ($event->getContext()->hasAttribute('validationGroups')) {
            $validationGroups = $event->getContext()->getAttribute('validationGroups');
            $validationGroups = explode(',', $validationGroups);
        }
        if ($event->getContext()->hasAttribute('validationGroupsRole')) {
            $validationGroupsRole = $event->getContext()->getAttribute('validationGroupsRole');
            $validationGroupsRoleIntersect = explode(',', $validationGroupsRole[array_key_first($validationGroupsRole)]);
            foreach ($this->security->getUser()->getRoles() as $role) {
                if (empty($validationGroupsRole[$role])) {
                    $validationGroupsRoleIntersect = [];
                    break;
                } else {
                    $validationGroupsRoleIntersect = array_intersect($validationGroupsRoleIntersect, explode(',', $validationGroupsRole[$role]));
                }
            }
            $validationGroups = array_merge($validationGroups, $validationGroupsRoleIntersect);
        }
        $validationErrors = null;
        if (!empty($validationGroups)) {
            if (\is_array($object)) {
                foreach ($object as $objectItem) {
                    $validationErrors = $this->validator->validate($objectItem, null, $validationGroups);
                }
            } else {
                $validationErrors = $this->validator->validate($object, null, $validationGroups);
            }
        }
        if ($validationErrors && $validationErrors->count()) {
            throw new ValidationFailedException($validationErrors);
        }

        return $event;
    }
}
