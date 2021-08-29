<?php

namespace App\Tests\Functional\Controller;

class AccountControllerTest extends AbstractBaseController
{
    public function login(array $data)
    {
        $this->sendPostRequest('/api/login_check', [], $data);
        $response = self::$client->getResponse();
        $this->assertSuccessResponse($response);
        $loginContent = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $loginContent);
        $this->assertArrayHasKey('data', $loginContent);

        return $loginContent;
    }

    public function testChangeMySuccess()
    {
        $data = [
            'username' => 'test_user1@site.com',
            'password' => '12345678',
        ];
        $loginContent = $this->login($data);
        // TODO: use faker
        $newAccountData = [
            'id' => $loginContent['data']['id'],
            'nickname' => $loginContent['data']['nickname'].'1',
            'full_name' => $loginContent['data']['fullName'].'1',
            'phone' => $loginContent['data']['phone'].'1',
            'email' => '1'.$loginContent['data']['email'],
        ];
        $response = $this->sendPutRequest(
            '/api/account/',
            [],
            $newAccountData +
            [
                'token' => $loginContent['token'],
            ]
        );
        $this->assertSuccessResponse($response);
        $accountContent = json_decode($response->getContent(), true);
        foreach ($newAccountData as $key => $value) {
            $this->assertArrayHasKey($key, $accountContent);
            $this->assertEquals($value, $accountContent[$key]);
        }
    }

    public function testChangeMyFailure()
    {
        $data = [
            'username' => 'company1',
            'password' => '12345678',
        ];
        $loginContent = $this->login($data);
        $newAccountData = [
            'id' => $loginContent['data']['id'],
            'nickname' => 'admin1',
            'email' => 'admin@site.com',
        ];
        $response = $this->sendPutRequest(
            '/api/account/',
            [],
            $newAccountData +
            [
                'token' => $loginContent['token'],
            ]
        );
        $accountContent = json_decode($response->getContent(), true);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $accountContent);
        $this->assertCount(4, $accountContent['errors']);
    }
}
