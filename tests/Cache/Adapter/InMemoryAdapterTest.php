<?php

declare(strict_types=1);

namespace Cache\Adapter;

use Devnix\PhpDocumentStore\Cache\Adapter\InMemoryAdapter;
use Devnix\PhpDocumentStore\Cache\Cache;
use Devnix\PhpDocumentStore\Tests\Cache\Mocks\Views;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(InMemoryAdapter::class)]
#[Framework\Attributes\CoversClass(Cache::class)]
#[Framework\Attributes\CoversFunction('Devnix\PhpDocumentStore\Internal\suffix')]
final class InMemoryAdapterTest extends Framework\TestCase
{
    private Cache $cache;

    protected function setUp(): void
    {
        $this->cache = new Cache(new InMemoryAdapter());
    }

    public function test_it_retrieves_a_default_value_without_prefix(): void
    {
        $views = $this->cache->get(Views::class, default: new Views(69420));

        self::assertSame(69420, $views->value);
    }

    public function test_it_retrieves_a_null_value_without_prefix(): void
    {
        $views = $this->cache->get(Views::class);

        self::assertNull($views->value);
    }

    public function test_read_and_write_with_and_without_prefix(): void
    {
        $views = $this->cache->get(Views::class);
        self::assertNull($views->value);

        $this->cache->set(new Views(69420));
        self::assertSame(69420, $this->cache->get(Views::class)->value);

        $this->cache->set(new Views(123), '1');
        self::assertSame(123, $this->cache->get(Views::class, '1')->value);

        $this->cache->set(new Views(500), '2');
        self::assertSame(500, $this->cache->get(Views::class, '2')->value);

        $this->cache->set($this->cache->get(Views::class)->increment());
        self::assertSame(69421, $this->cache->get(Views::class)->value);

        $this->cache->set($this->cache->get(Views::class, '1')->increment(), '1');
        self::assertSame(124, $this->cache->get(Views::class, '1')->value);

        $this->cache->set($this->cache->get(Views::class, '2')->increment(), '2');
        self::assertSame(501, $this->cache->get(Views::class, '2')->value);
    }

    // $this->cache->update(Views::class, '1', new Views(0), fn (Views $views) => $views->increment());
}