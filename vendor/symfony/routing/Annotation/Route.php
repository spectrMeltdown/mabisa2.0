<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Annotation;



class_exists(\Symfony\Component\Routing\Attribute\Route::class);

if (false) {
    #[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
    class Route extends \Symfony\Component\Routing\Attribute\Route
    {
    }
}
