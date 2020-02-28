<?php

namespace OldSound\RabbitMqBundle\Tests\Event;

use OldSound\RabbitMqBundle\MemoryChecker\MemoryConsumptionChecker;
use OldSound\RabbitMqBundle\MemoryChecker\NativeMemoryUsageProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class MemoryManagerTest
 *
 * @package OldSound\RabbitMqBundle\Tests\Manager
 */
class MemoryConsumptionCheckerTest extends TestCase
{
    private $maxConsumptionAllowed = '10M';
    private $allowedConsumptionUntil = '2M';

    public function testMemoryIsNotAlmostOverloaded()
    {
        $currentMemoryUsage = '7M';

        $memoryManager = new MemoryConsumptionChecker($this->getMemoryUsage($currentMemoryUsage));
        $this->assertFalse(
            $memoryManager->isRamAlmostOverloaded($this->maxConsumptionAllowed, $this->allowedConsumptionUntil)
        );
    }

    public function testMemoryIsAlmostOverloaded()
    {
        $currentMemoryUsage = '9M';

        $memoryManager = new MemoryConsumptionChecker($this->getMemoryUsage($currentMemoryUsage));

        $this->assertTrue(
            $memoryManager->isRamAlmostOverloaded($this->maxConsumptionAllowed, $this->allowedConsumptionUntil)
        );
    }

    public function testMemoryExactValueIsNotAlmostOverloaded()
    {
        $currentMemoryUsage = '7M';

        $memoryManager = new MemoryConsumptionChecker($this->getMemoryUsage($currentMemoryUsage));

        $this->assertFalse($memoryManager->isRamAlmostOverloaded($this->maxConsumptionAllowed));
    }

    public function testMemoryExactValueIsAlmostOverloaded()
    {
        $currentMemoryUsage = '11M';

        $memoryManager = new MemoryConsumptionChecker($this->getMemoryUsage($currentMemoryUsage));

        $this->assertTrue($memoryManager->isRamAlmostOverloaded($this->maxConsumptionAllowed));
    }

    /**
     * @param string $currentMemoryUsage
     *
     * @return NativeMemoryUsageProvider|MockObject
     */
    private function getMemoryUsage(string $currentMemoryUsage)
    {
        /** @var NativeMemoryUsageProvider|MockObject $memoryUsageProvider */
        $memoryUsageProvider = $this->getMockBuilder(NativeMemoryUsageProvider::class)->getMock();
        $memoryUsageProvider->expects($this->any())->method('getMemoryUsage')->willReturn($currentMemoryUsage);

        return $memoryUsageProvider;
    }
}
