<?php

namespace jasonmccallister\hasura\controllers;

use Craft;
use craft\web\Controller;
use jasonmccallister\hasura\events\HasuraEvent;
use jasonmccallister\hasura\Hasura;
use jasonmccallister\hasura\models\Settings;
use yii\web\BadRequestHttpException;

class WebhookController extends Controller
{
    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index'];

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
     * @throws BadRequestHttpException
     */
    public function actionIndex()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        /** @var Settings $settings */
        $settings = Hasura::$plugin->getSettings();

        if ($request->getHeaders()->get($settings->webhookHeader) !== $settings->webhookKey) {
            Craft::$app->getResponse()->setStatusCode(400);

            return $this->asErrorJson('Unable to authenticate with the webhook');
        }

        $payload = json_decode($request->rawBody, true);

        $this->trigger(
            'hasuraEventTrigger',
            new HasuraEvent(
                [
                    'trigger' => $payload['trigger']['name'],
                    'table' => $payload['table']['name'],
                    'payload' => $payload,
                ]
            )
        );

        return $this->asJson(['success' => 'event trigger ' . $payload['id'] . ' received']);
    }
}
