<?php

namespace App\Tests\Functional\Controller;

class ScheduleControllerTest extends AbstractBaseController
{

    public function getMyCompany($token)
    {
        $response = $this->sendGetRequest('/api/companies/my', [], ['token' => $token]);
        $this->assertSuccessResponse($response);
        $myCompanyContent = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $myCompanyContent);

        return $myCompanyContent;
    }

    public function getMySchedules($token)
    {
        $response = $this->sendGetRequest('/api/schedule/search/my', ['filter' => []], ['token' => $token]);

        return $this->assertSuccessEntitiesArray($response);
    }

    public function testCreateScheduleSuccess()
    {
        $token = $this->login([
            'username' => 'user1@site.com',
            'password' => '12345678',
        ]);

        $myCompany = $this->getMyCompany($token);

        $data = [
            'accept_booking_condition' => 0,
            'available' => false,
            'booking_condition' => 0,
            'booking_duration' => 30,
            'description' => "",
            'enabled' => true,
            'max_booking_time' => 0,
            'min_booking_time' => 0,
            'name' => "Test Schedule 1",
            'accept_booking_time' => 0,
            'time_between_bookings' => 0,
        ];
        $response = $this->sendPostRequest('/api/schedule/', [], $data + ['token' => $token, 'company' => $myCompany['id']]);
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
            'username' => 'user1@site.com',
            'password' => '12345678',
        ]);

        $schedules = $this->getMySchedules($token);
        $schedule = $schedules[array_key_first($schedules)];


        $data = [
            'accept_booking_condition' => 1,
            'available' => false,
            'booking_condition' => 0,
            'booking_duration' => 35,
            'description' => "Test schedule description UPDATE",
            'enabled' => false,
            'max_booking_time' => 0,
            'min_booking_time' => 0,
            'name' => "Test Schedule 1 UPDATE",
            'accept_booking_time' => 5,
            'time_between_bookings' => 1,
            'company' => $schedule['company']['id']
        ];
        $response = $this->sendPutRequest('/api/schedule/'.$schedule['id'], [], $data + ['token' => $token]);
        $this->assertSuccessResponse($response);
        $content = $response->getContent();
        $content = json_decode($content, true);
        foreach ($data as $dataKey => $dataValue) {
            $this->assertArrayHasKey($dataKey, $content);
            if ($dataKey !== 'company') {
                $this->assertEquals($content[$dataKey], $dataValue);
            }
        }
        $this->assertEquals($data['company'], $content['company']['id']);
    }
}
