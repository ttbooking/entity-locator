<?php

namespace Daniser\Tests;

use Daniser\EntityResolver\Exceptions\EntityNotFoundException;
use Daniser\EntityResolver\ValidatingResolver;
use PHPUnit\Framework\TestCase;

class ValidatingResolverTest extends TestCase
{
    public function testReturnsSameEntity()
    {
        $resolver = new ValidatingResolver;
        $mock = $this->getMockBuilder('TestClass')->getMock();
        $result = $resolver->resolve(get_class($mock), $mock);
        $this->assertSame($result, $mock);
    }

    public function testFailsOnWrongInstance()
    {
        $resolver = new ValidatingResolver;
        $this->expectException(EntityNotFoundException::class);
        $class = $this->getMockClass('TestClass');
        $mock = $this->getMockBuilder('WrongClass')->getMock();
        $resolver->resolve($class, $mock);
    }
}
