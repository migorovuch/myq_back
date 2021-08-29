<?php

namespace App\Tests\Functional\Controller;

/**
 * Class AuthControllerTest.
 */
class AuthControllerTest extends AbstractBaseController
{
    /**
     * @param array $data
     */
    public function failureLogin(array $data)
    {
        $response = $this->sendPostRequest('/api/login_check', [], $data);
        $content = $response->getContent();
        $content = json_decode($content, true);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertArrayHasKey('message', $content);
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function successLogin(array $data)
    {
        $response = $this->sendPostRequest('/api/login_check', [], $data);
        $content = $response->getContent();
        $content = json_decode($content, true);
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('token', $content);
        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('id', $content['data']);
        $this->assertArrayHasKey('fullName', $content['data']);
        $this->assertArrayHasKey('phone', $content['data']);
        $this->assertArrayHasKey('email', $content['data']);
        $this->assertArrayHasKey('nickname', $content['data']);

        return $content['token'];
    }

    /**
     * @param array $data
     */
    public function failureRegistration(array $data)
    {
        $response = $this->sendPostRequest('/api/registration', [], $data);
        $content = $response->getContent();
        $content = json_decode($content, true);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $content);
        $this->assertCount(4, $content['errors']);
    }

    /**
     * @param array  $data
     * @param string $role
     */
    public function successRegistration(array $data, string $role)
    {
        $response = $this->sendPostRequest('/api/registration', [], $data);
        $content = $response->getContent();
        $content = json_decode($content, true);
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('id', $content);
        $this->assertContains($role, $content['roles']);
        $nickname = explode('@', $data['email']);
        $nickname = $nickname[0];
        $this->assertEquals($nickname, $content['nickname']);
    }

    public function testFailureAdminLogin()
    {
        $data = [
            'username' => 'admin1232123@site.com',
            'password' => '12345',
        ];
        $this->failureLogin($data);
    }

    /**
     * @return mixed
     */
    public function testSuccessAdminLogin()
    {
        $data = [
            'username' => 'admin@site.com',
            'password' => '12345678',
        ];

        return $this->successLogin($data);
    }

    public function testSuccessRegistration()
    {
        $data = [
            'full_name' => 'Test User 123',
            'nickname' => '-',
            'email' => 'testuser123@site.com',
            'password' => '12345678',
            'roles' => ['ROLE_USER'],
        ];

        $this->successRegistration($data, 'ROLE_USER');
    }

    public function testFailureUnapprovedLogin()
    {
        $data = [
            'username' => 'testuser123@site.com',
            'password' => '12345678',
        ];
        $this->failureLogin($data);
    }
}
