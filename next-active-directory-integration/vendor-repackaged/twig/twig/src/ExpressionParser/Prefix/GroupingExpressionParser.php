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

namespace Dreitier\Nadi\Vendor\Twig\ExpressionParser\Prefix;

use Dreitier\Nadi\Vendor\Twig\Error\SyntaxError;
use Dreitier\Nadi\Vendor\Twig\ExpressionParser\AbstractExpressionParser;
use Dreitier\Nadi\Vendor\Twig\ExpressionParser\ExpressionParserDescriptionInterface;
use Dreitier\Nadi\Vendor\Twig\ExpressionParser\PrefixExpressionParserInterface;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\AbstractExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\ListExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\Variable\ContextVariable;
use Dreitier\Nadi\Vendor\Twig\Parser;
use Dreitier\Nadi\Vendor\Twig\Token;

/**
 * @internal
 */
final class GroupingExpressionParser extends AbstractExpressionParser implements PrefixExpressionParserInterface, ExpressionParserDescriptionInterface
{
    public function parse(Parser $parser, Token $token): AbstractExpression
    {
        $stream = $parser->getStream();
        $expr = $parser->parseExpression($this->getPrecedence());

        if ($stream->nextIf(Token::PUNCTUATION_TYPE, ')')) {
            if (!$stream->test(Token::OPERATOR_TYPE, '=>')) {
                return $expr->setExplicitParentheses();
            }

            return new ListExpression([$expr], $token->getLine());
        }

        // determine if we are parsing an arrow function arguments
        if (!$stream->test(Token::PUNCTUATION_TYPE, ',')) {
            $stream->expect(Token::PUNCTUATION_TYPE, ')', 'An opened parenthesis is not properly closed');
        }

        $names = [$expr];
        while (true) {
            if ($stream->nextIf(Token::PUNCTUATION_TYPE, ')')) {
                break;
            }
            $stream->expect(Token::PUNCTUATION_TYPE, ',');
            $token = $stream->expect(Token::NAME_TYPE);
            $names[] = new ContextVariable($token->getValue(), $token->getLine());
        }

        if (!$stream->test(Token::OPERATOR_TYPE, '=>')) {
            throw new SyntaxError('A list of variables must be followed by an arrow.', $stream->getCurrent()->getLine(), $stream->getSourceContext());
        }

        return new ListExpression($names, $token->getLine());
    }

    public function getName(): string
    {
        return '(';
    }

    public function getDescription(): string
    {
        return 'Explicit group expression (a)';
    }

    public function getPrecedence(): int
    {
        return 0;
    }
}
