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
use Dreitier\Nadi\Vendor\Twig\Node\Expression\ReturnBoolInterface;

class EndsWithBinary extends AbstractBinary implements ReturnBoolInterface
{
    public function compile(Compiler $compiler): void
    {
        $left = $compiler->getVarName();
        $right = $compiler->getVarName();
        $compiler
            ->raw(\sprintf('(is_string($%s = ', $left))
            ->subcompile($this->getNode('left'))
            ->raw(\sprintf(') && is_string($%s = ', $right))
            ->subcompile($this->getNode('right'))
            ->raw(\sprintf(') && str_ends_with($%1$s, $%2$s))', $left, $right))
        ;
    }

    public function operator(Compiler $compiler): Compiler
    {
        return $compiler->raw('');
    }
}
