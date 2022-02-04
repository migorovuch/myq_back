<?php

namespace App\Tests\Functional\Controller;

class CompanyControllerTest extends AbstractBaseController
{
    public function testCreateCompanySuccess()
    {
        $token = $this->login([
            'username' => 'user1@site.com',
            'password' => '12345678',
        ]);
        $data = [
            'name' => 'Test company',
            'email' => 'test@companu.com',
            'phone' => '+380983726299',
            'address' => 'Test company address',
            'address_link' => '',
            'slug' => 'company',
            'description' => 'Test company description',
        ];
        $response = $this->sendPostRequest('/api/companies/', [], $data + ['token' => $token]);
        $this->assertSuccessResponse($response);
        $content = $response->getContent();
        $content = json_decode($content, true);
        $this->assertArrayHasKey('id', $content);
        foreach ($data as $dataKey => $dataValue) {
            $this->assertArrayHasKey($dataKey, $content);
            $this->assertEquals($content[$dataKey], $dataValue);
        }
    }

    public function testUpdateCompanySuccess()
    {
        $token = $this->login([
            'username' => 'company1@site.com',
            'password' => '12345678',
        ]);
        $response = $this->sendGetRequest('/api/companies/my', [], ['token' => $token]);
        $this->assertSuccessResponse($response);
        $myCompanyContent = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $myCompanyContent);

        $data = [
            'name' => 'Test company UPDATE',
            'email' => 'testupdate@companu.com',
            'phone' => '+380983726277',
            'address' => 'Test company address UPDATE',
            'address_link' => 'UPDATE',
            'slug' => 'company1',
            'description' => 'Test company description UPDATE',
        ];
        $response = $this->sendPutRequest('/api/companies/'.$myCompanyContent['id'], [], $data + ['token' => $token]);
        $this->assertSuccessResponse($response);
        $content = $response->getContent();
        $content = json_decode($content, true);
        foreach ($data as $dataKey => $dataValue) {
            $this->assertArrayHasKey($dataKey, $content);
            $this->assertEquals($content[$dataKey], $dataValue);
        }
    }
}
