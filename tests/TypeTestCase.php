<?php

declare(strict_types=1);

/**
 * Proprietary licence.
 */

namespace App\Tests;

class TypeTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @param $classObj
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public function getResultFromMethod($classObj, $method, $params = [])
    {
        $reflectionClass = new \ReflectionClass(get_class($classObj));
        $method = $reflectionClass->getMethod($method);
        $method->setAccessible(true);
        return $method->invokeArgs($classObj, $params);
    }
    /**
     * @param $classObj
     * @param $property
     *
     * @return mixed
     */
    public function getProperty($classObj, $property)
    {
        $reflectionClass = new \ReflectionClass(get_class($classObj));
        $property = $reflectionClass->getProperty($property);
        $property->setAccessible(true);
        return $property->getValue($classObj);
    }
    /**
     * @param $classObj
     * @param $property
     * @param $value
     */
    public function setProperty($classObj, $property, $value)
    {
        $reflectionClass = new \ReflectionClass(get_class($classObj));
        $property = $reflectionClass->getProperty($property);
        $property->setAccessible(true);
        return $property->setValue($classObj, $value);
    }
}
