<?php

namespace jasonmccallister\hasura;

use jasonmccallister\hasura\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;
use Twig\Error\LoaderError as TwigLoaderError;
use Twig\Error\RuntimeError as TwigRuntimeError;
use Twig\Error\SyntaxError as TwigSyntaxError;
use yii\base\Event;
use yii\base\Exception;

class Hasura extends Plugin
{
    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Hasura::$plugin
     *
     * @var Hasura
     */
    public static $plugin;

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.1.0';

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * Hasura::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();

        self::$plugin = $this;

        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['hasura/auth'] = 'hasura/auth';
                $event->rules['hasura/webhook'] = 'hasura/webhook';
            }
        );

        Craft::info(
            Craft::t(
                'hasura',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     * @throws
     * @throws TwigLoaderError|TwigRuntimeError|TwigSyntaxError|Exception
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'hasura/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
