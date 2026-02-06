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

use Dreitier\Nadi\Vendor\Twig\Error\SyntaxError;
use Dreitier\Nadi\Vendor\Twig\Node\EmptyNode;
use Dreitier\Nadi\Vendor\Twig\Node\Node;
use Dreitier\Nadi\Vendor\Twig\Node\Nodes;
use Dreitier\Nadi\Vendor\Twig\Token;

/**
 * @internal
 */
final class GuardTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $stream = $this->parser->getStream();
        $typeToken = $stream->expect(Token::NAME_TYPE);
        if (!\in_array($typeToken->getValue(), ['function', 'filter', 'test'], true)) {
            throw new SyntaxError(\sprintf('Supported guard types are function, filter and test, "%s" given.', $typeToken->getValue()), $typeToken->getLine(), $stream->getSourceContext());
        }
        $method = 'get'.$typeToken->getValue();

        $nameToken = $stream->expect(Token::NAME_TYPE);
        $name = $nameToken->getValue();
        if ('test' === $typeToken->getValue() && $stream->test(Token::NAME_TYPE)) {
            // try 2-words tests
            $name .= ' '.$stream->getCurrent()->getValue();
            $stream->next();
        }

        try {
            $exists = null !== $this->parser->getEnvironment()->$method($name);
        } catch (SyntaxError) {
            $exists = false;
        }

        $stream->expect(Token::BLOCK_END_TYPE);
        if ($exists) {
            $body = $this->parser->subparse([$this, 'decideGuardFork']);
        } else {
            $body = new EmptyNode();
            $this->parser->subparseIgnoreUnknownTwigCallables([$this, 'decideGuardFork']);
        }
        $else = new EmptyNode();
        if ('else' === $stream->next()->getValue()) {
            $stream->expect(Token::BLOCK_END_TYPE);
            $else = $this->parser->subparse([$this, 'decideGuardEnd'], true);
        }
        $stream->expect(Token::BLOCK_END_TYPE);

        return new Nodes([$exists ? $body : $else]);
    }

    public function decideGuardFork(Token $token): bool
    {
        return $token->test(['else', 'endguard']);
    }

    public function decideGuardEnd(Token $token): bool
    {
        return $token->test(['endguard']);
    }

    public function getTag(): string
    {
        return 'guard';
    }
}
