<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractBaseControllerTest.
 */
class AbstractBaseController extends WebTestCase
{
    /**
     * @param array $data
     *
     * @return array
     */
    protected function login(array $data)
    {
        $client = $this->sendPostRequest('/api/login', [], $data);
        $response = $client->getResponse();
        $content = $response->getContent();

        return json_decode($content, true);
    }

    /**
     * @param string $url
     * @param array  $parameters
     * @param array  $data
     *
     * @return Client
     */
    protected function sendGetRequest(string $url, array $parameters = [], array $data = [])
    {
        return $this->sendRequest('GET', $url, $parameters, $data);
    }

    /**
     * @param string $url
     * @param array  $parameters
     * @param $data
     *
     * @return Client
     */
    protected function sendPostRequest(string $url, array $parameters, array $data)
    {
        return $this->sendRequest('POST', $url, $parameters, $data);
    }

    /**
     * @param string $url
     * @param array  $parameters
     * @param $data
     *
     * @return Client
     */
    protected function sendPutRequest(string $url, array $parameters, array $data)
    {
        return $this->sendRequest('PUT', $url, $parameters, $data);
    }

    /**
     * @param string $url
     * @param array  $parameters
     * @param $data
     *
     * @return Client
     */
    protected function sendPatchRequest(string $url, array $parameters, array $data)
    {
        return $this->sendRequest('PATCH', $url, $parameters, $data);
    }

    /**
     * @param string $url
     * @param array  $parameters
     * @param $data
     *
     * @return Client
     */
    protected function sendDeleteRequest(string $url, array $parameters = [], array $data = [])
    {
        return $this->sendRequest('DELETE', $url, $parameters, $data);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array  $parameters
     * @param array  $data
     *
     * @return Client
     */
    protected function sendRequest(string $method, string $url, array $parameters = [], array $data = [])
    {
        $client = static::createClient();
        $headerData = ['CONTENT_TYPE' => 'application/json'];
        if (!empty($data['token'])) {
            $headerData['HTTP_Authorization'] = 'Bearer '.$data['token'];
            unset($data['token']);
        }
        $client->request(
            $method,
            $url,
            $parameters,
            [],
            $headerData,
            json_encode($data)
        );

        return $client;
    }

    /**
     * @param Response $response
     */
    protected function assertSuccessResponse(Response $response)
    {
        $content = json_decode($response->getContent(), true);
        $errorMsg = isset($content['title']) ? $content['title'] : 'Incorrect response code';
        $this->assertEquals(200, $response->getStatusCode(), $errorMsg);
    }
}
