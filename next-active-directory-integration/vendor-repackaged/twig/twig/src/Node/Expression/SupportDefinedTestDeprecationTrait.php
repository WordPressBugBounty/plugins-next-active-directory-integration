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

namespace Dreitier\Nadi\Vendor\Twig\Node\Expression;

/**
 * @internal
 *
 * To be removed in 4.0
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
trait SupportDefinedTestDeprecationTrait
{
    public function getAttribute($name, $default = null)
    {
        if ('is_defined_test' === $name) {
            trigger_deprecation('twig/twig', '3.21', 'The "is_defined_test" attribute is deprecated, call "isDefinedTestEnabled()" instead.');

            return $this->isDefinedTestEnabled();
        }

        return parent::getAttribute($name, $default);
    }

    public function setAttribute(string $name, $value): void
    {
        if ('is_defined_test' === $name) {
            trigger_deprecation('twig/twig', '3.21', 'The "is_defined_test" attribute is deprecated, call "enableDefinedTest()" instead.');

            $this->definedTest = (bool) $value;
        } else {
            parent::setAttribute($name, $value);
        }
    }
}
