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

use Dreitier\Nadi\Vendor\Twig\ExpressionParser\AbstractExpressionParser;
use Dreitier\Nadi\Vendor\Twig\ExpressionParser\ExpressionParserDescriptionInterface;
use Dreitier\Nadi\Vendor\Twig\ExpressionParser\InfixAssociativity;
use Dreitier\Nadi\Vendor\Twig\ExpressionParser\InfixExpressionParserInterface;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\AbstractExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\ArrowFunctionExpression;
use Dreitier\Nadi\Vendor\Twig\Parser;
use Dreitier\Nadi\Vendor\Twig\Token;

/**
 * @internal
 */
final class ArrowExpressionParser extends AbstractExpressionParser implements InfixExpressionParserInterface, ExpressionParserDescriptionInterface
{
    public function parse(Parser $parser, AbstractExpression $expr, Token $token): AbstractExpression
    {
        // As the expression of the arrow function is independent from the current precedence, we want a precedence of 0
        return new ArrowFunctionExpression($parser->parseExpression(), $expr, $token->getLine());
    }

    public function getName(): string
    {
        return '=>';
    }

    public function getDescription(): string
    {
        return 'Arrow function (x => expr)';
    }

    public function getPrecedence(): int
    {
        return 250;
    }

    public function getAssociativity(): InfixAssociativity
    {
        return InfixAssociativity::Left;
    }
}
