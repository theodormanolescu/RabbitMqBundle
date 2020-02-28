<?php

namespace OldSound\RabbitMqBundle\Tests\Event;

use OldSound\RabbitMqBundle\Event\BeforeProcessingMessageEvent;
use OldSound\RabbitMqBundle\RabbitMq\Consumer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

/**
 * Class BeforeProcessingMessageEventTest
 *
 * @package OldSound\RabbitMqBundle\Tests\Event
 */
class BeforeProcessingMessageEventTest extends TestCase
{
    /**
     * @return Consumer
     */
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

    public function testEvent()
    {
        $AMQPMessage = new AMQPMessage('body');
        $consumer = $this->getConsumer();
        $event = new BeforeProcessingMessageEvent($consumer, $AMQPMessage);
        $this->assertSame($AMQPMessage, $event->getAMQPMessage());
        $this->assertSame($consumer, $event->getConsumer());
    }
}
