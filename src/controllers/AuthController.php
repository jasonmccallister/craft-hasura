<?php

namespace jasonmccallister\hasura\controllers;

use Craft;
use craft\web\Controller;
use jasonmccallister\hasura\Encoder;

class AuthController extends Controller
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
            $this->enableCsrfValidation = \jasonmccallister\hasura\Hasura::$plugin->getSettings()->requireCsrfToken;
        }

        if (Craft::$app->getRequest()->isOptions) {
            Craft::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Origin', '*');
            Craft::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Headers', "X-Requested-With, Authorization, Content-Type, Request-Method");
            Craft::$app->end();
        }

        return parent::beforeAction($action);
    }

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/hasura/auth
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->requirePostRequest();

        $loginName = Craft::$app->getRequest()->getRequiredBodyParam('loginName');
        $password = Craft::$app->getRequest()->getRequiredBodyParam('password');
        $user = Craft::$app->getUsers()->getUserByUsernameOrEmail($loginName);

        if (!$user || !$user->authenticate($password)) {
            return $this->asErrorJson('Unable to authenticate the user');
        }

        $generalConfig = Craft::$app->getConfig()->getGeneral();

        $token = Encoder::encode($user, $generalConfig->userSessionDuration);

        return $this->asJson(['token' => $token]);
    }
}
