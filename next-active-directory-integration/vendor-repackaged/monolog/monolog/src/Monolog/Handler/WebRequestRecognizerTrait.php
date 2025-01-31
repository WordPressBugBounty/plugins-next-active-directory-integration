<?php
/**
 * @license MIT
 *
 * Modified by __root__ on 31-January-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */ declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dreitier\Nadi\Vendor\Monolog\Handler;

trait WebRequestRecognizerTrait
{
    /**
     * Checks if PHP's serving a web request
     * @return bool
     */
    protected function isWebRequest(): bool
    {
        return 'cli' !== \PHP_SAPI && 'phpdbg' !== \PHP_SAPI;
    }
}
