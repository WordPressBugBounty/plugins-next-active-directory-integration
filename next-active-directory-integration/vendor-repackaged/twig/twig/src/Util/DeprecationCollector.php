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

namespace Dreitier\Nadi\Vendor\Twig\Util;

use Dreitier\Nadi\Vendor\Twig\Environment;
use Dreitier\Nadi\Vendor\Twig\Error\SyntaxError;
use Dreitier\Nadi\Vendor\Twig\Source;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class DeprecationCollector
{
    public function __construct(
        private Environment $twig,
    ) {
    }

    /**
     * Returns deprecations for templates contained in a directory.
     *
     * @param string $dir A directory where templates are stored
     * @param string $ext Limit the loaded templates by extension
     *
     * @return array An array of deprecations
     */
    public function collectDir(string $dir, string $ext = '.twig'): array
    {
        $iterator = new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::LEAVES_ONLY
            ), '{'.preg_quote($ext).'$}'
        );

        return $this->collect(new TemplateDirIterator($iterator));
    }

    /**
     * Returns deprecations for passed templates.
     *
     * @param \Traversable $iterator An iterator of templates (where keys are template names and values the contents of the template)
     *
     * @return array An array of deprecations
     */
    public function collect(\Traversable $iterator): array
    {
        $deprecations = [];
        set_error_handler(function ($type, $msg) use (&$deprecations) {
            if (\E_USER_DEPRECATED === $type) {
                $deprecations[] = $msg;
            }

            return false;
        });

        foreach ($iterator as $name => $contents) {
            try {
                $this->twig->parse($this->twig->tokenize(new Source($contents, $name)));
            } catch (SyntaxError $e) {
                // ignore templates containing syntax errors
            }
        }

        restore_error_handler();

        return $deprecations;
    }
}
