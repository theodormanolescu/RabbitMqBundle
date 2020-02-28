<?php

namespace OldSound\RabbitMqBundle\Tests\RabbitMq;

use OldSound\RabbitMqBundle\RabbitMq\BaseAmqp;
use OldSound\RabbitMqBundle\RabbitMq\BaseConsumer;
use OldSound\RabbitMqBundle\RabbitMq\DequeuerInterface;
use OldSound\RabbitMqBundle\Tests\Helper\ObjectReflection;
use PhpAmqpLib\Connection\AMQPConnection;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class BaseConsumerTest extends TestCase
{
    /** @var BaseConsumer */
    protected $consumer;

    protected function setUp(): void
    {
        $amqpConnection = $this->getMockBuilder(AMQPConnection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->consumer = $this->getMockBuilder(BaseConsumer::class)
            ->setConstructorArgs(array($amqpConnection))
            ->getMockForAbstractClass();
    }

    public function testItExtendsBaseAmqpInterface()
    {
        $this->assertInstanceOf(BaseAmqp::class, $this->consumer);
    }

    public function testItImplementsDequeuerInterface()
    {
        $this->assertInstanceOf(DequeuerInterface::class, $this->consumer);
    }

    public function testItsIdleTimeoutIsMutable()
    {
        $this->assertEquals(0, $this->consumer->getIdleTimeout());
        $this->consumer->setIdleTimeout(42);
        $this->assertEquals(42, $this->consumer->getIdleTimeout());
    }

    public function testItsIdleTimeoutExitCodeIsMutable()
    {
        $this->assertEquals(0, $this->consumer->getIdleTimeoutExitCode());
        $this->consumer->setIdleTimeoutExitCode(43);
        $this->assertEquals(43, $this->consumer->getIdleTimeoutExitCode());
    }

    /**
     * @throws ReflectionException
     */
    public function testForceStopConsumer()
    {
        $this->assertFalse(ObjectReflection::getValue($this->consumer, 'forceStop'));
        $this->consumer->forceStopConsumer();
        $this->assertTrue(ObjectReflection::getValue($this->consumer, 'forceStop'));
    }
}
