<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by __root__ on 13-September-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Dreitier\Nadi\Vendor\Twig\Node;

use Dreitier\Nadi\Vendor\Twig\Attribute\YieldReady;
use Dreitier\Nadi\Vendor\Twig\Compiler;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\AbstractExpression;

/**
 * Represents a do node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
#[YieldReady]
class DoNode extends Node
{
    public function __construct(AbstractExpression $expr, int $lineno)
    {
        parent::__construct(['expr' => $expr], [], $lineno);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->write('')
            ->subcompile($this->getNode('expr'))
            ->raw(";\n")
        ;
    }
}