<?php

namespace OldSound\RabbitMqBundle\Tests\RabbitMq;

use OldSound\RabbitMqBundle\RabbitMq\Binding;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPConnection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BindingTest extends TestCase
{

    protected function getBinding($amqpConnection, $amqpChannel)
    {
        return new Binding($amqpConnection, $amqpChannel);
    }

    /**
     * @return MockObject|AMQPConnection
     */
    protected function prepareAMQPConnection()
    {
        return $this->getMockBuilder(AMQPConnection::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function prepareAMQPChannel($channelId = null)
    {
        $channelMock = $this->getMockBuilder(AMQPChannel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $channelMock->expects($this->any())
            ->method('getChannelId')
            ->willReturn($channelId);
        return $channelMock;
    }

    public function testQueueBind()
    {
        $ch = $this->prepareAMQPChannel('channel_id');
        $con = $this->prepareAMQPConnection();

        $source = 'example_source';
        $destination = 'example_destination';
        $key = 'example_key';
        $ch->expects($this->once())
            ->method('queue_bind')
            ->will($this->returnCallback(function ($d, $s, $k, $n, $a) use ($destination, $source, $key) {
                $this->assertEquals($destination, $d);
                $this->assertEquals($source, $s);
                $this->assertEquals($key, $k);
                $this->assertFalse($n);
                $this->assertNull($a);
            }));

        $binding = $this->getBinding($con, $ch);
        $binding->setExchange($source);
        $binding->setDestination($destination);
        $binding->setRoutingKey($key);
        $binding->setupFabric();
    }

    public function testExhangeBind()
    {
        $ch = $this->prepareAMQPChannel('channel_id');
        $con = $this->prepareAMQPConnection();

        $source = 'example_source';
        $destination = 'example_destination';
        $key = 'example_key';
        $ch->expects($this->once())
            ->method('exchange_bind')
            ->will($this->returnCallback(function ($d, $s, $k, $n, $a) use ($destination, $source, $key) {
                $this->assertEquals($destination, $d);
                $this->assertEquals($source, $s);
                $this->assertEquals($key, $k);
                $this->assertFalse($n);
                $this->assertNull($a);
            }));

        $binding = $this->getBinding($con, $ch);
        $binding->setExchange($source);
        $binding->setDestination($destination);
        $binding->setRoutingKey($key);
        $binding->setDestinationIsExchange(true);
        $binding->setupFabric();
    }
}
