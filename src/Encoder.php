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
     * The default ecryption key
     *
     * @var string
     */
    protected static $privateKey = <<<EOD
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
        $roles = $user->groups;
        if (!$roles) {
            $roles = ['user'];
        }

        if ($user->admin) {
            $defaultRole = 'admin';
        } else {
            $defaultRole = 'user';
        }

        $iat = time();
        $exp = $iat + $duration;

        $claimsNamespace = 'https://hasura.io/jwt/claims';

        $token = [
            'sub' => $user->uid,
            'admin' => $user->admin ?? false,
            'iat' => $iat,
            'exp' => $exp,
            $claimsNamespace => [
                'x-hasura-allowed-roles' => $roles,
                'x-hasura-default-role' => $defaultRole,
                'x-hasura-user-id' => $user->uid,
            ]
        ];

        return JWT::encode($token, self::$privateKey, 'RS256');
    }
}
