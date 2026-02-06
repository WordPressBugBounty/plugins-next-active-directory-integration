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

namespace Dreitier\Nadi\Vendor\Twig\Node\Expression\Filter;

use Dreitier\Nadi\Vendor\Twig\Attribute\FirstClassTwigCallableReady;
use Dreitier\Nadi\Vendor\Twig\Compiler;
use Dreitier\Nadi\Vendor\Twig\Node\EmptyNode;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\AbstractExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\ConstantExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\FilterExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Node;
use Dreitier\Nadi\Vendor\Twig\TwigFilter;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class RawFilter extends FilterExpression
{
    /**
     * @param AbstractExpression $node
     */
    #[FirstClassTwigCallableReady]
    public function __construct(Node $node, TwigFilter|ConstantExpression|null $filter = null, ?Node $arguments = null, int $lineno = 0)
    {
        if (!$node instanceof AbstractExpression) {
            trigger_deprecation('twig/twig', '3.15', 'Not passing a "%s" instance to the "node" argument of "%s" is deprecated ("%s" given).', AbstractExpression::class, static::class, $node::class);
        }

        parent::__construct($node, $filter ?: new TwigFilter('raw', null, ['is_safe' => ['all']]), $arguments ?: new EmptyNode(), $lineno ?: $node->getTemplateLine());
    }

    public function compile(Compiler $compiler): void
    {
        $compiler->subcompile($this->getNode('node'));
    }
}
