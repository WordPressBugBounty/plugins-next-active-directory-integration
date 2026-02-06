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

namespace Dreitier\Nadi\Vendor\Twig\ExpressionParser\Infix;

use Dreitier\Nadi\Vendor\Twig\Node\Expression\AbstractExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\Unary\NotUnary;
use Dreitier\Nadi\Vendor\Twig\Parser;
use Dreitier\Nadi\Vendor\Twig\Token;

/**
 * @internal
 */
final class IsNotExpressionParser extends IsExpressionParser
{
    public function parse(Parser $parser, AbstractExpression $expr, Token $token): AbstractExpression
    {
        return new NotUnary(parent::parse($parser, $expr, $token), $token->getLine());
    }

    public function getName(): string
    {
        return 'is not';
    }
}
