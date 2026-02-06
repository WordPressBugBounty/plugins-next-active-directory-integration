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
 * Interface implemented by expressions that support the defined test.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface SupportDefinedTestInterface
{
    public function enableDefinedTest(): void;

    public function isDefinedTestEnabled(): bool;
}
