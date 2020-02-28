<?php

namespace OldSound\RabbitMqBundle\Tests\RabbitMq;

use OldSound\RabbitMqBundle\RabbitMq\RpcClient;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

class RpcClientTest extends TestCase
{
    public function testProcessMessageWithCustomUnserializer()
    {
        /** @var RpcClient $client */
        $client = $this->getMockBuilder(RpcClient::class)
            ->addMethods(['sendReply', 'maybeStopConsumer'])
            ->disableOriginalConstructor()
            ->getMock();
        /** @var AMQPMessage $message */
        $message = $this->getMockBuilder(AMQPMessage::class)
            ->onlyMethods(['get'])
            ->setConstructorArgs(['message'])
            ->getMock();
        /** @var MockObject|SerializerInterface $serializer */
        $serializer = $this->getMockBuilder(SerializerInterface::class)
            ->onlyMethods(['serialize', 'deserialize'])
            ->getMock();
        $serializer->expects($this->once())->method('deserialize')->with('message', 'json', null);
        $client->initClient(true);
        $client->setUnserializer(function($data) use ($serializer) {
            $serializer->deserialize($data, 'json','');
        });
        $client->processMessage($message);
    }

    public function testProcessMessageWithNotifyMethod()
    {
        /** @var RpcClient $client */
        $client = $this->getMockBuilder(RpcClient::class)
            ->addMethods(['sendReply', 'maybeStopConsumer'])
            ->disableOriginalConstructor()
            ->getMock();
        $expectedNotify = 'message';
        /** @var AMQPMessage $message */
        $message = $this->getMockBuilder(AMQPMessage::class)
            ->onlyMethods(['get'])
            ->setConstructorArgs([$expectedNotify])
            ->getMock();
        $notified = false;
        $client->notify(function ($message) use (&$notified) {
            $notified = $message;
        });

        $client->initClient(false);
        $client->processMessage($message);

        $this->assertSame($expectedNotify, $notified);
    }

    public function testInvalidParameterOnNotify()
    {
        /** @var RpcClient $client */
        $client = $this->getMockBuilder(RpcClient::class)
            ->addMethods(['sendReply', 'maybeStopConsumer'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->expectException(\InvalidArgumentException::class);
        $client->notify('not a callable');
    }
}
