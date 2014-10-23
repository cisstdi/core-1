<?php

/**
 * @package    contao-bootstrap
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Bootstrap\Core\Contao\Model;

use Netzmacht\Bootstrap\Core\Config\TypeManager;

/**
 * Class BootstrapConfigModel
 * @package Netzmacht\Bootstrap\Contao\Model
 * @property int    id
 * @property string type
 * @property bool   override
 */
class BootstrapConfigModel extends \Model
{
    /**
     * @var string
     */
    protected static $strTable = 'tl_bootstrap_config';

    /**
     * Find all published configurations which belongs to a theme
     *
     * @param int   $themeId
     * @param array $options
     *
     * @return \Model\Collection|null
     */
    public static function findPublishedByTheme($themeId, array $options=array(), $ignoreGlobal=true)
    {
        if (!isset($options['order'])) {
            $options['order'] = 'sorting';
        }

        $column = array('published=?', 'pid=?');
        $value  = array(true, $themeId);

        if ($ignoreGlobal) {
            $manager = static::getTypeManager();
            $types   = $manager->getTypesWithGlobalScope(true);

            if ($types) {
                $ins      = self::createPlaceholders($types);
                $column[] = sprintf('type NOT IN(%s)', $ins);
                $value    = array_merge($value, $types);
            }
        }

        return static::findBy($column, $value, $options);
    }

    /**
     * Find all published configurations which belongs to the global scope
     *
     * @param array $options
     *
     * @return \Model\Collection|null
     */
    public static function findGlobalPublished(array $options=array())
    {
        if (!isset($options['order'])) {
            $options['order'] = 'sorting';
        }

        $manager = static::getTypeManager();
        $types   = $manager->getTypesWithGlobalScope(true);

        // no global types exists
        if (!$types) {
            return null;
        }

        $ins = self::createPlaceholders($types);

        return static::findBy(
            array('published=?', sprintf('type IN(%s)', $ins)),
            array_merge(array(true), $types),
            $options
        );
    }

    /**
     * @return TypeManager
     */
    private static function getTypeManager()
    {
        return $GLOBALS['container']['bootstrap.config-type-manager'];
    }

    /**
     * @param $types
     * @return string
     */
    public static function createPlaceholders($types)
    {
        $ins = str_repeat('? , ', count($types) - 1) . '?';

        return $ins;
    }
} 