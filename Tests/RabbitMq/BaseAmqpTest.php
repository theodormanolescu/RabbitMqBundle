<?php

namespace OldSound\RabbitMqBundle\Tests\RabbitMq;

use PhpAmqpLib\Connection\AbstractConnection;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as ContractsEventDispatcherInterface;
use OldSound\RabbitMqBundle\Event\AMQPEvent;
use OldSound\RabbitMqBundle\RabbitMq\BaseAmqp;
use OldSound\RabbitMqBundle\RabbitMq\Consumer;
use PHPUnit\Framework\TestCase;

class BaseAmqpTest extends TestCase
{
    /** @var MockObject|AbstractConnection  */
    private $connection;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->connection = $this->getMockBuilder(AbstractConnection::class)
            ->disableOriginalConstructor()
            ->getMock();
        parent::setUp();
    }

    public function testLazyConnection()
    {
        $this->connection
            ->expects($this->any())
            ->method('connectOnConstruct')
            ->willReturn(false);
        $this->connection
            ->expects($this->never())
            ->method('channel');

        new Consumer($this->connection, null);
    }

    public function testNotLazyConnection()
    {
        $this->connection
            ->expects($this->any())
            ->method('connectOnConstruct')
            ->willReturn(true);
        $this->connection
            ->expects($this->once())
            ->method('channel');

        new Consumer($this->connection, null);
    }

    public function testDispatchEvent()
    {
        /** @var BaseAmqp|MockObject $baseAmqpConsumer */
        $baseAmqpConsumer = $this->getMockBuilder(BaseAmqp::class)
            ->disableOriginalConstructor()
            ->getMock();
        if (is_subclass_of('AMQPEvent', 'ContractsBaseEvent')) {
            $eventDispatcher = $this->getMockBuilder(ContractsEventDispatcherInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        } else {
            $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        }
        $baseAmqpConsumer->expects($this->atLeastOnce())
            ->method('getEventDispatcher')
            ->willReturn($eventDispatcher);
        if ($eventDispatcher instanceof ContractsEventDispatcherInterface) {
            $eventDispatcher->expects($this->once())
                ->method('dispatch')
                ->with(new AMQPEvent(), AMQPEvent::ON_CONSUME)
                ->willReturn(new AMQPEvent());
        } else {
            $eventDispatcher->expects($this->once())
                ->method('dispatch')
                ->with(AMQPEvent::ON_CONSUME, new AMQPEvent())
                ->willReturn(true);
        }
        $this->invokeMethod('dispatchEvent', $baseAmqpConsumer, array(AMQPEvent::ON_CONSUME, new AMQPEvent()));
    }

    /**
     * @param $name
     * @param $obj
     * @param $params
     *
     * @return mixed
     * @throws \ReflectionException
     */
    protected function invokeMethod($name, $obj, $params)
    {
        $class = new \ReflectionClass(get_class($obj));
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $params);
    }
}
