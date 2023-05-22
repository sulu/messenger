<?php

declare(strict_types=1);

namespace Sulu\Messenger\Tests\Traits;

use ReflectionException;

trait PrivatePropertyTrait
{
    /**
     * @return mixed
     */
    protected static function getPrivateProperty(object $object, string $propertyName)
    {
        $reflection = new \ReflectionClass($object);
        $propertyReflection = $reflection->getProperty($propertyName);
        $propertyReflection->setAccessible(true);

        return $propertyReflection->getValue($object);
    }

    protected static function setPrivateProperty(object $object, string $propertyName, mixed $value): void
    {
        $reflection = new \ReflectionClass($object);
        try {
            $propertyReflection = $reflection->getProperty($propertyName);
            self::setValue($propertyReflection, $object, $value);
        } catch (ReflectionException) {
            $parent = $reflection->getParentClass();
            if ($parent) {
                $propertyReflection = $parent->getProperty($propertyName);
                self::setValue($propertyReflection, $object, $value);
            }
        }
    }

    private static function setValue(\ReflectionProperty $propertyReflection, object $object, mixed $value): void
    {
        $propertyReflection->setAccessible(true);

        $propertyReflection->setValue($object, $value);
    }
}
