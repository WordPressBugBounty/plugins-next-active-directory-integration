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

namespace Dreitier\Nadi\Vendor\Twig\Profiler\Node;

use Dreitier\Nadi\Vendor\Twig\Attribute\YieldReady;
use Dreitier\Nadi\Vendor\Twig\Compiler;
use Dreitier\Nadi\Vendor\Twig\Node\Node;

/**
 * Represents a profile enter node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
#[YieldReady]
class EnterProfileNode extends Node
{
    public function __construct(string $extensionName, string $type, string $name, string $varName)
    {
        parent::__construct([], ['extension_name' => $extensionName, 'name' => $name, 'type' => $type, 'var_name' => $varName]);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->write(\sprintf('$%s = $this->extensions[', $this->getAttribute('var_name')))
            ->repr($this->getAttribute('extension_name'))
            ->raw("];\n")
            ->write(\sprintf('$%s->enter($%s = new \Dreitier\Nadi\Vendor\Twig\Profiler\Profile($this->getTemplateName(), ', $this->getAttribute('var_name'), $this->getAttribute('var_name').'_prof'))
            ->repr($this->getAttribute('type'))
            ->raw(', ')
            ->repr($this->getAttribute('name'))
            ->raw("));\n\n")
        ;
    }
}
