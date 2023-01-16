<?php

namespace App\Model\DTO\AWS;

use DateTime;
use JMS\Serializer\Annotation as Serializer;

class InstanceIdentityDTO
{

    /**
     * @Serializer\Type("string")
     */
    private string $accountId;
    /**
     * @Serializer\Type("string")
     */
    private string $architecture;
    /**
     * @Serializer\Type("string")
     */
    private string $availabilityZone;
    /**
     * @Serializer\Type("string")
     */
    private string $imageId;
    /**
     * @Serializer\Type("string")
     */
    private string $instanceId;
    /**
     * @Serializer\Type("string")
     */
    private string $instanceType;
    /**
     * @Serializer\Type("DateTime<'U'>")
     */
    private DateTime $pendingTime;
    /**
     * @Serializer\Type("string")
     */
    private string $region;
    /**
     * @Serializer\Type("string")
     */
    private string $version;

    public function __construct(
        string $accountId,
        string $architecture,
        string $availabilityZone,
        string $imageId,
        string $instanceId,
        string $instanceType,
        DateTime $pendingTime,
        string $region,
        string $version
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
     * @return string
     */
    public function getAccountId(): string
    {
        return $this->accountId;
    }

    /**
     * @return string
     */
    public function getArchitecture(): string
    {
        return $this->architecture;
    }

    /**
     * @return string
     */
    public function getAvailabilityZone(): string
    {
        return $this->availabilityZone;
    }

    /**
     * @return string
     */
    public function getImageId(): string
    {
        return $this->imageId;
    }

    /**
     * @return string
     */
    public function getInstanceId(): string
    {
        return $this->instanceId;
    }

    /**
     * @return string
     */
    public function getInstanceType(): string
    {
        return $this->instanceType;
    }

    /**
     * @return DateTime
     */
    public function getPendingTime(): DateTime
    {
        return $this->pendingTime;
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}