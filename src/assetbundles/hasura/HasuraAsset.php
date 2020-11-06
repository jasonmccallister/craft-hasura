<?php
/**
 * Hasura plugin for Craft CMS 3.x
 *
 * Use your Craft CMS credentials to authenticate with a GraphQL API powered by Hasura.io
 *
 * @link      https://mccallister.io
 * @copyright Copyright (c) 2019 Jason McCallister
 */

namespace jasonmccallister\Hasura\assetbundles\Hasura;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Jason McCallister
 * @package   Hasura
 * @since     1.0.0
 */
class HasuraAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = "@jasonmccallister/hasura/assetbundles/hasura/dist";

        // define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        $this->js = [
            'js/Hasura.js',
        ];

        $this->css = [
            'css/Hasura.css',
        ];

        parent::init();
    }
}
