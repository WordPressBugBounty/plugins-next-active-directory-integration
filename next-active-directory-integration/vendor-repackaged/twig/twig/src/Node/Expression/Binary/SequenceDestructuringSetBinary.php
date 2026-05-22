<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by __root__ on 22-May-2026 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Dreitier\Nadi\Vendor\Twig\Node\Expression\Binary;

use Dreitier\Nadi\Vendor\Twig\Compiler;
use Dreitier\Nadi\Vendor\Twig\Error\SyntaxError;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\AbstractExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\ArrayExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\EmptyExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\Variable\ContextVariable;
use Dreitier\Nadi\Vendor\Twig\Node\Node;

/**
 * @internal
 */
class SequenceDestructuringSetBinary extends AbstractBinary
{
    private array $variables = [];

    /**
     * @param ArrayExpression    $left  The array expression containing variables to assign to
     * @param AbstractExpression $right The expression providing values for assignment
     */
    public function __construct(Node $left, Node $right, int $lineno)
    {
        foreach ($left->getKeyValuePairs() as $pair) {
            if ($pair['value'] instanceof EmptyExpression) {
                $this->variables[] = null;
            } elseif ($pair['value'] instanceof ContextVariable) {
                $this->variables[] = $pair['value']->getAttribute('name');
            } else {
                throw new SyntaxError(\sprintf('Cannot assign to "%s", only variables can be assigned in sequence destructuring.', $pair['value']::class), $lineno);
            }
        }

        parent::__construct($left, $right, $lineno);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler->addDebugInfo($this);
        $compiler->raw('[');
        foreach ($this->variables as $i => $name) {
            if ($i) {
                $compiler->raw(', ');
            }
            if (null !== $name) {
                $compiler->raw('$context[')->repr($name)->raw(']');
            }
        }
        $compiler->raw('] = array_pad(')->subcompile($this->getNode('right'))->raw(', ')->repr(\count($this->variables))->raw(', null)');
    }

    public function operator(Compiler $compiler): Compiler
    {
        return $compiler->raw('=');
    }
}
