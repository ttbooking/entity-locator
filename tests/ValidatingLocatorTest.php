<?php

namespace TTBooking\Tests;

use TTBooking\EntityLocator\Exceptions\EntityNotFoundException;
use TTBooking\EntityLocator\ValidatingLocator;
use PHPUnit\Framework\TestCase;

class ValidatingLocatorTest extends TestCase
{
    public function testReturnsSameEntity()
    {
        $locator = new ValidatingLocator;
        $mock = $this->getMockBuilder('TestClass')->getMock();
        $result = $locator->locate(get_class($mock), $mock);
        $this->assertSame($result, $mock);
    }

    public function testFailsOnWrongInstance()
    {
        $locator = new ValidatingLocator;
        $this->expectException(EntityNotFoundException::class);
        $class = $this->getMockClass('TestClass');
        $mock = $this->getMockBuilder('WrongClass')->getMock();
        $locator->locate($class, $mock);
    }
}
