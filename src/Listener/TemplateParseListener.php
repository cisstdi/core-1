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

namespace ContaoBootstrap\Core\Listener;

use Contao\Template;
use ContaoBootstrap\Core\View\Template\Filter\PostRenderFilter;
use ContaoBootstrap\Core\View\Template\Filter\PreRenderFilter;
use ContaoBootstrap\Core\View\Template\Modifier;

/**
 * Class TemplateModifier contains all template modifiers used by bootstrap config.
 *
 * @package ContaoBootstrap
 */
final class TemplateParseListener
{
    /**
     * Template modifier.
     *
     * @var Modifier
     */
    private $modifier;

    /**
     * Pre render filter.
     *
     * @var PreRenderFilter
     */
    private $preRenderFilter;

    /**
     * Post render filter.
     *
     * @var PostRenderFilter
     */
    private $postRenderFilter;

    /**
     * Modifier constructor.
     *
     * @param Modifier         $modifier         Template modifier.
     * @param PreRenderFilter  $preRenderFilter  Pre render filter.
     * @param PostRenderFilter $postRenderFilter Post render filter.
     */
    public function __construct(
        Modifier $modifier,
        PreRenderFilter $preRenderFilter,
        PostRenderFilter $postRenderFilter
    ) {
        $this->modifier         = $modifier;
        $this->preRenderFilter  = $preRenderFilter;
        $this->postRenderFilter = $postRenderFilter;
    }

    /**
     * Execute all registered template modifiers.
     *
     * @param Template $template Current template.
     *
     * @return void
     */
    public function prepare(Template $template): void
    {
        if ($this->modifier->supports($template->getName())) {
            $this->modifier->prepare($template);
        }

        if ($this->preRenderFilter->supports($template)) {
            $this->preRenderFilter->filter($template);
        }
    }

    /**
     * Parse current template.
     *
     * @param string $buffer       Parsed template.
     * @param string $templateName Name of the template.
     *
     * @return string
     */
    public function parse(string $buffer, string $templateName): string
    {
        if ($this->modifier->supports($templateName)) {
            $buffer = $this->modifier->parse($buffer, $templateName);
        }

        if ($this->postRenderFilter->supports($templateName)) {
            $buffer = $this->postRenderFilter->filter($buffer, $templateName);
        }

        return $buffer;
    }
}
