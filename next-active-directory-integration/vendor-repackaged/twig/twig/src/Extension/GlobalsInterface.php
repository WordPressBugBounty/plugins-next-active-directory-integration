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

namespace Dreitier\Nadi\Vendor\Twig\Extension;

/**
 * Allows Twig extensions to add globals to the context.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface GlobalsInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getGlobals(): array;
}
