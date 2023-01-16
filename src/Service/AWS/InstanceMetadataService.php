<?php

namespace App\Service\AWS;

use App\Model\DTO\AWS\InstanceIdentityDTO;
use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class InstanceMetadataService
{
    const INSTANCE_METADATA_URL = 'http://169.254.169.254/';
    private HttpClientInterface $client;
    private SerializerInterface $serializer;

    public function __construct(HttpClientInterface $client, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    public function getInstanceIdentity(): InstanceIdentityDTO
    {
        $token = $this->generateToken();
        $response = $this->client->request(
            'GET',
            self::INSTANCE_METADATA_URL.'latest/dynamic/instance-identity/document',
            [
                'headers' => ['X-aws-ec2-metadata-token' => $token]
            ]
        );

        return $this->serializer->deserialize($response->getContent(), InstanceIdentityDTO::class, 'json');
    }

    protected function generateToken(): string
    {
        $response = $this->client->request(
            'GET',
            self::INSTANCE_METADATA_URL.'latest/api/token',
            [
                'headers' => [
                    'X-aws-ec2-metadata-token-ttl-seconds' => 21600
                ]
            ]
        );

        return $response->getContent();
    }
}