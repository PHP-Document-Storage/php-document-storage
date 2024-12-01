<?php

declare(strict_types=1);

use Devnix\DocumentStore\Cache\CacheInterface;
use Devnix\DocumentStore\Tests\Cache\Mocks\Views;

function updateWithoutDefaultValue(CacheInterface $cache): ?Views
{
    return $cache->tryUpdate(Views::class, fn (Views $views) => $views->increment());
}

function updateWithDefaultValue(CacheInterface $cache): Views
{
    return $cache->tryUpdate(Views::class, static fn (Views $views) => $views->increment(), default: new Views(0));
}
