<?php

declare(strict_types=1);

namespace Devnix\DocumentStore\Tests\Cache\Adapter;

use Devnix\DocumentStore\Cache\Adapter\InMemoryAdapter;
use Devnix\DocumentStore\Cache\Cache;
use Devnix\DocumentStore\Cache\DocumentInterface;
use Devnix\DocumentStore\Cache\Exception\DocumentNotFoundException;
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
        $views = $this->cache->tryGet(Views::class, default: new Views(69420));

        self::assertSame(69420, $views->value);
    }

    public function testItRetrievesANullValueWithoutPrefix(): void
    {
        $views = $this->cache->tryGet(Views::class);
        self::assertNull($views);
    }

    public function testReadAndWriteWithAndWithoutPrefix(): void
    {
        $views = $this->cache->tryGet(Views::class);
        self::assertNull($views);

        $this->cache->set(new Views(69420));
        self::assertEquals(new Views(69420), $this->cache->tryGet(Views::class));

        $this->cache->set(new Views(123), '1');
        self::assertEquals(new Views(123), $this->cache->tryGet(Views::class, '1'));

        $this->cache->set(new Views(500), '2');
        self::assertEquals(new Views(500), $this->cache->tryGet(Views::class, '2'));

        $views = $this->cache->tryGet(Views::class);
        self::assertNotNull($views);
        $this->cache->set($views->increment());
        self::assertEquals(new Views(69421), $this->cache->tryGet(Views::class));

        $views1 = $this->cache->tryGet(Views::class, '1');
        self::assertNotNull($views1);
        $this->cache->set($views1->increment(), '1');
        self::assertEquals(new Views(124), $this->cache->tryGet(Views::class, '1'));

        $views2 = $this->cache->tryGet(Views::class, '2');
        self::assertNotNull($views2);
        $this->cache->set($views2->increment(), '2');
        self::assertEquals(new Views(501), $this->cache->tryGet(Views::class, '2'));
    }

    /**
     * @return iterable<array{
     *     class-string<DocumentInterface>,
     *     non-empty-string|null,
     *     string,
     * }>
     */
    public static function providerGetKeyThatDoesNotExist(): iterable
    {
        yield [
            Views::class,
            null,
            'Could not find a document with key "module.submodule.views" for class "Devnix\DocumentStore\Tests\Cache\Mocks\Views"',
        ];
        yield [
            Views::class,
            '1',
            'Could not find a document with key "module.submodule.views.1" for class "Devnix\DocumentStore\Tests\Cache\Mocks\Views"',
        ];
        yield [
            Views::class,
            '2',
            'Could not find a document with key "module.submodule.views.2" for class "Devnix\DocumentStore\Tests\Cache\Mocks\Views"',
        ];
        yield [
            Views::class,
            'CARTAHENA',
            'Could not find a document with key "module.submodule.views.CARTAHENA" for class "Devnix\DocumentStore\Tests\Cache\Mocks\Views"',
        ];
    }

    /**
     * @param class-string<DocumentInterface> $documentClass
     * @param non-empty-string|null $prefix
     * @throws DocumentNotFoundException
     */
    #[Framework\Attributes\DataProvider('providerGetKeyThatDoesNotExist')]
    public function testGetKeyThatDoesNotExist(string $documentClass, ?string $prefix, string $expectedMessage): void
    {
        self::expectException(DocumentNotFoundException::class);
        self::expectExceptionMessage($expectedMessage);

        $this->cache->get($documentClass, $prefix);
    }

    /**
     * @throws DocumentNotFoundException
     */
    public function testExistingKey(): void
    {
        $this->cache->set(new Views(9000));
        $this->cache->set(new Views(50), '1');

        self::assertEquals(new Views(9000), $this->cache->get(Views::class));
        self::assertEquals(new Views(50), $this->cache->get(Views::class, '1'));
    }

    public function testExists(): void
    {
        self::assertFalse($this->cache->exists(Views::class));
        self::assertFalse($this->cache->exists(Views::class), '1');

        $this->cache->set(new Views(123));
        self::assertTrue($this->cache->exists(Views::class));
        self::assertFalse($this->cache->exists(Views::class, '1'));

        $this->cache->set(new Views(456), '420');
        self::assertTrue($this->cache->exists(Views::class));
        self::assertFalse($this->cache->exists(Views::class, '1'));
    }

    public function testTryUpdateNonExisingDocument(): void
    {
        $views = $this->cache->tryUpdate(
            Views::class,
            fn (Views $views) => throw new \RuntimeException('This method should not be called')
        );

        self::assertNull($views);

        $views1 = $this->cache->tryUpdate(
            Views::class,
            fn (Views $views) => throw new \RuntimeException('This method should not be called'),
            '1'
        );

        self::assertNull($views1);
    }

    public function testTryUpdateExistingDocument(): void
    {
        $this->cache->set(new Views(69420));

        $views = $this->cache->tryUpdate(
            Views::class,
            fn (Views $views) => $views->increment(),
        );

        self::assertEquals(new Views(69421), $views);

        $views = $this->cache->tryUpdate(
            Views::class,
            fn (Views $views) => $views->increment(),
            default: new Views(0)
        );

        self::assertEquals(new Views(69422), $views);

        $this->cache->set(new Views(42), '1');

        $views1 = $this->cache->tryUpdate(
            Views::class,
            fn (Views $views) => $views->increment(),
            '1',
        );

        self::assertEquals(new Views(43), $views1);

        $views1 = $this->cache->tryUpdate(
            Views::class,
            fn (Views $views) => $views->increment(),
            '1',
            new Views(0),
        );

        self::assertEquals(new Views(44), $views1);
    }

    /**
     * @throws DocumentNotFoundException
     */
    public function testUpdateNonExisingDocument(): void
    {
        self::expectException(DocumentNotFoundException::class);
        self::expectExceptionMessage('Could not find a document with key "module.submodule.views" for class "Devnix\DocumentStore\Tests\Cache\Mocks\Views"');

        $this->cache->update(
            Views::class,
            fn (Views $views) =>  $views->increment(),
        );
    }

    /**
     * @throws DocumentNotFoundException
     */
    public function testUpdateNonExisingDocumentWithDefaultValue(): void
    {
        $views = $this->cache->update(
            Views::class,
            fn (Views $views) =>  $views->increment(),
            null,
            new Views(26),
        );

        self::assertEquals(new Views(27), $views);
    }

    /**
     * @throws DocumentNotFoundException
     */
    public function testUpdateExistingDocument(): void
    {
        $this->cache->set(new Views(69420));

        $views = $this->cache->update(
            Views::class,
            fn (Views $views) => $views->increment(),
            null,
        );

        self::assertEquals(new Views(69421), $views);

        $views = $this->cache->update(
            Views::class,
            fn (Views $views) => $views->increment(),
            default: new Views(0)
        );

        self::assertEquals(new Views(69422), $views);

        $this->cache->set(new Views(42), '1');

        $views1 = $this->cache->update(
            Views::class,
            fn (Views $views) => $views->increment(),
            '1',
        );

        self::assertEquals(new Views(43), $views1);

        $views1 = $this->cache->update(
            Views::class,
            fn (Views $views) => $views->increment(),
            '1',
            new Views(0),
        );

        self::assertEquals(new Views(44), $views1);
    }
}
