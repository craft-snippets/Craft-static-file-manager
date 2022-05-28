<?php
/**
 * Static file manager plugin for Craft CMS 3.x
 *
 * Static file manager
 *
 * @link      http://craftsnippets.com
 * @copyright Copyright (c) 2019 Piotr Pogorzelski
 */

namespace craftsnippets\staticfilemanager\twigextensions;

use craftsnippets\staticfilemanager\StaticFileManager;
use craftsnippets\staticfilemanager\services\StaticFileManagerService as StaticFileManagerService;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use Craft;

/**
 * @author    Piotr Pogorzelski
 * @package   StaticFileManager
 * @since     1.0.0
 */
class StaticFileManagerTwigExtension extends AbstractExtension 
{


    public function getFilters()
    {
        $filters = [];
        $filters[] = new TwigFilter('version', [$this, 'bustCache']);
        $filters[] = new TwigFilter('bustCache', [$this, 'bustCache']);
        return $filters;
    }


    public function bustCache($path)
    {
        return StaticFileManagerService::getFileUrl($path);
    }
}
