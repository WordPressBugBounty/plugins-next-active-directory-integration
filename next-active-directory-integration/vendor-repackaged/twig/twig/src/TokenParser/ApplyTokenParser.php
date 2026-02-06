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

namespace Dreitier\Nadi\Vendor\Twig\TokenParser;

use Dreitier\Nadi\Vendor\Twig\ExpressionParser\Infix\FilterExpressionParser;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\Variable\LocalVariable;
use Dreitier\Nadi\Vendor\Twig\Node\Node;
use Dreitier\Nadi\Vendor\Twig\Node\Nodes;
use Dreitier\Nadi\Vendor\Twig\Node\PrintNode;
use Dreitier\Nadi\Vendor\Twig\Node\SetNode;
use Dreitier\Nadi\Vendor\Twig\Token;

/**
 * Applies filters on a section of a template.
 *
 *   {% apply upper %}
 *      This text becomes uppercase
 *   {% endapply %}
 *
 * @internal
 */
final class ApplyTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $lineno = $token->getLine();
        $ref = new LocalVariable(null, $lineno);
        $filter = $ref;
        $op = $this->parser->getEnvironment()->getExpressionParsers()->getByClass(FilterExpressionParser::class);
        while (true) {
            $filter = $op->parse($this->parser, $filter, $this->parser->getCurrentToken());
            if (!$this->parser->getStream()->test(Token::OPERATOR_TYPE, '|')) {
                break;
            }
            $this->parser->getStream()->next();
        }

        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideApplyEnd'], true);
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new Nodes([
            new SetNode(true, $ref, $body, $lineno),
            new PrintNode($filter, $lineno),
        ], $lineno);
    }

    public function decideApplyEnd(Token $token): bool
    {
        return $token->test('endapply');
    }

    public function getTag(): string
    {
        return 'apply';
    }
}
