<?php

declare(strict_types=1);

namespace Cache\Adapter;

use Devnix\DocumentStore\Cache\Adapter\InMemoryAdapter;
use Devnix\DocumentStore\Cache\Cache;
use Devnix\DocumentStore\Tests\Cache\Mocks\Views;
use PHPUnit\Framework;

#[Framework\Attributes\CoversClass(InMemoryAdapter::class)]
#[Framework\Attributes\CoversClass(Cache::class)]
#[Framework\Attributes\CoversFunction('Devnix\DocumentStore\Internal\suffix')]
final class InMemoryAdapterTest extends Framework\TestCase
{
    private Cache $cache;

    protected function setUp(): void
    {
        $this->cache = new Cache(new InMemoryAdapter());
    }

    public function testItRetrievesADefaultValueWithoutPrefix(): void
    {
        $views = $this->cache->get(Views::class, default: new Views(69420));

        self::assertSame(69420, $views->value);
    }

    public function testItRetrievesANullValueWithoutPrefix(): void
    {
        $views = $this->cache->get(Views::class);

        self::assertNull($views->value);
    }

    public function testReadAndWriteWithAndWithoutPrefix(): void
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
