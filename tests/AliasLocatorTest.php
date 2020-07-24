<?php

declare(strict_types=1);

namespace TTBooking\Tests;

use PHPUnit\Framework\TestCase;
use TTBooking\EntityLocator\AliasLocator;
use TTBooking\EntityLocator\Contracts\EntityLocator;
use TTBooking\EntityLocator\Exceptions\EntityNotFoundException;
use TTBooking\EntityLocator\Exceptions\EntityTypeMismatchException;

class AliasLocatorTest extends TestCase
{
    protected array $repository;

    protected EntityLocator $decoratedLocator;

    protected function setUp(): void
    {
        $this->repository = [
            'entity' => [new \stdClass, new \stdClass],
            'object' => [new \stdClass],
        ];

        $this->decoratedLocator = $this->createStub(EntityLocator::class);
        $this->decoratedLocator->method('locate')->willReturnCallback(function ($type, $id) {
            if (! isset($this->repository[$type])) {
                throw new EntityTypeMismatchException;
            }

            if (! isset($this->repository[$type][$id])) {
                throw new EntityNotFoundException;
            }

            return $this->repository[$type][$id];
        });
    }

    public function testPassesThrough(): void
    {
        $locator = new AliasLocator($this->decoratedLocator);
        $result = $locator->locate('entity', 0);
        $this->assertSame($result, $this->repository['entity'][0]);
    }

    public function testLocatesAlias(): void
    {
        $locator = new AliasLocator($this->decoratedLocator, ['alias' => 'entity']);
        $result = $locator->locate('alias', 0);
        $this->assertSame($result, $this->repository['entity'][0]);
    }

    public function testLocatesByAliasCoveredType(): void
    {
        $locator = new AliasLocator($this->decoratedLocator, ['alias' => 'entity']);
        $result = $locator->locate('entity', 0);
        $this->assertSame($result, $this->repository['entity'][0]);
    }

    public function testLocatesByTypeWhenAliasWithSameNameExist(): void
    {
        $locator = new AliasLocator($this->decoratedLocator, ['object' => 'entity']);
        $result = $locator->locate('object', 0);
        $this->assertSame($result, $this->repository['object'][0]);
    }

    public function testFailsOnNonExistentTypeOrAlias(): void
    {
        $locator = new AliasLocator($this->decoratedLocator);
        $this->expectException(EntityTypeMismatchException::class);
        $locator->locate('dummy', 0);
    }

    public function testFailsOnNonExistentAliasTarget(): void
    {
        $locator = new AliasLocator($this->decoratedLocator, ['alias' => 'dummy']);
        $this->expectException(EntityTypeMismatchException::class);
        $locator->locate('alias', 0);
    }

    public function testFailsOnNonExistentEntityByType(): void
    {
        $locator = new AliasLocator($this->decoratedLocator);
        $this->expectException(EntityNotFoundException::class);
        $locator->locate('entity', 10);
    }

    public function testFailsOnNonExistentEntityByAlias(): void
    {
        $locator = new AliasLocator($this->decoratedLocator, ['alias' => 'entity']);
        $this->expectException(EntityNotFoundException::class);
        $locator->locate('alias', 10);
    }

    /*public function testFailsOnNonExistentEntityByTypeWhenSelfTitledAliasTargetsExistentEntityByGivenAddress(): void
    {
        $locator = new AliasLocator($this->decoratedLocator, ['object' => 'entity']);
        $this->expectException(EntityNotFoundException::class);
        $locator->locate('object', 1);
    }*/
}
