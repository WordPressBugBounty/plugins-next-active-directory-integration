<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by __root__ on 31-January-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Dreitier\Nadi\Vendor\Twig\Node\Expression;

use Dreitier\Nadi\Vendor\Twig\Compiler;

class MethodCallExpression extends AbstractExpression
{
    public function __construct(AbstractExpression $node, string $method, ArrayExpression $arguments, int $lineno)
    {
        trigger_deprecation('twig/twig', '3.15', 'The "%s" class is deprecated, use "%s" instead.', __CLASS__, MacroReferenceExpression::class);

        parent::__construct(['node' => $node, 'arguments' => $arguments], ['method' => $method, 'safe' => false, 'is_defined_test' => false], $lineno);

        if ($node instanceof NameExpression) {
            $node->setAttribute('always_defined', true);
        }
    }

    public function compile(Compiler $compiler): void
    {
        if ($this->getAttribute('is_defined_test')) {
            $compiler
                ->raw('method_exists($macros[')
                ->repr($this->getNode('node')->getAttribute('name'))
                ->raw('], ')
                ->repr($this->getAttribute('method'))
                ->raw(')')
            ;

            return;
        }

        $compiler
            ->raw('CoreExtension::callMacro($macros[')
            ->repr($this->getNode('node')->getAttribute('name'))
            ->raw('], ')
            ->repr($this->getAttribute('method'))
            ->raw(', ')
            ->subcompile($this->getNode('arguments'))
            ->raw(', ')
            ->repr($this->getTemplateLine())
            ->raw(', $context, $this->getSourceContext())');
    }
}
