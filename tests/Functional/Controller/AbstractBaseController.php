<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractBaseControllerTest.
 */
class AbstractBaseController extends WebTestCase
{
    protected static KernelBrowser $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = static::createClient();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function login(array $data)
    {
        $this->sendPostRequest('/api/login_check', [], $data);
        $response = self::$client->getResponse();
        $this->assertSuccessResponse($response);
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $content);

        return $content['token'];
    }

    /**
     * @param string $url
     * @param array $parameters
     * @param array $data
     * @return Response
     */
    protected function sendGetRequest(string $url, array $parameters = [], array $data = [])
    {
        $this->sendRequest('GET', $url, $parameters, $data);

        return self::$client->getResponse();
    }

    /**
     * @param string $url
     * @param array $parameters
     * @param array $data
     * @return Response
     */
    protected function sendPostRequest(string $url, array $parameters, array $data)
    {
        $this->sendRequest('POST', $url, $parameters, $data);

        return self::$client->getResponse();
    }

    /**
     * @param string $url
     * @param array $parameters
     * @param array $data
     * @return Response
     */
    protected function sendPutRequest(string $url, array $parameters, array $data)
    {
        $this->sendRequest('PUT', $url, $parameters, $data);

        return self::$client->getResponse();
    }

    /**
     * @param string $url
     * @param array $parameters
     * @param array $data
     * @return Response
     */
    protected function sendPatchRequest(string $url, array $parameters, array $data)
    {
        $this->sendRequest('PATCH', $url, $parameters, $data);

        return self::$client->getResponse();
    }

    /**
     * @param string $url
     * @param array $parameters
     * @param array $data
     * @return Response
     */
    protected function sendDeleteRequest(string $url, array $parameters = [], array $data = [])
    {
        $this->sendRequest('DELETE', $url, $parameters, $data);

        return self::$client->getResponse();
    }

    /**
     * @param string $method
     * @param string $url
     * @param array  $parameters
     * @param array  $data
     */
    protected function sendRequest(string $method, string $url, array $parameters = [], array $data = [])
    {
        $headerData = ['CONTENT_TYPE' => 'application/json'];
        if (!empty($data['token'])) {
            $headerData['HTTP_Authorization'] = 'Bearer '.$data['token'];
            unset($data['token']);
        }
        self::$client->request(
            $method,
            $url,
            $parameters,
            [],
            $headerData,
            json_encode($data)
        );
    }

    /**
     * @param Response $response
     */
    protected function assertSuccessResponse(Response $response)
    {
        $content = json_decode($response->getContent(), true);
        $errorMsg = $content['title'] ?? 'Incorrect response code';
        $this->assertEquals(200, $response->getStatusCode(), $errorMsg);
    }

    /**
     * @param Response $response
     * @return array
     */
    protected function assertSuccessEntitiesArray(Response $response): array
    {
        $this->assertSuccessResponse($response);
        $listContent = json_decode($response->getContent(), true);
        $this->assertIsArray($listContent);
        $this->assertNotEmpty($listContent);
        if (isset($listContent['data'], $listContent['total'])) {
            $listContent = $listContent['data'];
        }
        $firstKey = array_key_first($listContent);
        $this->assertArrayHasKey('id', $listContent[$firstKey]);

        return $listContent;
    }
}
