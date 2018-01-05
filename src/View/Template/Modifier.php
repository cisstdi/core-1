<?php

/**
 * Contao Bootstrap
 *
 * @package    contao-bootstrap
 * @subpackage Core
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/contao-bootstrap/core
 * @filesource
 */

declare(strict_types=1);

namespace ContaoBootstrap\Core\View\Template;

use Contao\Template;

/**
 * Interface Modifier describes an template modifier which is called when a template is parsed.
 *
 * @package ContaoBootstrap\Core\View\Template
 *
 * @deprecated Get's removed in 2.0.0.
 */
interface Modifier
{
    /**
     * Check if modifier supports a specific template by its name.
     *
     * @param string $templateName Template name.
     *
     * @return bool
     */
    public function supports(string $templateName): bool;

    /**
     * Prepare a template before parsing.
     *
     * @param Template $template Template.
     *
     * @return void
     */
    public function prepare(Template $template): void;

    /**
     * Modify the generated output.
     *
     * @param string $buffer       Template output.
     * @param string $templateName Template name.
     *
     * @return string
     */
    public function parse(string $buffer, string $templateName): string;
}
