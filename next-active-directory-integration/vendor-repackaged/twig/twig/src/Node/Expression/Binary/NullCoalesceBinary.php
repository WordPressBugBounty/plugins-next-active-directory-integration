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

namespace Dreitier\Nadi\Vendor\Twig\Node\Expression\Binary;

use Dreitier\Nadi\Vendor\Twig\Compiler;
use Dreitier\Nadi\Vendor\Twig\Node\EmptyNode;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\AbstractExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\BlockReferenceExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\NameExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\OperatorEscapeInterface;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\Test\DefinedTest;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\Test\NullTest;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\Unary\NotUnary;
use Dreitier\Nadi\Vendor\Twig\TwigTest;

final class NullCoalesceBinary extends AbstractBinary implements OperatorEscapeInterface
{
    public function __construct(AbstractExpression $left, AbstractExpression $right, int $lineno)
    {
        parent::__construct($left, $right, $lineno);

        if (!$left instanceof NameExpression) {
            $test = new DefinedTest(clone $left, new TwigTest('defined'), new EmptyNode(), $left->getTemplateLine());
            // for "block()", we don't need the null test as the return value is always a string
            if (!$left instanceof BlockReferenceExpression) {
                $test = new AndBinary(
                    $test,
                    new NotUnary(new NullTest($left, new TwigTest('null'), new EmptyNode(), $left->getTemplateLine()), $left->getTemplateLine()),
                    $left->getTemplateLine(),
                );
            }

            $this->setNode('test', $test);
        } else {
            $left->setAttribute('always_defined', true);
        }
    }

    public function compile(Compiler $compiler): void
    {
        /*
         * This optimizes only one case. PHP 7 also supports more complex expressions
         * that can return null. So, for instance, if log is defined, log("foo") ?? "..." works,
         * but log($a["foo"]) ?? "..." does not if $a["foo"] is not defined. More advanced
         * cases might be implemented as an optimizer node visitor, but has not been done
         * as benefits are probably not worth the added complexity.
         */
        if ($this->hasNode('test')) {
            $compiler
                ->raw('((')
                ->subcompile($this->getNode('test'))
                ->raw(') ? (')
                ->subcompile($this->getNode('left'))
                ->raw(') : (')
                ->subcompile($this->getNode('right'))
                ->raw('))')
            ;

            return;
        }

        parent::compile($compiler);
    }

    public function operator(Compiler $compiler): Compiler
    {
        return $compiler->raw('??');
    }

    public function getOperandNamesToEscape(): array
    {
        return $this->hasNode('test') ? ['left', 'right'] : ['right'];
    }
}
