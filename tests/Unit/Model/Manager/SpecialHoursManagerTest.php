<?php

namespace App\Test\Unit\Model\Manager;

use App\Model\Manager\SpecialHoursManager;
use App\Repository\SpecialHoursRepository;
use App\Util\DTOExporter\DTOExporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

class SpecialHoursManagerTest extends TestCase
{
    protected SpecialHoursManager $specialHoursManager;

    public function setUp(): void
    {
        $this->specialHoursManager = new SpecialHoursManager(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(SpecialHoursRepository::class),
            $this->createMock(Security::class),
            $this->createMock(DTOExporterInterface::class),
            $this->createMock(SerializerInterface::class)
        );
    }

    public function mergeDataProvider()
    {
        return [
            [
                [
                    ['from' => '09:00', 'to' => '12:00'],
                    ['from' => '15:00', 'to' => '16:00'],
                    ['from' => '11:00', 'to' => '12:00'],
                    ['from' => '16:00', 'to' => '18:00']
                ],
                [['from' => '11:00', 'to' => '13:00']],
                [['from' => '09:00', 'to' => '13:00'], ['from' => '15:00', 'to' => '18:00'],]
            ],
            [
                [
                    ['from' => '09:00', 'to' => '19:00'],
                    ['from' => '15:00', 'to' => '16:00'],
                    ['from' => '11:00', 'to' => '12:00'],
                    ['from' => '16:00', 'to' => '18:00']
                ],
                [['from' => '11:00', 'to' => '13:00']],
                [['from' => '09:00', 'to' => '19:00']]
            ],
            [
                [['from' => '12:00', 'to' => '13:00'], ['from' => '09:00', 'to' => '16:00'],],
                [['from' => '11:00', 'to' => '13:00']],
                [['from' => '09:00', 'to' => '16:00']]
            ],
            [
                [['from' => '09:00', 'to' => '13:00'], ['from' => '13:00', 'to' => '16:00'],],
                [['from' => '11:00', 'to' => '14:00']],
                [['from' => '09:00', 'to' => '16:00']]
            ],
            [
                [['from' => '09:00', 'to' => '13:00'], ['from' => '13:00', 'to' => '16:00'],],
                [['from' => '11:00', 'to' => '17:00']],
                [['from' => '09:00', 'to' => '17:00']]
            ],
        ];
    }

    /**
     * @dataProvider mergeDataProvider
     */
    public function testMergeRanges($arrayRanges1, $arrayRanges2, $expectedResult)
    {
        $result = $this->specialHoursManager->addRanges($arrayRanges1, $arrayRanges2);

        $this->assertEquals($result, $expectedResult);
    }
}
