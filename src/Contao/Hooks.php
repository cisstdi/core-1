<?php

/**
 * @package   contao-bootstrap
 * @author    David Molineus <david.molineus@netzmacht.de>
 * @license   LGPL 3+
 * @copyright 2013-2015 netzmacht creative David Molineus
 */

namespace ContaoBootstrap\Core\Contao;

use ContaoBootstrap\Core\Environment;
use ContaoBootstrap\Core\Event\InitializeEnvironmentEvent;
use ContaoBootstrap\Core\Event\InitializeLayoutEvent;
use ContaoBootstrap\Core\Event\ReplaceInsertTagsEvent;
use ContaoBootstrap\Core\Util\AssetsManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Hooks contains hooks being called from Contao.
 *
 * @package ContaoBootstrap\Core\Contao
 */
class Hooks
{
    /**
     * The event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * The bootstrap environment.
     *
     * @var Environment
     */
    protected $environment;

    /**
     * Construct.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __construct()
    {
        $container = \Controller::getContainer();

        $this->eventDispatcher = $container->get('event_dispatcher');
        $this->environment     = $container->get('contao_bootstrap.environment');
    }

    /**
     * Replace insert tags. Dispatches an event an get the result.
     *
     * @param string $tag   Insert tag.
     * @param bool   $cache Insert tag cache.
     *
     * @return string|false
     */
    public function replaceInsertTags($tag, $cache = true)
    {
        $event = new ReplaceInsertTagsEvent($tag, $cache);

        $this->eventDispatcher->dispatch($event::NAME, $event);

        return $event->getHtml() ?: false;
    }

    /**
     * Initialize bootstrap at initialize system hook.
     *
     * @return void
     */
    public function initializeSystem()
    {
        $this->initializeEnvironment();
        $this->selectIconSet();
    }

    /**
     * Initialize bootstrap environment.
     *
     * @return void
     */
    protected function initializeEnvironment()
    {
        $event = new InitializeEnvironmentEvent($this->environment);
        $this->eventDispatcher->dispatch($event::NAME, $event);
    }

    /**
     * Initialize Layout.
     *
     * @param \PageModel   $page   Current page.
     * @param \LayoutModel $layout Page layout.
     *
     * @return void
     */
    public function initializeLayout(\PageModel $page, \LayoutModel $layout)
    {
        $environment = $this->environment;
        $environment->setLayout($layout);
        $environment->setEnabled($layout->layoutType == 'bootstrap');

        $event = new InitializeLayoutEvent($environment, $layout, $page);
        $this->eventDispatcher->dispatch($event::NAME, $event);
    }

    /**
     * Add icon stylesheet to the backend template.
     *
     * @return void
     */
    public function addIconStylesheet()
    {
        if (TL_MODE == 'BE') {
            $active = $this->environment->getConfig()->get('icons.active');
            $css    = $this->environment->getConfig()->get(sprintf('icons.sets.%s.stylesheet', $active));

            AssetsManager::addStylesheets($css, 'bootstrap-icon-set');
        }
    }

    /**
     * Select an icon set.
     *
     * @return void
     */
    protected function selectIconSet()
    {
        $config   = $this->environment->getConfig();
        $iconSet  = $this->environment->getIconSet();
        $active   = $config->get('icons.active');
        $template = $config->get(sprintf('icons.sets.%s.template', $active));
        $path     = $config->get(sprintf('icons.sets.%s.path', $active));

        if ($active && $path && file_exists(TL_ROOT . '/' . $path)) {
            $icons = include TL_ROOT . '/' . $path;
            $iconSet
                ->setIconSetName($active)
                ->setIcons($icons)
                ->setTemplate($template);
        }

        $this->addIconStylesheet();
    }
}
