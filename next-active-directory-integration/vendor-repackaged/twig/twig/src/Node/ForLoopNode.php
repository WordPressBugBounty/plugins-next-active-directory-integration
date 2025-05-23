<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by __root__ on 24-April-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Dreitier\Nadi\Vendor\Twig\Node;

use Dreitier\Nadi\Vendor\Twig\Attribute\YieldReady;
use Dreitier\Nadi\Vendor\Twig\Compiler;

/**
 * Internal node used by the for node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
#[YieldReady]
class ForLoopNode extends Node
{
    public function __construct(int $lineno)
    {
        parent::__construct([], ['with_loop' => false, 'ifexpr' => false, 'else' => false], $lineno);
    }

    public function compile(Compiler $compiler): void
    {
        if ($this->getAttribute('else')) {
            $compiler->write("\$context['_iterated'] = true;\n");
        }

        if ($this->getAttribute('with_loop')) {
            $compiler
                ->write("++\$context['loop']['index0'];\n")
                ->write("++\$context['loop']['index'];\n")
                ->write("\$context['loop']['first'] = false;\n")
                ->write("if (isset(\$context['loop']['revindex0'], \$context['loop']['revindex'])) {\n")
                ->indent()
                ->write("--\$context['loop']['revindex0'];\n")
                ->write("--\$context['loop']['revindex'];\n")
                ->write("\$context['loop']['last'] = 0 === \$context['loop']['revindex0'];\n")
                ->outdent()
                ->write("}\n")
            ;
        }
    }
}
