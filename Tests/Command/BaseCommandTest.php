<?php

namespace OldSound\RabbitMqBundle\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputDefinition;

abstract class BaseCommandTest extends TestCase
{
    /** @var MockObject|Application */
    protected $application;
    /** @var MockObject|InputDefinition */
    protected $definition;
    /** @var MockObject|HelperSet */
    protected $helperSet;
    protected $command;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->application = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->definition = $this->getMockBuilder(InputDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->helperSet = $this->getMockBuilder(HelperSet::class)->getMock();
        $this->application->expects($this->any())
            ->method('getDefinition')
            ->will($this->returnValue($this->definition));
        $this->definition->expects($this->any())
            ->method('getArguments')
            ->will($this->returnValue(array()));
    }
}
