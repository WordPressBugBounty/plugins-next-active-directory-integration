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
 * Modified by __root__ on 26-August-2026 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Dreitier\Nadi\Vendor\Twig\Node\Expression\Binary;

use Dreitier\Nadi\Vendor\Twig\Compiler;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\ReturnBoolInterface;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\Test\TrueTest;
use Dreitier\Nadi\Vendor\Twig\Node\Node;

class XorBinary extends AbstractBinary implements ReturnBoolInterface
{
    public function __construct(Node $left, Node $right, int $lineno)
    {
        parent::__construct(TrueTest::wrap($left), TrueTest::wrap($right), $lineno);
    }

    public function operator(Compiler $compiler): Compiler
    {
        return $compiler->raw('xor');
    }
}
