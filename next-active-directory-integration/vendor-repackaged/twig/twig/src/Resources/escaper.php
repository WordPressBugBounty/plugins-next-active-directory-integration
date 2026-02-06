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

use Dreitier\Nadi\Vendor\Twig\Environment;
use Dreitier\Nadi\Vendor\Twig\Extension\EscaperExtension;
use Dreitier\Nadi\Vendor\Twig\Node\Node;
use Dreitier\Nadi\Vendor\Twig\Runtime\EscaperRuntime;

/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function dreitier_nadi__twig_raw_filter($string)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);

    return $string;
}

/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function dreitier_nadi__twig_escape_filter(Environment $env, $string, $strategy = 'html', $charset = null, $autoescape = false)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);

    return $env->getRuntime(EscaperRuntime::class)->escape($string, $strategy, $charset, $autoescape);
}

/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function dreitier_nadi__dreitier_nadi__twig_escape_filter_is_safe(Node $filterArgs)
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);

    return EscaperExtension::escapeFilterIsSafe($filterArgs);
}
