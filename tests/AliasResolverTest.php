<?php

namespace Daniser\Tests;

use Daniser\EntityResolver\AliasResolver;
use Daniser\EntityResolver\Contracts\EntityResolver;
use Daniser\EntityResolver\Exceptions\EntityNotFoundException;
use Daniser\EntityResolver\Exceptions\EntityTypeMismatchException;
use PHPUnit\Framework\TestCase;

class AliasResolverTest extends TestCase
{
    protected array $repository;

    protected EntityResolver $decoratedResolver;

    protected function setUp(): void
    {
        $this->repository = [
            'entity' => [new \stdClass, new \stdClass],
            'object' => [new \stdClass],
        ];

        $this->decoratedResolver = $this->createStub(EntityResolver::class);
        $this->decoratedResolver->method('resolve')->willReturnCallback(function ($type, $id) {
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
        $resolver = new AliasResolver($this->decoratedResolver);
        $result = $resolver->resolve('entity', 0);
        $this->assertSame($result, $this->repository['entity'][0]);
    }

    public function testResolvesAlias(): void
    {
        $resolver = new AliasResolver($this->decoratedResolver, ['alias' => 'entity']);
        $result = $resolver->resolve('alias', 0);
        $this->assertSame($result, $this->repository['entity'][0]);
    }

    public function testResolvesByAliasCoveredType(): void
    {
        $resolver = new AliasResolver($this->decoratedResolver, ['alias' => 'entity']);
        $result = $resolver->resolve('entity', 0);
        $this->assertSame($result, $this->repository['entity'][0]);
    }

    public function testResolvesByTypeWhenAliasWithSameNameExist(): void
    {
        $resolver = new AliasResolver($this->decoratedResolver, ['object' => 'entity']);
        $result = $resolver->resolve('object', 0);
        $this->assertSame($result, $this->repository['object'][0]);
    }

    public function testFailsOnNonExistentTypeOrAlias(): void
    {
        $resolver = new AliasResolver($this->decoratedResolver);
        $this->expectException(EntityTypeMismatchException::class);
        $resolver->resolve('dummy', 0);
    }

    public function testFailsOnNonExistentAliasTarget(): void
    {
        $resolver = new AliasResolver($this->decoratedResolver, ['alias' => 'dummy']);
        $this->expectException(EntityTypeMismatchException::class);
        $resolver->resolve('alias', 0);
    }

    public function testFailsOnNonExistentEntityByType(): void
    {
        $resolver = new AliasResolver($this->decoratedResolver);
        $this->expectException(EntityNotFoundException::class);
        $resolver->resolve('entity', 10);
    }

    public function testFailsOnNonExistentEntityByAlias(): void
    {
        $resolver = new AliasResolver($this->decoratedResolver, ['alias' => 'entity']);
        $this->expectException(EntityNotFoundException::class);
        $resolver->resolve('alias', 10);
    }

    /*public function testFailsOnNonExistentEntityByTypeWhenSelfTitledAliasTargetsExistentEntityByGivenAddress(): void
    {
        $resolver = new AliasResolver($this->decoratedResolver, ['object' => 'entity']);
        $this->expectException(EntityNotFoundException::class);
        $resolver->resolve('object', 1);
    }*/
}
