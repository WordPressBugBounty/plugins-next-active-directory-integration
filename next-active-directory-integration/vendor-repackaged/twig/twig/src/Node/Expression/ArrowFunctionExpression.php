<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by __root__ on 24-April-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Dreitier\Nadi\Vendor\Twig\Node\Expression;

use Dreitier\Nadi\Vendor\Twig\Compiler;
use Dreitier\Nadi\Vendor\Twig\Node\Node;

/**
 * Represents an arrow function.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ArrowFunctionExpression extends AbstractExpression
{
    public function __construct(AbstractExpression $expr, Node $names, $lineno)
    {
        parent::__construct(['expr' => $expr, 'names' => $names], [], $lineno);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->addDebugInfo($this)
            ->raw('function (')
        ;
        foreach ($this->getNode('names') as $i => $name) {
            if ($i) {
                $compiler->raw(', ');
            }

            $compiler
                ->raw('$__')
                ->raw($name->getAttribute('name'))
                ->raw('__')
            ;
        }
        $compiler
            ->raw(') use ($context, $macros) { ')
        ;
        foreach ($this->getNode('names') as $name) {
            $compiler
                ->raw('$context["')
                ->raw($name->getAttribute('name'))
                ->raw('"] = $__')
                ->raw($name->getAttribute('name'))
                ->raw('__; ')
            ;
        }
        $compiler
            ->raw('return ')
            ->subcompile($this->getNode('expr'))
            ->raw('; }')
        ;
    }
}
