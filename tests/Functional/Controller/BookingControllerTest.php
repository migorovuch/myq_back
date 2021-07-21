<?php

namespace App\Tests\Functional\Controller;

use DateTime;
use Symfony\Component\HttpFoundation\Response;

class BookingControllerTest extends AbstractBaseController
{
    protected function findCompanyByEmail(string $email)
    {
        $this->sendGetRequest('/api/companies/search/app', ['filter' => ['email' => $email]]);

        return $this->assertSuccessEntitiesArray(self::$client->getResponse());
    }

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
        if ($now->format('N') > 5) {
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
}
