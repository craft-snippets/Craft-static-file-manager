<?php
/**
 * Static file manager plugin for Craft CMS 3.x
 *
 * Static file manager
 *
 * @link      http://craftsnippets.com
 * @copyright Copyright (c) 2019 Piotr Pogorzelski
 */

namespace craftsnippets\staticfilemanager;

use craftsnippets\staticfilemanager\services\StaticFileManagerService as StaticFileManagerServiceService;
use craftsnippets\staticfilemanager\variables\StaticFileManagerVariable;
use craftsnippets\staticfilemanager\twigextensions\StaticFileManagerTwigExtension;
use craftsnippets\staticfilemanager\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Class StaticFileManager
 *
 * @author    Piotr Pogorzelski
 * @package   StaticFileManager
 * @since     1.0.0
 *
 * @property  StaticFileManagerServiceService $staticFileManagerService
 */
class StaticFileManager extends Plugin
{

    public static $plugin;

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // cache bust filter
        Craft::$app->view->registerTwigExtension(new StaticFileManagerTwigExtension());

        // variable
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                $variable = $event->sender;
                $variable->set('staticFileManager', StaticFileManagerVariable::class);
            }
        );

        // inject files into control panel
        StaticFileManagerServiceService::injectCp();


    }


    protected function createSettingsModel()
    {
        return new Settings();
    }
}
