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

namespace Dreitier\Nadi\Vendor\Twig\Node;

use Dreitier\Nadi\Vendor\Twig\Attribute\YieldReady;
use Dreitier\Nadi\Vendor\Twig\Compiler;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
#[YieldReady]
class CheckSecurityCallNode extends Node
{
    /**
     * @return void
     */
    public function compile(Compiler $compiler)
    {
        $compiler
            ->write("\$this->sandbox = \$this->extensions[SandboxExtension::class];\n")
            ->write("\$this->checkSecurity();\n")
        ;
    }
}
