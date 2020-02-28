<?php

namespace OldSound\RabbitMqBundle\Tests\Helper;

class ObjectReflection
{
    /**
     * @param        $instance
     * @param string $property
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public static function getValue($instance, string $property)
    {
        $reflectionClass = new \ReflectionClass($instance);
        $property = $reflectionClass->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($instance);
    }
}
