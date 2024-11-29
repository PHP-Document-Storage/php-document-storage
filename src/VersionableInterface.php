<?php

declare(strict_types=1);

namespace Devnix\PhpDocumentStore;

interface VersionableInterface
{
    /**
     * @return positive-int
     */
    public static function version(): int;
}