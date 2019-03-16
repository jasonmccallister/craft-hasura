<?php
/**
 * Hasura plugin for Craft CMS 3.x
 *
 * Use your Craft CMS credentials to authenticate with a GraphQL powered by Hasura
 *
 * @link      https://mccallister.io
 * @copyright Copyright (c) 2019 Jason McCallister
 */

namespace jasonmccallister\hasura\controllers;

use Craft;
use Firebase\JWT\JWT;
use craft\web\Controller;
use jasonmccallister\hasura\Hasura;

/**
 * Auth Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Jason McCallister
 * @package   Hasura
 * @since     1.0.0
 */
class AuthController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/hasura/auth
     *
     * @return mixed
     */
    public function actionIndex()
    {
        // make sure its post
        if (!Craft::$app->getRequest()->getIsPost()) {
            return null;
        }

        $loginName = Craft::$app->getRequest()->getRequiredBodyParam('loginName');
        $password = Craft::$app->getRequest()->getRequiredBodyParam('password');

        $user = Craft::$app->getUsers()->getUserByUsernameOrEmail($loginName);

        if (!$user->authenticate($password)) {
            return null;
        }

        if (!Craft::$app->getUser()->login($user, 1)) {
            return null;
        }

        $privateKey = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQC8kGa1pSjbSYZVebtTRBLxBz5H4i2p/llLCrEeQhta5kaQu/Rn
vuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t0tyazyZ8JXw+KgXTxldMPEL9
5+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4ehde/zUxo6UvS7UrBQIDAQAB
AoGAb/MXV46XxCFRxNuB8LyAtmLDgi/xRnTAlMHjSACddwkyKem8//8eZtw9fzxz
bWZ/1/doQOuHBGYZU8aDzzj59FZ78dyzNFoF91hbvZKkg+6wGyd/LrGVEB+Xre0J
Nil0GReM2AHDNZUYRv+HYJPIOrB0CRczLQsgFJ8K6aAD6F0CQQDzbpjYdx10qgK1
cP59UHiHjPZYC0loEsk7s+hUmT3QHerAQJMZWC11Qrn2N+ybwwNblDKv+s5qgMQ5
5tNoQ9IfAkEAxkyffU6ythpg/H0Ixe1I2rd0GbF05biIzO/i77Det3n4YsJVlDck
ZkcvY3SK2iRIL4c9yY6hlIhs+K9wXTtGWwJBAO9Dskl48mO7woPR9uD22jDpNSwe
k90OMepTjzSvlhjbfuPN1IdhqvSJTDychRwn1kIJ7LQZgQ8fVz9OCFZ/6qMCQGOb
qaGwHmUK6xzpUbbacnYrIM6nLSkXgOAwv7XXCojvY614ILTK3iXiLBOxPu5Eu13k
eUz9sHyD6vkgZzjtxXECQAkp4Xerf5TGfQXGXhxIX52yH+N2LtujCdkQZjXAsGdm
B2zNzvrlgRmgBrklMTrMYgm1NPcW+bRLGcwgW2PTvNM=
-----END RSA PRIVATE KEY-----
EOD;

        $token = [
            'sub' => '$user->id', // TODO make this pull the users id
            'admin' => false, // TODO make this use the actual function to check if user is an admin
            'iat' => 1356999524, // TODO make this get the current time
            'https://hasura.io/jwt/claims' => [ // TODO make this a configuration from hasura config `claims_namespace`
                'x-hasura-allowed-roles' => ['editor', 'user', 'mod'], // TODO make this grab the kabob version of the users groups
                'x-hasura-default-role' => 'user', // TODO grab the users first group? Maybe make this a configuration option to select the default group?
                'x-hasura-user-id' => '$user->id', // TODO grab the users ID
                'x-hasura-org-id' => '123' // TODO support additional claims, possibly by grabbing the attributes on the users fields?
            ]
        ];

        return JWT::encode($token, $privateKey, 'RS256');
    }
}
