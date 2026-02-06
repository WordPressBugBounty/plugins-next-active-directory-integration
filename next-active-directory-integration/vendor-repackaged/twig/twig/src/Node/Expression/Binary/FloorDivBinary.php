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

namespace Dreitier\Nadi\Vendor\Twig\Node\Expression\Binary;

use Dreitier\Nadi\Vendor\Twig\Compiler;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\ReturnNumberInterface;

class FloorDivBinary extends AbstractBinary implements ReturnNumberInterface
{
    public function compile(Compiler $compiler): void
    {
        $compiler->raw('(int) floor(');
        parent::compile($compiler);
        $compiler->raw(')');
    }

    public function operator(Compiler $compiler): Compiler
    {
        return $compiler->raw('/');
    }
}
