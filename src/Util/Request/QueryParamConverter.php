<?php

namespace App\Util\Request;

use App\Exception\ValidationFailedException;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class QueryParamConverter.
 */
class QueryParamConverter implements ParamConverterInterface
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
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * QueryParamConverter constructor.
     *
     * @param ValidatorInterface  $validator
     * @param Security            $security
     * @param SerializerInterface $serializer
     */
    public function __construct(ValidatorInterface $validator, Security $security, SerializerInterface $serializer)
    {
        $this->validator = $validator;
        $this->security = $security;
        $this->serializer = $serializer;
    }

    /**
     * Stores the object in the request.
     *
     * @param Request        $request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $option = $configuration->getOptions();
        $param = $option['paramName'];
        $className = $configuration->getClass();
        $object = null;
        if ($request->query->has($param)) {
            $object = $this->serializer->deserialize(
                $this->serializer->serialize(
                    $request->query->get($param),
                    'json'
                ),
                $className,
                'json'
            );
            $validationGroups = [];
            if (!empty($option['validationGroups'])) {
                $validationGroups = $option['validationGroups'];
                $validationGroups = explode(',', $validationGroups);
            }
            if (!empty($option['validationGroupsRole'])) {
                $validationGroupsRole = $option['validationGroupsRole'];
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
                $validationErrors = $this->validator->validate($object, null, $validationGroups);
            }
            if ($validationErrors && $validationErrors->count()) {
                throw new ValidationFailedException($validationErrors);
            }
        } else {
            $object = new $className();
        }
        $request->attributes->set($configuration->getName(), $object);
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return class_exists($configuration->getClass()) && !empty($configuration->getOptions()['paramName']);
    }
}
