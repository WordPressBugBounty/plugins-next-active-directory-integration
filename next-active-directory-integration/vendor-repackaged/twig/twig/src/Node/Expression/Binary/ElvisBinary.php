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

namespace Dreitier\Nadi\Vendor\Twig\Node\Expression\Binary;

use Dreitier\Nadi\Vendor\Twig\Compiler;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\AbstractExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\OperatorEscapeInterface;

final class ElvisBinary extends AbstractBinary implements OperatorEscapeInterface
{
    public function __construct(AbstractExpression $left, AbstractExpression $right, int $lineno)
    {
        parent::__construct($left, $right, $lineno);

        $this->setNode('test', clone $left);
        $left->setAttribute('always_defined', true);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->raw('((')
            ->subcompile($this->getNode('test'))
            ->raw(') ? (')
            ->subcompile($this->getNode('left'))
            ->raw(') : (')
            ->subcompile($this->getNode('right'))
            ->raw('))')
        ;
    }

    public function operator(Compiler $compiler): Compiler
    {
        return $compiler->raw('?:');
    }

    public function getOperandNamesToEscape(): array
    {
        return ['left', 'right'];
    }
}
