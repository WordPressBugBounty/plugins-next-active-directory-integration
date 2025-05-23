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

namespace Dreitier\Nadi\Vendor\Twig\Extension;

use Dreitier\Nadi\Vendor\Twig\NodeVisitor\SandboxNodeVisitor;
use Dreitier\Nadi\Vendor\Twig\Sandbox\SecurityNotAllowedMethodError;
use Dreitier\Nadi\Vendor\Twig\Sandbox\SecurityNotAllowedPropertyError;
use Dreitier\Nadi\Vendor\Twig\Sandbox\SecurityPolicyInterface;
use Dreitier\Nadi\Vendor\Twig\Sandbox\SourcePolicyInterface;
use Dreitier\Nadi\Vendor\Twig\Source;
use Dreitier\Nadi\Vendor\Twig\TokenParser\SandboxTokenParser;

final class SandboxExtension extends AbstractExtension
{
    private $sandboxedGlobally;
    private $sandboxed;
    private $policy;
    private $sourcePolicy;

    public function __construct(SecurityPolicyInterface $policy, $sandboxed = false, ?SourcePolicyInterface $sourcePolicy = null)
    {
        $this->policy = $policy;
        $this->sandboxedGlobally = $sandboxed;
        $this->sourcePolicy = $sourcePolicy;
    }

    public function getTokenParsers(): array
    {
        return [new SandboxTokenParser()];
    }

    public function getNodeVisitors(): array
    {
        return [new SandboxNodeVisitor()];
    }

    public function enableSandbox(): void
    {
        $this->sandboxed = true;
    }

    public function disableSandbox(): void
    {
        $this->sandboxed = false;
    }

    public function isSandboxed(?Source $source = null): bool
    {
        return $this->sandboxedGlobally || $this->sandboxed || $this->isSourceSandboxed($source);
    }

    public function isSandboxedGlobally(): bool
    {
        return $this->sandboxedGlobally;
    }

    private function isSourceSandboxed(?Source $source): bool
    {
        if (null === $source || null === $this->sourcePolicy) {
            return false;
        }

        return $this->sourcePolicy->enableSandbox($source);
    }

    public function setSecurityPolicy(SecurityPolicyInterface $policy)
    {
        $this->policy = $policy;
    }

    public function getSecurityPolicy(): SecurityPolicyInterface
    {
        return $this->policy;
    }

    public function checkSecurity($tags, $filters, $functions, ?Source $source = null): void
    {
        if ($this->isSandboxed($source)) {
            $this->policy->checkSecurity($tags, $filters, $functions);
        }
    }

    public function checkMethodAllowed($obj, $method, int $lineno = -1, ?Source $source = null): void
    {
        if ($this->isSandboxed($source)) {
            try {
                $this->policy->checkMethodAllowed($obj, $method);
            } catch (SecurityNotAllowedMethodError $e) {
                $e->setSourceContext($source);
                $e->setTemplateLine($lineno);

                throw $e;
            }
        }
    }

    public function checkPropertyAllowed($obj, $property, int $lineno = -1, ?Source $source = null): void
    {
        if ($this->isSandboxed($source)) {
            try {
                $this->policy->checkPropertyAllowed($obj, $property);
            } catch (SecurityNotAllowedPropertyError $e) {
                $e->setSourceContext($source);
                $e->setTemplateLine($lineno);

                throw $e;
            }
        }
    }

    public function ensureToStringAllowed($obj, int $lineno = -1, ?Source $source = null)
    {
        if (\is_array($obj)) {
            $this->ensureToStringAllowedForArray($obj, $lineno, $source);

            return $obj;
        }

        if ($obj instanceof \Stringable && $this->isSandboxed($source)) {
            try {
                $this->policy->checkMethodAllowed($obj, '__toString');
            } catch (SecurityNotAllowedMethodError $e) {
                $e->setSourceContext($source);
                $e->setTemplateLine($lineno);

                throw $e;
            }
        }

        return $obj;
    }

    private function ensureToStringAllowedForArray(array $obj, int $lineno, ?Source $source, array &$stack = []): void
    {
        foreach ($obj as $k => $v) {
            if (!$v) {
                continue;
            }

            if (!\is_array($v)) {
                $this->ensureToStringAllowed($v, $lineno, $source);
                continue;
            }

            if ($r = \ReflectionReference::fromArrayElement($obj, $k)) {
                if (isset($stack[$r->getId()])) {
                    continue;
                }

                $stack[$r->getId()] = true;
            }

            $this->ensureToStringAllowedForArray($v, $lineno, $source, $stack);
        }
    }
}
