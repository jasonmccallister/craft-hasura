<?php
/**
 * Hasura plugin for Craft CMS 3.x
 *
 * Use your Craft CMS credentials to authenticate with a GraphQL API powered by Hasura.io
 *
 * @link      https://mccallister.io
 * @copyright Copyright (c) 2019 Jason McCallister
 */

namespace jasonmccallister\hasura\models;

use jasonmccallister\hasura\Hasura;

use Craft;
use craft\base\Model;

/**
 * @author    Jason McCallister
 * @package   Hasura
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * default role model attribute
     *
     * @var string
     */
    public $defaultRole = 'user';

    /**
     * claims namespace model attribute
     *
     * @var string
     */
    public $claimsNamespace = 'https://hasura.io/jwt/claims';

    /**
     * require CSRF token model attribute
     *
     * @var boolean
     */
    public $requireCsrfToken = false;

    /**
     * signing method used for JWT model attribute
     *
     * @var string
     */
    public $signingMethod = 'HS256';

    /**
     * signing key used for JWT model attribute
     *
     * @var string
     */
    public $signingKey = null;

    /**
     * The key used to allow posts to the webhook endpoint
     *
     * @var string
     */
    public $webhookKey = null;
}
