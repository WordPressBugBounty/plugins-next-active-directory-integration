<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by __root__ on 24-April-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Dreitier\Nadi\Vendor\Twig\TokenParser;

use Dreitier\Nadi\Vendor\Twig\Node\Expression\Variable\AssignContextVariable;
use Dreitier\Nadi\Vendor\Twig\Node\ForNode;
use Dreitier\Nadi\Vendor\Twig\Node\Node;
use Dreitier\Nadi\Vendor\Twig\Token;

/**
 * Loops over each item of a sequence.
 *
 *   <ul>
 *    {% for user in users %}
 *      <li>{{ user.username|e }}</li>
 *    {% endfor %}
 *   </ul>
 *
 * @internal
 */
final class ForTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $targets = $this->parser->getExpressionParser()->parseAssignmentExpression();
        $stream->expect(Token::OPERATOR_TYPE, 'in');
        $seq = $this->parser->getExpressionParser()->parseExpression();

        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse([$this, 'decideForFork']);
        if ('else' == $stream->next()->getValue()) {
            $stream->expect(Token::BLOCK_END_TYPE);
            $else = $this->parser->subparse([$this, 'decideForEnd'], true);
        } else {
            $else = null;
        }
        $stream->expect(Token::BLOCK_END_TYPE);

        if (\count($targets) > 1) {
            $keyTarget = $targets->getNode('0');
            $keyTarget = new AssignContextVariable($keyTarget->getAttribute('name'), $keyTarget->getTemplateLine());
            $valueTarget = $targets->getNode('1');
        } else {
            $keyTarget = new AssignContextVariable('_key', $lineno);
            $valueTarget = $targets->getNode('0');
        }
        $valueTarget = new AssignContextVariable($valueTarget->getAttribute('name'), $valueTarget->getTemplateLine());

        return new ForNode($keyTarget, $valueTarget, $seq, null, $body, $else, $lineno);
    }

    public function decideForFork(Token $token): bool
    {
        return $token->test(['else', 'endfor']);
    }

    public function decideForEnd(Token $token): bool
    {
        return $token->test('endfor');
    }

    public function getTag(): string
    {
        return 'for';
    }
}
