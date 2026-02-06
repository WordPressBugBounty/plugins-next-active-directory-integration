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

class HasSomeBinary extends AbstractBinary implements ReturnBoolInterface
{
    public function compile(Compiler $compiler): void
    {
        $compiler
            ->raw('CoreExtension::arraySome($this->env, ')
            ->subcompile($this->getNode('left'))
            ->raw(', ')
            ->subcompile($this->getNode('right'))
            ->raw(')')
        ;
    }

    public function operator(Compiler $compiler): Compiler
    {
        return $compiler->raw('');
    }
}
