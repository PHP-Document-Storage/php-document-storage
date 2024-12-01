<?php

declare(strict_types=1);

namespace Devnix\DocumentStore\Internal;

/**
 * @param non-empty-string|null $suffix
 *
 * @return non-empty-string|null
 */
function suffix(?string $suffix): ?string
{
    if (null !== $suffix) {
        return '.'.$suffix;
    }

    return null;
}
