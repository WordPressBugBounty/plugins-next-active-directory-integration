<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by __root__ on 28-November-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Dreitier\Nadi\Vendor\Twig\Node\Expression\Test;

use Dreitier\Nadi\Vendor\Twig\Compiler;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\TestExpression;

/**
 * Checks that an expression is true.
 *
 *  {{ var is true }}
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TrueTest extends TestExpression
{
    public function compile(Compiler $compiler): void
    {
        $compiler
            ->raw('(($tmp = ')
            ->subcompile($this->getNode('node'))
            ->raw(') && $tmp instanceof Markup ? (string) $tmp : $tmp)')
        ;
    }
}
