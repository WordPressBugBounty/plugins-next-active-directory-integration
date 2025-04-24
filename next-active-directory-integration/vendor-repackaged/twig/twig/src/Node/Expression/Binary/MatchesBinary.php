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
use Dreitier\Nadi\Vendor\Twig\Error\SyntaxError;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\ConstantExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Node;

class MatchesBinary extends AbstractBinary
{
    public function __construct(Node $left, Node $right, int $lineno)
    {
        if ($right instanceof ConstantExpression) {
            $regexp = $right->getAttribute('value');
            set_error_handler(static fn ($t, $m) => throw new SyntaxError(\sprintf('Regexp "%s" passed to "matches" is not valid: %s.', $regexp, substr($m, 14)), $lineno));
            try {
                preg_match($regexp, '');
            } finally {
                restore_error_handler();
            }
        }

        parent::__construct($left, $right, $lineno);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->raw('CoreExtension::matches(')
            ->subcompile($this->getNode('right'))
            ->raw(', ')
            ->subcompile($this->getNode('left'))
            ->raw(')')
        ;
    }

    public function operator(Compiler $compiler): Compiler
    {
        return $compiler->raw('');
    }
}
