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

namespace Dreitier\Nadi\Vendor\Twig\Node;

use Dreitier\Nadi\Vendor\Twig\Attribute\YieldReady;
use Dreitier\Nadi\Vendor\Twig\Compiler;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\AbstractExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\ConstantExpression;

/**
 * Represents a deprecated node.
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
#[YieldReady]
class DeprecatedNode extends Node
{
    public function __construct(AbstractExpression $expr, int $lineno)
    {
        parent::__construct(['expr' => $expr], [], $lineno);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler->addDebugInfo($this);

        $expr = $this->getNode('expr');

        if (!$expr instanceof ConstantExpression) {
            $varName = $compiler->getVarName();
            $compiler
                ->write(\sprintf('$%s = ', $varName))
                ->subcompile($expr)
                ->raw(";\n")
            ;
        }

        $compiler->write('trigger_deprecation(');
        if ($this->hasNode('package')) {
            $compiler->subcompile($this->getNode('package'));
        } else {
            $compiler->raw("''");
        }
        $compiler->raw(', ');
        if ($this->hasNode('version')) {
            $compiler->subcompile($this->getNode('version'));
        } else {
            $compiler->raw("''");
        }
        $compiler->raw(', ');

        if ($expr instanceof ConstantExpression) {
            $compiler->subcompile($expr);
        } else {
            $compiler->write(\sprintf('$%s', $varName));
        }

        $compiler
            ->raw('.')
            ->string(\sprintf(' in "%s" at line %d.', $this->getTemplateName(), $this->getTemplateLine()))
            ->raw(");\n")
        ;
    }
}
