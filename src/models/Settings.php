<?php

namespace jasonmccallister\hasura\models;

use craft\base\Model;

class Settings extends Model
{
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
     * Twig string to be parsed on include
     *
     * @var string
     */
    public $fieldTwig = '';

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
     * The header that will contain the webhook key
     *
     * @var string
     */
    public $webhookHeader = 'x-api-key';

    /**
     * The key used to allow posts to the webhook endpoint
     *
     * @var string
     */
    public $webhookKey = null;
}
