<?php


namespace App\Tests\Functional\Controller;

use DateTime;

class SpecialHoursControllerTest extends AbstractBaseController
{
    protected function getMySchedules($token)
    {
        $response = $this->sendGetRequest('/api/schedule/search/my', ['filter' => []], ['token' => $token]);
        $this->assertSuccessResponse($response);
        $mySchedulesContent = json_decode($response->getContent(), true);
        $this->assertIsArray($mySchedulesContent);
        $firstKey = array_key_first($mySchedulesContent);
        $this->assertArrayHasKey('id', $mySchedulesContent[$firstKey]);

        return $mySchedulesContent;
    }

    protected function getMySpecialHours($token, $scheduleId)
    {
        $response = $this->sendGetRequest('/api/special-hours/search', ['filter' => ['schedule' => $scheduleId]], ['token' => $token]);
        $this->assertSuccessResponse($response);
        $mySpecialHourssContent = json_decode($response->getContent(), true);
        $this->assertIsArray($mySpecialHourssContent);

        return $mySpecialHourssContent;
    }

    public function testUpdateListSuccess()
    {
        $token = $this->login([
            'username' => 'user1@site.com',
            'password' => '12345678',
        ]);
        $mySchedules = $this->getMySchedules($token);
        $schedule = $mySchedules[array_key_first($mySchedules)];
        $mySpecialHours = $this->getMySpecialHours($token, $schedule['id']);
        foreach ($mySpecialHours as &$specialHour) {
            $specialHour['start_date'] = (new DateTime($specialHour['start_date']))->getTimestamp();
            $specialHour['end_date'] = (new DateTime($specialHour['end_date']))->getTimestamp();
            if (!empty($specialHour['repeat_date'])) {
                $specialHour['repeat_date'] = (new DateTime($specialHour['repeat_date']))->getTimestamp();
            }
            $specialHour['schedule'] = $schedule['id'];
        }
        $now = new DateTime();
        $data = [
            'deleted' => false,
            'schedule' => $schedule['id'],
            'ranges' => [['from'=> '10:00', 'to'=> '11:00']],
            'start_date' => $now->modify("-1 week")->getTimestamp(),
            'end_date' => $now->modify("+1 year")->getTimestamp(),
            'repeat_condition' => 1,
            'repeat_date' => $now->getTimestamp(),
            'repeat_day' => 1,
            'available' => true,
        ];
        $mySpecialHours[] = $data;
        $response = $this->sendPutRequest('/api/special-hours/update-list', [], $mySpecialHours + ['token' => $token]);
        $this->assertSuccessResponse($response);
        $mySpecialHourssContent = json_decode($response->getContent(), true);
        $this->assertIsArray($mySpecialHourssContent);
        $this->assertCount(count($mySpecialHours), $mySpecialHourssContent);
    }
}
