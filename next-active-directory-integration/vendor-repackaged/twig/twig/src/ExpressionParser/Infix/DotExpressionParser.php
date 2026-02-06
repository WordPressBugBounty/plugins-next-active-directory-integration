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

use Dreitier\Nadi\Vendor\Twig\Error\SyntaxError;
use Dreitier\Nadi\Vendor\Twig\ExpressionParser\AbstractExpressionParser;
use Dreitier\Nadi\Vendor\Twig\ExpressionParser\ExpressionParserDescriptionInterface;
use Dreitier\Nadi\Vendor\Twig\ExpressionParser\InfixAssociativity;
use Dreitier\Nadi\Vendor\Twig\ExpressionParser\InfixExpressionParserInterface;
use Dreitier\Nadi\Vendor\Twig\Lexer;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\AbstractExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\ArrayExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\ConstantExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\GetAttrExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\MacroReferenceExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\NameExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\Variable\TemplateVariable;
use Dreitier\Nadi\Vendor\Twig\Parser;
use Dreitier\Nadi\Vendor\Twig\Template;
use Dreitier\Nadi\Vendor\Twig\Token;

/**
 * @internal
 */
final class DotExpressionParser extends AbstractExpressionParser implements InfixExpressionParserInterface, ExpressionParserDescriptionInterface
{
    use ArgumentsTrait;

    public function parse(Parser $parser, AbstractExpression $expr, Token $token): AbstractExpression
    {
        $stream = $parser->getStream();
        $token = $stream->getCurrent();
        $lineno = $token->getLine();
        $arguments = new ArrayExpression([], $lineno);
        $type = Template::ANY_CALL;

        if ($stream->nextIf(Token::OPERATOR_TYPE, '(')) {
            $attribute = $parser->parseExpression();
            $stream->expect(Token::PUNCTUATION_TYPE, ')');
        } else {
            $token = $stream->next();
            if (
                $token->test(Token::NAME_TYPE)
                || $token->test(Token::NUMBER_TYPE)
                || ($token->test(Token::OPERATOR_TYPE) && preg_match(Lexer::REGEX_NAME, $token->getValue()))
            ) {
                $attribute = new ConstantExpression($token->getValue(), $token->getLine());
            } else {
                throw new SyntaxError(\sprintf('Expected name or number, got value "%s" of type %s.', $token->getValue(), $token->toEnglish()), $token->getLine(), $stream->getSourceContext());
            }
        }

        if ($stream->test(Token::OPERATOR_TYPE, '(')) {
            $type = Template::METHOD_CALL;
            $arguments = $this->parseCallableArguments($parser, $token->getLine());
        }

        if (
            $expr instanceof NameExpression
            && (
                null !== $parser->getImportedSymbol('template', $expr->getAttribute('name'))
                || '_self' === $expr->getAttribute('name') && $attribute instanceof ConstantExpression
            )
        ) {
            return new MacroReferenceExpression(new TemplateVariable($expr->getAttribute('name'), $expr->getTemplateLine()), 'macro_'.$attribute->getAttribute('value'), $arguments, $expr->getTemplateLine());
        }

        return new GetAttrExpression($expr, $attribute, $arguments, $type, $lineno);
    }

    public function getName(): string
    {
        return '.';
    }

    public function getDescription(): string
    {
        return 'Get an attribute on a variable';
    }

    public function getPrecedence(): int
    {
        return 512;
    }

    public function getAssociativity(): InfixAssociativity
    {
        return InfixAssociativity::Left;
    }
}
