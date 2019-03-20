<?php
/**
 * Hasura plugin for Craft CMS 3.x
 *
 * Use your Craft CMS credentials to authenticate with a GraphQL API powered by Hasura.io
 *
 * @link      https://mccallister.io
 * @copyright Copyright (c) 2019 Jason McCallister
 */

namespace jasonmccallister\hasura;

use Firebase\JWT\JWT;
use craft\elements\User;

class Encoder {
    /**
     * Takes a user and returns a signed JWT
     *
     * @param User $user
     * @param int $duration
     *
     * @return string
     */
    public static function encode(User $user, int $duration) : string
    {
        $roles = self::getUserRoles($user);
        $settings = Hasura::$plugin->getSettings();
        $defaultRole = $settings->defaultRole;
        $namespace = $settings->claimsNamespace;

        $default = $user->admin ? 'admin' : $defaultRole;

        $iat = time();
        $exp = $iat + $duration;

        $token = [
            'sub' => $user->uid,
            'admin' => $user->admin ?? false,
            'iat' => $iat,
            'exp' => $exp,
            $namespace => [
                'x-hasura-allowed-roles' => $roles,
                'x-hasura-default-role' => $default,
                'x-hasura-user-id' => $user->uid,
            ]
        ];

        return self::sign($token);
    }

    /**
     * Get all of the users roles by their group handle.
     *
     * @param User $user
     *
     * @return array
     */
    protected static function getUserRoles(User $user): array
    {
        $roles = $user->groups ? array_column(
            $user->groups,
            'handle'
        ) : [Hasura::$plugin->getSettings()->defaultRole];

        if ($user->admin) {
            return array_merge($roles, ['admin']);
        }

        return $roles;
    }

    /**
     * Takes a token and signs it based on the settings.
     *
     * @param array $token
     *
     * @return string
     */
    protected static function sign(array $token): string
    {
        return JWT::encode(
            $token,
            Hasura::$plugin->settings->signingKey,
            Hasura::$plugin->settings->signingMethod
        );
    }
}
