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

namespace Dreitier\Nadi\Vendor\Twig\ExpressionParser;

/**
 * @internal
 */
enum ExpressionParserType: string
{
    case Prefix = 'prefix';
    case Infix = 'infix';

    public static function getType(object $object): ExpressionParserType
    {
        if ($object instanceof PrefixExpressionParserInterface) {
            return self::Prefix;
        }
        if ($object instanceof InfixExpressionParserInterface) {
            return self::Infix;
        }

        throw new \InvalidArgumentException(\sprintf('Unsupported expression parser type: %s', $object::class));
    }
}
