<?php

namespace OldSound\RabbitMqBundle\Tests\Event;

use OldSound\RabbitMqBundle\Event\OnIdleEvent;
use OldSound\RabbitMqBundle\RabbitMq\Consumer;
use PHPUnit\Framework\TestCase;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPConnection;

/**
 * Class OnIdleEventTest
 *
 * @package OldSound\RabbitMqBundle\Tests\Event
 */
class OnIdleEventTest extends TestCase
{
    protected function getConsumer(): Consumer
    {
        return new Consumer(
            $this->getMockBuilder(AMQPConnection::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this->getMockBuilder(AMQPChannel::class)
                ->disableOriginalConstructor()
                ->getMock()
        );
    }

    public function testShouldAllowGetConsumerSetInConstructor()
    {
        $consumer = $this->getConsumer();
        $event = new OnIdleEvent($consumer);

        $this->assertSame($consumer, $event->getConsumer());
    }

    public function testShouldSetForceStopToTrueInConstructor()
    {
        $consumer = $this->getConsumer();
        $event = new OnIdleEvent($consumer);

        $this->assertTrue($event->isForceStop());
    }

    public function testShouldReturnPreviouslySetForceStop()
    {
        $consumer = $this->getConsumer();
        $event = new OnIdleEvent($consumer);

        //guard
        $this->assertTrue($event->isForceStop());

        $event->setForceStop(false);
        $this->assertFalse($event->isForceStop());
    }
}
