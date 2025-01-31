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

namespace Dreitier\Nadi\Vendor\Twig\Node\Expression;

use Dreitier\Nadi\Vendor\Twig\Attribute\FirstClassTwigCallableReady;
use Dreitier\Nadi\Vendor\Twig\Compiler;
use Dreitier\Nadi\Vendor\Twig\Node\NameDeprecation;
use Dreitier\Nadi\Vendor\Twig\Node\Node;
use Dreitier\Nadi\Vendor\Twig\TwigFunction;

class FunctionExpression extends CallExpression
{
    #[FirstClassTwigCallableReady]
    public function __construct(TwigFunction|string $function, Node $arguments, int $lineno)
    {
        if ($function instanceof TwigFunction) {
            $name = $function->getName();
        } else {
            $name = $function;
            trigger_deprecation('twig/twig', '3.12', 'Not passing an instance of "TwigFunction" when creating a "%s" function of type "%s" is deprecated.', $name, static::class);
        }

        parent::__construct(['arguments' => $arguments], ['name' => $name, 'type' => 'function', 'is_defined_test' => false], $lineno);

        if ($function instanceof TwigFunction) {
            $this->setAttribute('dreitier_nadi__twig_callable', $function);
        }

        $this->deprecateAttribute('needs_charset', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('needs_environment', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('needs_context', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('arguments', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('callable', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('is_variadic', new NameDeprecation('twig/twig', '3.12'));
        $this->deprecateAttribute('dynamic_name', new NameDeprecation('twig/twig', '3.12'));
    }

    public function compile(Compiler $compiler)
    {
        $name = $this->getAttribute('name');
        if ($this->hasAttribute('dreitier_nadi__twig_callable')) {
            $name = $this->getAttribute('dreitier_nadi__twig_callable')->getName();
            if ($name !== $this->getAttribute('name')) {
                trigger_deprecation('twig/twig', '3.12', 'Changing the value of a "function" node in a NodeVisitor class is not supported anymore.');
                $this->removeAttribute('dreitier_nadi__twig_callable');
            }
        }

        if (!$this->hasAttribute('dreitier_nadi__twig_callable')) {
            $this->setAttribute('dreitier_nadi__twig_callable', $compiler->getEnvironment()->getFunction($name));
        }

        if ('constant' === $name && $this->getAttribute('is_defined_test')) {
            $this->getNode('arguments')->setNode('checkDefined', new ConstantExpression(true, $this->getTemplateLine()));
        }

        $this->compileCallable($compiler);
    }
}
