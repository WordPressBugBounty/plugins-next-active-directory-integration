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

namespace Dreitier\Nadi\Vendor\Twig\ExpressionParser\Infix;

use Dreitier\Nadi\Vendor\Twig\Error\SyntaxError;
use Dreitier\Nadi\Vendor\Twig\ExpressionParser\InfixAssociativity;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\AbstractExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\ArrayExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\Binary\AbstractBinary;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\Binary\ObjectDestructuringSetBinary;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\Binary\SequenceDestructuringSetBinary;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\Binary\SetBinary;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\Variable\ContextVariable;
use Dreitier\Nadi\Vendor\Twig\Parser;
use Dreitier\Nadi\Vendor\Twig\Token;

/**
 * @internal
 */
class AssignmentExpressionParser extends BinaryOperatorExpressionParser
{
    public function __construct(
        string $name,
    ) {
        parent::__construct(SetBinary::class, $name, 0, InfixAssociativity::Right);
    }

    /**
     * @return AbstractBinary
     */
    public function parse(Parser $parser, AbstractExpression $left, Token $token): AbstractExpression
    {
        if (!$left instanceof ContextVariable && !$left instanceof ArrayExpression) {
            throw new SyntaxError(\sprintf('Cannot assign to "%s", only variables can be assigned.', $left::class), $token->getLine(), $parser->getStream()->getSourceContext());
        }
        $right = $parser->parseExpression(InfixAssociativity::Left === $this->getAssociativity() ? $this->getPrecedence() + 1 : $this->getPrecedence());
        $right = match ($this->getName()) {
            '=' => $right,
            default => throw new \LogicException(\sprintf('Unknown operator: %s.', $this->getName())),
        };

        if ($left instanceof ArrayExpression) {
            if ($left->isSequence()) {
                return new SequenceDestructuringSetBinary($left, $right, $token->getLine());
            }

            return new ObjectDestructuringSetBinary($left, $right, $token->getLine());
        }

        return new SetBinary($left, $right, $token->getLine());
    }

    public function getDescription(): string
    {
        return 'Assignment operator';
    }
}
