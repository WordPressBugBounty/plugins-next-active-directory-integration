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

namespace Dreitier\Nadi\Vendor\Twig\Node;

use Dreitier\Nadi\Vendor\Twig\Attribute\YieldReady;
use Dreitier\Nadi\Vendor\Twig\Compiler;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\AbstractExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\NameExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\Variable\AssignTemplateVariable;

/**
 * Represents an import node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
#[YieldReady]
class ImportNode extends Node
{
    public function __construct(AbstractExpression $expr, AbstractExpression|AssignTemplateVariable $var, int $lineno)
    {
        if (\func_num_args() > 3) {
            trigger_deprecation('twig/twig', '3.15', \sprintf('Passing more than 3 arguments to "%s()" is deprecated.', __METHOD__));
        }

        if (!$var instanceof AssignTemplateVariable) {
            trigger_deprecation('twig/twig', '3.15', \sprintf('Passing a "%s" instance as the second argument of "%s" is deprecated, pass a "%s" instead.', $var::class, __CLASS__, AssignTemplateVariable::class));

            $var = new AssignTemplateVariable($var->getAttribute('name'), $lineno);
        }

        parent::__construct(['expr' => $expr, 'var' => $var], [], $lineno);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler->subcompile($this->getNode('var'));

        if ($this->getNode('expr') instanceof NameExpression && '_self' === $this->getNode('expr')->getAttribute('name')) {
            $compiler->raw('$this');
        } else {
            $compiler
                ->raw('$this->loadTemplate(')
                ->subcompile($this->getNode('expr'))
                ->raw(', ')
                ->repr($this->getTemplateName())
                ->raw(', ')
                ->repr($this->getTemplateLine())
                ->raw(')->unwrap()')
            ;
        }

        $compiler->raw(";\n");
    }
}
