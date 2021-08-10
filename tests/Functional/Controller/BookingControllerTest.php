<?php

namespace App\Tests\Functional\Controller;

use DateTime;

/**
 * Class BookingControllerTest
 */
class BookingControllerTest extends AbstractBaseController
{
    /**
     * @param string $email
     * @return array
     */
    protected function findCompanyByEmail(string $email)
    {
        $this->sendGetRequest('/api/companies/search/app', ['filter' => ['email' => $email]]);

        return $this->assertSuccessEntitiesArray(self::$client->getResponse());
    }

    /**
     * @param array $filter
     * @return array
     */
    protected function findCompanySchedule(array $filter)
    {
        $this->sendGetRequest('/api/schedule/search/app', ['filter' => $filter]);

        return $this->assertSuccessEntitiesArray(self::$client->getResponse());
    }

    public function testCreateBookingUnauthorizedSuccess()
    {
        $companies = $this->findCompanyByEmail('company1@gmail.com');
        $company = $companies[array_key_first($companies)];
        $schedules = $this->findCompanySchedule(['company' => $company['id'], 'name' => 'Schedule 1']);
        $schedule = $schedules[array_key_first($schedules)];

        $now = new DateTime();
        $daysCount = 5;
        if (((int)$now->format('N')) + $daysCount > 5) {
            $daysCount += 2;
        }
        $start = (new DateTime())->modify($daysCount.' day')->setTime(11, 0, 0);
        $end = (new DateTime())->modify($daysCount.' day')->setTime(11, 30, 0);

        $response = $this->sendPostRequest(
            '/api/bookings/',
            [],
            [
                'schedule' => $schedule['id'],
                'start' => $start->getTimestamp(),
                'end' => $end->getTimestamp(),
                'customer_comment' => 'some customer comment',
                'user_name' => 'Test customer name',
                'user_phone' => '223123123123',
            ]
        );
        $this->assertSuccessResponse($response);
        $bookingContent = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $bookingContent);
        $this->assertArrayHasKey('client', $bookingContent, 'Client wasn\'t created');
        $this->assertNotEmpty($bookingContent['client'], 'Client property is empty');
        $this->assertArrayHasKey('id', $bookingContent['client'], 'Client wasn\'t created properly');
    }

    /**
     * Schedule should not be available for unauthorized users
     */
    public function testCreateBookingUnauthorizedFail()
    {
        $companies = $this->findCompanyByEmail('company1@gmail.com');
        $company = $companies[array_key_first($companies)];
        $schedules = $this->findCompanySchedule(['company' => $company['id'], 'name' => 'Schedule 2']);
        $schedule = $schedules[array_key_first($schedules)];

        $now = new DateTime();
        $daysCount = 5;
        $bookingDay = ((int)$now->format('N')) + $daysCount;
        if ($bookingDay !== 1 && $bookingDay !== 7) {
            $daysCount += (7 - $bookingDay);
        }
        $start = (new DateTime())->modify($daysCount.' day')->setTime(11, 0, 0);
        $end = (new DateTime())->modify($daysCount.' day')->setTime(11, 30, 0);

        $response = $this->sendPostRequest(
            '/api/bookings/',
            [],
            [
                'schedule' => $schedule['id'],
                'start' => $start->getTimestamp(),
                'end' => $end->getTimestamp(),
                'customer_comment' => 'some customer comment',
                'user_name' => 'Test customer name',
                'user_phone' => '223123123123',
            ]
        );
        $this->assertEquals(403, $response->getStatusCode());
        $bookingContent = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('title', $bookingContent);
        $this->assertEquals(
            'This booking is only available for authorized users. Please, sign in.',
            $bookingContent['title']
        );
    }

    public function testCreateBookingAuthorizedSuccess()
    {
        $token = $this->login([
            'username' => 'user1@site.com',
            'password' => '12345678',
        ]);
        $companies = $this->findCompanyByEmail('company1@gmail.com');
        $company = $companies[array_key_first($companies)];
        $schedules = $this->findCompanySchedule(['company' => $company['id'], 'name' => 'Schedule 2']);
        $schedule = $schedules[array_key_first($schedules)];

        $now = new DateTime();
        $daysCount = 5;
        $bookingDay = ((int)$now->format('N')) + $daysCount;
        if ($bookingDay !== 1 && $bookingDay !== 7) {
            $daysCount += (7 - $bookingDay);
        }
        $start = (new DateTime())->modify($daysCount.' day')->setTime(11, 0, 0);
        $end = (new DateTime())->modify($daysCount.' day')->setTime(11, 30, 0);

        $response = $this->sendPostRequest(
            '/api/bookings/',
            [],
            [
                'schedule' => $schedule['id'],
                'start' => $start->getTimestamp(),
                'end' => $end->getTimestamp(),
                'customer_comment' => 'some customer comment',
                'user_name' => 'Test customer name',
                'user_phone' => '223123123123',
            ] +
            [
                'token' => $token
            ]
        );
        $this->assertSuccessResponse($response);
        $bookingContent = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $bookingContent);
        $this->assertArrayHasKey('client', $bookingContent, 'Client wasn\'t created');
        $this->assertNotEmpty($bookingContent['client'], 'Client property is empty');
        $this->assertArrayHasKey('id', $bookingContent['client'], 'Client wasn\'t created properly');
    }
}
