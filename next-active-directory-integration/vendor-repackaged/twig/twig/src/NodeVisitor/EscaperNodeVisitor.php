<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by __root__ on 31-January-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Dreitier\Nadi\Vendor\Twig\NodeVisitor;

use Dreitier\Nadi\Vendor\Twig\Environment;
use Dreitier\Nadi\Vendor\Twig\Extension\EscaperExtension;
use Dreitier\Nadi\Vendor\Twig\Node\AutoEscapeNode;
use Dreitier\Nadi\Vendor\Twig\Node\BlockNode;
use Dreitier\Nadi\Vendor\Twig\Node\BlockReferenceNode;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\AbstractExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\ConstantExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\FilterExpression;
use Dreitier\Nadi\Vendor\Twig\Node\Expression\OperatorEscapeInterface;
use Dreitier\Nadi\Vendor\Twig\Node\ImportNode;
use Dreitier\Nadi\Vendor\Twig\Node\ModuleNode;
use Dreitier\Nadi\Vendor\Twig\Node\Node;
use Dreitier\Nadi\Vendor\Twig\Node\Nodes;
use Dreitier\Nadi\Vendor\Twig\Node\PrintNode;
use Dreitier\Nadi\Vendor\Twig\NodeTraverser;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
final class EscaperNodeVisitor implements NodeVisitorInterface
{
    private $statusStack = [];
    private $blocks = [];
    private $safeAnalysis;
    private $traverser;
    private $defaultStrategy = false;
    private $safeVars = [];

    public function __construct()
    {
        $this->safeAnalysis = new SafeAnalysisNodeVisitor();
    }

    public function enterNode(Node $node, Environment $env): Node
    {
        if ($node instanceof ModuleNode) {
            if ($env->hasExtension(EscaperExtension::class) && $defaultStrategy = $env->getExtension(EscaperExtension::class)->getDefaultStrategy($node->getTemplateName())) {
                $this->defaultStrategy = $defaultStrategy;
            }
            $this->safeVars = [];
            $this->blocks = [];
        } elseif ($node instanceof AutoEscapeNode) {
            $this->statusStack[] = $node->getAttribute('value');
        } elseif ($node instanceof BlockNode) {
            $this->statusStack[] = $this->blocks[$node->getAttribute('name')] ?? $this->needEscaping();
        } elseif ($node instanceof ImportNode) {
            $this->safeVars[] = $node->getNode('var')->getNode('var')->getAttribute('name');
        }

        return $node;
    }

    public function leaveNode(Node $node, Environment $env): ?Node
    {
        if ($node instanceof ModuleNode) {
            $this->defaultStrategy = false;
            $this->safeVars = [];
            $this->blocks = [];
        } elseif ($node instanceof FilterExpression) {
            return $this->preEscapeFilterNode($node, $env);
        } elseif ($node instanceof PrintNode && false !== $type = $this->needEscaping()) {
            $expression = $node->getNode('expr');
            if ($expression instanceof OperatorEscapeInterface) {
                $this->escapeConditional($expression, $env, $type);
            } else {
                $node->setNode('expr', $this->escapeExpression($expression, $env, $type));
            }

            return $node;
        }

        if ($node instanceof AutoEscapeNode || $node instanceof BlockNode) {
            array_pop($this->statusStack);
        } elseif ($node instanceof BlockReferenceNode) {
            $this->blocks[$node->getAttribute('name')] = $this->needEscaping();
        }

        return $node;
    }

    /**
     * @param AbstractExpression&OperatorEscapeInterface $expression
     */
    private function escapeConditional($expression, Environment $env, string $type): void
    {
        foreach ($expression->getOperandNamesToEscape() as $name) {
            /** @var AbstractExpression $operand */
            $operand = $expression->getNode($name);
            if ($operand instanceof OperatorEscapeInterface) {
                $this->escapeConditional($operand, $env, $type);
            } else {
                $expression->setNode($name, $this->escapeExpression($operand, $env, $type));
            }
        }
    }

    private function escapeExpression(AbstractExpression $expression, Environment $env, string $type): AbstractExpression
    {
        return $this->isSafeFor($type, $expression, $env) ? $expression : $this->getEscaperFilter($env, $type, $expression);
    }

    private function preEscapeFilterNode(FilterExpression $filter, Environment $env): FilterExpression
    {
        if ($filter->hasAttribute('dreitier_nadi__twig_callable')) {
            $type = $filter->getAttribute('dreitier_nadi__twig_callable')->getPreEscape();
        } else {
            // legacy
            $name = $filter->getNode('filter', false)->getAttribute('value');
            $type = $env->getFilter($name)->getPreEscape();
        }

        if (null === $type) {
            return $filter;
        }

        /** @var AbstractExpression $node */
        $node = $filter->getNode('node');
        if ($this->isSafeFor($type, $node, $env)) {
            return $filter;
        }

        $filter->setNode('node', $this->getEscaperFilter($env, $type, $node));

        return $filter;
    }

    private function isSafeFor(string $type, AbstractExpression $expression, Environment $env): bool
    {
        $safe = $this->safeAnalysis->getSafe($expression);

        if (!$safe) {
            if (null === $this->traverser) {
                $this->traverser = new NodeTraverser($env, [$this->safeAnalysis]);
            }

            $this->safeAnalysis->setSafeVars($this->safeVars);

            $this->traverser->traverse($expression);
            $safe = $this->safeAnalysis->getSafe($expression);
        }

        return \in_array($type, $safe) || \in_array('all', $safe);
    }

    private function needEscaping()
    {
        if (\count($this->statusStack)) {
            return $this->statusStack[\count($this->statusStack) - 1];
        }

        return $this->defaultStrategy ?: false;
    }

    private function getEscaperFilter(Environment $env, string $type, AbstractExpression $node): FilterExpression
    {
        $line = $node->getTemplateLine();
        $filter = $env->getFilter('escape');
        $args = new Nodes([new ConstantExpression($type, $line), new ConstantExpression(null, $line), new ConstantExpression(true, $line)]);

        return new FilterExpression($node, $filter, $args, $line);
    }

    public function getPriority(): int
    {
        return 0;
    }
}
