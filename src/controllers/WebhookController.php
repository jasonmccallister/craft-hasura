<?php
/**
 * Hasura plugin for Craft CMS 3.x
 *
 * Use your Craft CMS credentials to authenticate with a GraphQL API powered by Hasura.io
 *
 * @link      https://mccallister.io
 * @copyright Copyright (c) 2019 Jason McCallister
 */

namespace jasonmccallister\Hasura\controllers;

use Craft;
use craft\web\Controller;
use jasonmccallister\Hasura\events\HasuraEvent;

/**
 * Webhook Controller
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
 * @since     1.1.0
 */
class WebhookController extends Controller
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
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        // Don't enable CSRF validation for auth requests
        if ($action->id === 'index') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/hasura/webhook
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        $settings = \jasonmccallister\hasura\Hasura::$plugin->getSettings();

        if ($request->getHeaders()->get('x-api-key') !== $settings->webhookKey) {
            Craft::$app->getResponse()->setStatusCode(400);
            return $this->asErrorJson('Unable to authenticate with the webhook');
        }

        $payload = json_decode($request->rawBody, true);

        $this->trigger('hasuraEventTrigger', new HasuraEvent([
            'trigger' => $payload['trigger']['name'],
            'table' => $payload['table']['name'],
            'payload' => $payload,
        ]));

        return $this->asJson(['success' => 'event trigger ' . $payload['id'] . ' received']);
    }
}
