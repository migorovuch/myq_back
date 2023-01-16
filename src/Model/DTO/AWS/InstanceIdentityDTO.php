<?php

namespace App\Model\DTO\AWS;

use DateTime;
use JMS\Serializer\Annotation as Serializer;

class InstanceIdentityDTO
{

    /**
     * @Serializer\Type("string")
     */
    private ?string $accountId = null;
    /**
     * @Serializer\Type("string")
     */
    private ?string $architecture = null;
    /**
     * @Serializer\Type("string")
     */
    private ?string $availabilityZone = null;
    /**
     * @Serializer\Type("string")
     */
    private ?string $imageId = null;
    /**
     * @Serializer\Type("string")
     */
    private ?string $instanceId = null;
    /**
     * @Serializer\Type("string")
     */
    private ?string $instanceType = null;
    /**
     * @Serializer\Type("DateTime<'U'>")
     */
    private ?DateTime $pendingTime = null;
    /**
     * @Serializer\Type("string")
     */
    private ?string $region = null;
    /**
     * @Serializer\Type("string")
     */
    private ?string $version = null;

    /**
     * @param string|null $accountId
     * @param string|null $architecture
     * @param string|null $availabilityZone
     * @param string|null $imageId
     * @param string|null $instanceId
     * @param string|null $instanceType
     * @param DateTime|null $pendingTime
     * @param string|null $region
     * @param string|null $version
     */
    public function __construct(
        ?string $accountId,
        ?string $architecture,
        ?string $availabilityZone,
        ?string $imageId,
        ?string $instanceId,
        ?string $instanceType,
        ?DateTime $pendingTime,
        ?string $region,
        ?string $version
    ) {
        $this->accountId = $accountId;
        $this->architecture = $architecture;
        $this->availabilityZone = $availabilityZone;
        $this->imageId = $imageId;
        $this->instanceId = $instanceId;
        $this->instanceType = $instanceType;
        $this->pendingTime = $pendingTime;
        $this->region = $region;
        $this->version = $version;
    }

    /**
     * @return string|null
     */
    public function getAccountId(): ?string
    {
        return $this->accountId;
    }

    /**
     * @return string|null
     */
    public function getArchitecture(): ?string
    {
        return $this->architecture;
    }

    /**
     * @return string|null
     */
    public function getAvailabilityZone(): ?string
    {
        return $this->availabilityZone;
    }

    /**
     * @return string|null
     */
    public function getImageId(): ?string
    {
        return $this->imageId;
    }

    /**
     * @return string|null
     */
    public function getInstanceId(): ?string
    {
        return $this->instanceId;
    }

    /**
     * @return string|null
     */
    public function getInstanceType(): ?string
    {
        return $this->instanceType;
    }

    /**
     * @return DateTime|null
     */
    public function getPendingTime(): ?DateTime
    {
        return $this->pendingTime;
    }

    /**
     * @return string|null
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }
}