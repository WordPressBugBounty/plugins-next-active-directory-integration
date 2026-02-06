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

use Dreitier\Nadi\Vendor\Twig\Node\Expression\AbstractExpression;
use Dreitier\Nadi\Vendor\Twig\Parser;
use Dreitier\Nadi\Vendor\Twig\Token;

interface PrefixExpressionParserInterface extends ExpressionParserInterface
{
    public function parse(Parser $parser, Token $token): AbstractExpression;
}
