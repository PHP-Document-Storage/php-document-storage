<?php

declare(strict_types=1);


namespace Devnix\DocumentStore\Tests\Cache\Mocks;

use Devnix\DocumentStore\Cache\DocumentInterface;

readonly class Views implements DocumentInterface
{
    public static function identifier(): string
    {
        return 'module.submodule.views';
    }

    public function __construct(public int $value)
    {
    }

    public function increment(): self
    {
        return new self($this->value + 1);
    }
}


// $cache->get(Visits::class, default: new Visits(0));