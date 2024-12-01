<?php

declare(strict_types=1);

namespace Devnix\DocumentStore;

interface VersionableInterface
{
    /**
     * @return positive-int
     */
    public static function version(): int;
}