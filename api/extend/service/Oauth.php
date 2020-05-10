<?php

namespace service;

use InvalidArgumentException;
use OAuth2\OpenID\Storage\AuthorizationCodeInterface as OpenIDAuthorizationCodeInterface;
use OAuth2\OpenID\Storage\UserClaimsInterface;
use OAuth2\Storage\AccessTokenInterface;
use OAuth2\Storage\AuthorizationCodeInterface;
use OAuth2\Storage\ClientCredentialsInterface;
use OAuth2\Storage\JwtBearerInterface;
use OAuth2\Storage\PublicKeyInterface;
use OAuth2\Storage\RefreshTokenInterface;
use OAuth2\Storage\ScopeInterface;
use OAuth2\Storage\UserCredentialsInterface;
use think\Db;

/**
 * Simple PDO storage for all storage types
 *
 * NOTE: This class is meant to get users started
 * quickly. If your application requires further
 * customization, extend this class or create your own.
 *
 * NOTE: Passwords are stored in plaintext, which is never
 * a good idea.  Be sure to override this for your application
 *
 * @author Brent Shaffer <bshafs at gmail dot com>
 */
class Oauth implements
    AuthorizationCodeInterface,
    AccessTokenInterface,
    ClientCredentialsInterface,
    UserCredentialsInterface,
    RefreshTokenInterface,
    JwtBearerInterface,
    ScopeInterface,
    PublicKeyInterface,
    UserClaimsInterface,
    OpenIDAuthorizationCodeInterface
{
    /**
     * @var array
     */
    protected $config;

    /*
     *
     */
    protected $claim;

    /**
     * @param mixed $connection
     * @param array $config
     *
     * @throws InvalidArgumentException
     */
    public function __construct($config = [])
    {
        $this->config = array_merge([
            'client_table' => 'oauth_client',
            'access_token_table' => 'oauth_access_token',
            'refresh_token_table' => 'oauth_refresh_token',
            'code_table' => 'oauth_authorization_code',
            'user_table' => 'oauth_user',
            'jwt_table' => 'oauth_jwt',
            'jti_table' => 'oauth_jti',
            'scope_table' => 'oauth_scope',
            'public_key_table' => 'oauth_public_key',
        ], $config);
    }

    public function D($table)
    {
        return Db::table($this->config[$table . '_table']);
    }

    public function getUserByToken($access_token){
        $value = $this->D('access_token')->where('access_token', $access_token)->value('user_id');
        if($value){
            return $this->D('user')->where('id', $value)->find();
        }
        return [];
    }
    /**
     * @param string $client_id
     * @param null|string $client_secret
     * @return bool
     */
    public function checkClientCredentials($client_id, $client_secret = null)
    {
        $value = $this->D('client')->where('client_id', $client_id)->value('client_secret');

        // make this extensible
        return $value == $client_secret;
    }

    /**
     * @param string $client_id
     * @return bool
     */
    public function isPublicClient($client_id)
    {
        $client_secret = $this->D('client')->where('client_id', $client_id)->value('client_secret');

        return empty($client_secret);
    }

    /**
     * @param string $client_id
     * @return array|mixed
     */
    public function getClientDetails($client_id)
    {
        return $this->D('client')->where('client_id', $client_id)->find();
    }

    /**
     * @param string $client_id
     * @param null|string $client_secret
     * @param null|string $redirect_uri
     * @param null|array $grant_types
     * @param null|string $scope
     * @param null|string $user_id
     * @return bool
     */
    public function setClientDetails($client_id, $client_secret = null, $redirect_uri = null, $grant_types = null, $scope = null, $user_id = null)
    {
        // if it exists, update it.
        if ($this->getClientDetails($client_id)) {
            return $this->D('client')->where('client_id', $client_id)->update(['client_secret' => $client_secret, 'redirect_uri' => $redirect_uri, 'grant_types' => $grant_types, 'scope' => $scope, 'user_id' => $user_id]);
        } else {
            return $this->D('')->insert(['client_secret' => $client_secret, 'redirect_uri' => $redirect_uri, 'grant_types' => $grant_types, 'scope' => $scope, 'user_id' => $user_id]);
        }
    }

    /**
     * @param $client_id
     * @param $grant_type
     * @return bool
     */
    public function checkRestrictedGrantType($client_id, $grant_type)
    {
        $details = $this->getClientDetails($client_id);
        if (isset($details['grant_types'])) {
            $grant_types = explode(' ', $details['grant_types']);

            return in_array($grant_type, (array) $grant_types);
        }

        // if grant_types are not defined, then none are restricted
        return true;
    }

    /**
     * @param string $access_token
     * @return array|bool|mixed|null
     */
    public function getAccessToken($access_token)
    {
        $token = $this->D('access_token')->where('access_token', $access_token)->find();

        if ($token) {
            // convert date string back to timestamp
            $token['expires'] = strtotime($token['expires']);
        }

        return $token;
    }

    /**
     * @param string $access_token
     * @param mixed $client_id
     * @param mixed $user_id
     * @param int $expires
     * @param string $scope
     * @return bool
     */
    public function setAccessToken($access_token, $client_id, $user_id, $expires, $scope = null)
    {
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        // if it exists, update it.
        if ($this->getAccessToken($access_token)) {
            return $this->D('access_token')->where('access_token', $access_token)->update(['client_id' => $client_id, 'expires' => $expires, 'user_id' => $user_id, 'scope' => $scope]);
        } else {
            return $this->D('access_token')->insert(['client_id' => $client_id, 'expires' => $expires, 'user_id' => $user_id, 'scope' => $scope, 'access_token' => $access_token]);
        }
    }

    /**
     * @param $access_token
     * @return bool
     */
    public function unsetAccessToken($access_token)
    {
        $count = $this->D('access_token')->where(['access_token' => $access_token])->count(1);

        return $count > 0;
    }

    /* OAuth2\Storage\AuthorizationCodeInterface */
    /**
     * @param string $code
     * @return mixed
     */
    public function getAuthorizationCode($code)
    {
        $result = $this->D('code')->where(['authorization_code' => $code])->find();
        if ($result) {
            // convert date string back to timestamp
            $result['expires'] = strtotime($result['expires']);
        }

        return $result;
    }

    /**
     * @param string $code
     * @param mixed $client_id
     * @param mixed $user_id
     * @param string $redirect_uri
     * @param int $expires
     * @param string $scope
     * @param string $id_token
     * @return bool|mixed
     */
    public function setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null, $id_token = null)
    {
        if (func_num_args() > 6) {
            // we are calling with an id token
            return call_user_func_array(array($this, 'setAuthorizationCodeWithIdToken'), func_get_args());
        }

        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        // if it exists, update it.
        if ($this->getAuthorizationCode($code)) {
            return $this->D('code')->where(['authorization_code' => $code])->update(['client_id' => $client_id, 'user_id' => $user_id, 'redirect_uri' => $redirect_uri, 'expires' => $expires, 'scope' => $scope]);
        } else {
            return $this->D('code')->insert(['authorization_code' => $code])->update(['client_id' => $client_id, 'user_id' => $user_id, 'redirect_uri' => $redirect_uri, 'expires' => $expires, 'scope' => $scope]);
        }
    }

    /**
     * @param string $code
     * @param mixed $client_id
     * @param mixed $user_id
     * @param string $redirect_uri
     * @param string $expires
     * @param string $scope
     * @param string $id_token
     * @return bool
     */
    private function setAuthorizationCodeWithIdToken($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null, $id_token = null)
    {
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        // if it exists, update it.
        if ($this->getAuthorizationCode($code)) {
            return $this->D('code')->where('authorization_code', $code)->update(['client_id' => $client_id, 'user_id' => $user_id, 'redirect_uri' => $redirect_uri, 'expires' => $expires, 'scope' => $scope, 'id_token ' => $id_token]);
        } else {
            return $this->D('code')->insert(['client_id' => $client_id, 'user_id' => $user_id, 'redirect_uri' => $redirect_uri, 'expires' => $expires, 'scope' => $scope, 'id_token ' => $id_token]);
        }
    }

    /**
     * @param string $code
     * @return bool
     */
    public function expireAuthorizationCode($code)
    {
        return $this->D('code')->where('authorization_code', $code)->delete();
    }

    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function checkUserCredentials($username, $password)
    {
        if ($user = $this->getUser($username)) {
            return $this->checkPassword($user, $password);
        }

        return false;
    }

    /**
     * @param string $username
     * @return array|bool
     */
    public function getUserDetails($username)
    {
        return $this->getUser($username);
    }

    /**
     * @param mixed $user_id
     * @param string $claims
     * @return array|bool
     */
    public function getUserClaims($user_id, $claims)
    {
        if (!$userDetails = $this->getUserDetails($user_id)) {
            return false;
        }

        $claims = explode(' ', trim($claims));
        $userClaims = array();

        // for each requested claim, if the user has the claim, set it in the response
        $validClaims = explode(' ', self::VALID_CLAIMS);
        foreach ($validClaims as $validClaim) {
            if (in_array($validClaim, $claims)) {
                if ($validClaim == 'address') {
                    // address is an object with subfields
                    $userClaims['address'] = $this->getUserClaim($validClaim, $userDetails['address'] ?: $userDetails);
                } else {
                    $userClaims = array_merge($userClaims, $this->getUserClaim($validClaim, $userDetails));
                }
            }
        }

        return $userClaims;
    }

    /**
     * @param string $claim
     * @param array $userDetails
     * @return array
     */
    protected function getUserClaim($claim, $userDetails)
    {
        $userClaims = array();
        $claimValuesString = constant(sprintf('self::%s_CLAIM_VALUES', strtoupper($claim)));
        $claimValues = explode(' ', $claimValuesString);

        foreach ($claimValues as $value) {
            $userClaims[$value] = isset($userDetails[$value]) ? $userDetails[$value] : null;
        }

        return $userClaims;
    }

    /**
     * @param string $refresh_token
     * @return bool|mixed
     */
    public function getRefreshToken($refresh_token)
    {
        $result = $this->D('refresh_token')->where('refresh_token', $refresh_token)->find();

        if ($result) {
            // convert expires to epoch time
            $result['expires'] = strtotime($result['expires']);
        }

        return $result;
    }

    /**
     * @param string $refresh_token
     * @param mixed $client_id
     * @param mixed $user_id
     * @param string $expires
     * @param string $scope
     * @return bool
     */
    public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null)
    {
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        $result = $this->D('refresh_token')->insert(['refresh_token' => $refresh_token, 'client_id' => $client_id, 'user_id' => $user_id, 'expires' => $expires, 'scope' => $scope]);

        return $result;
    }

    /**
     * @param string $refresh_token
     * @return bool
     */
    public function unsetRefreshToken($refresh_token)
    {
        $result = $this->D('refresh_token')->where('refresh_token', $refresh_token)->delete();

        return $result->rowCount() > 0;
    }

    /**
     * plaintext passwords are bad!  Override this for your application
     *
     * @param array $user
     * @param string $password
     * @return bool
     */
    protected function checkPassword($user, $password)
    {
        // return $user['password'] == $this->hashPassword($password);
        return true;
    }

    // use a secure hashing algorithm when storing passwords. Override this for your application
    protected function hashPassword($password)
    {
        return sha1($password);
    }

    /**
     * @param string $username
     * @return array|bool
     */
    public function getUser($username)
    {
        $accountField = strpos('@', $username) ? 'email' : preg_match('/^1[353678]\d{9}$/i', $username) ? 'mobile' : 'username';

        if (!$user = $this->D('user')->where($accountField, $username)->find()) {
            return false;
        }

        // the default behavior is to use "username" as the user_id
        return array_merge([
            'user_id' => $user['id'],
        ], $user);
    }

    /**
     * plaintext passwords are bad!  Override this for your application
     *
     * @param string $username
     * @param string $password
     * @param string $firstName
     * @param string $lastName
     * @return bool
     */
    public function setUser($username, $password, $firstName = null, $lastName = null)
    {
        // do not store in plaintext
        $password = $this->hashPassword($password);

        // if it exists, update it.
        if ($this->getUser($username)) {
            $bool = $this->D('user')->where('username', $username)->update(['password' => $password, 'name' => $firstName . ' ' . $lastName]);
        } else {
            $bool = $this->D('user')->insert(['username' => $username, 'password' => $password, 'name' => $firstName . ' ' . $lastName]);
        }

        return $bool;
    }

    /**
     * @param string $scope
     * @return bool
     */
    public function scopeExists($scope)
    {
        $scope = explode(' ', $scope);
        $whereIn = array_fill(0, count($scope), '?');
        if ($count = $this->D('scope')->whereIn('scope', implode(',', $whereIn))->count()) {
            return $count == count($scope);
        }

        return false;
    }

    /**
     * @param mixed $client_id
     * @return null|string
     */
    public function getDefaultScope($client_id = null)
    {
        $result = $this->D('scope')->where(['is_default' => true, 'client_id' => $client_id])->column('scope');

        return count($result) ? implode(' ', $result) : null;
    }

    /**
     * @param mixed $client_id
     * @param $subject
     * @return string
     */
    public function getClientKey($client_id, $subject)
    {
        return $this->D('jwt')->where(['client_id' => $client_id, 'subject' => $subject])->value('public_key');
    }

    /**
     * @param mixed $client_id
     * @return bool|null
     */
    public function getClientScope($client_id)
    {
        if (!$clientDetails = $this->getClientDetails($client_id)) {
            return false;
        }

        if (isset($clientDetails['scope'])) {
            return $clientDetails['scope'];
        }

        return null;
    }

    /**
     * @param mixed $client_id
     * @param $subject
     * @param $audience
     * @param $expires
     * @param $jti
     * @return array|null
     */
    public function getJti($client_id, $subject, $audience, $expires, $jti)
    {
        if ($result = $this->D('jti')->where(['issuer' => $client_id, 'subject' => $subject, 'audience' => $audience, 'expires' => $expires, 'jti' => $jti])->find()) {
            return array(
                'issuer' => $result['issuer'],
                'subject' => $result['subject'],
                'audience' => $result['audience'],
                'expires' => $result['expires'],
                'jti' => $result['jti'],
            );
        }

        return null;
    }

    /**
     * @param mixed $client_id
     * @param $subject
     * @param $audience
     * @param $expires
     * @param $jti
     * @return bool
     */
    public function setJti($client_id, $subject, $audience, $expires, $jti)
    {
        $result = $this->D('jti')->insert(['issuer' => $client_id, 'subject' => $subject, 'audience' => $audience, 'expires' => $expires, 'jti' => $jti]);

        return $result;
    }

    /**
     * @param mixed $client_id
     * @return mixed
     */
    public function getPublicKey($client_id = null)
    {
        return $this->D('public_key')->whereOr(['client_id' => $client_id, 'client_id' => 0])->value('public_key');
    }

    /**
     * @param mixed $client_id
     * @return mixed
     */
    public function getPrivateKey($client_id = null)
    {
        return $this->D('public_key')->whereOr(['client_id' => $client_id, 'client_id' => 0])->order(['client_id' => 'DESC'])->value('private_key');
    }

    /**
     * @param mixed $client_id
     * @return string
     */
    public function getEncryptionAlgorithm($client_id = null)
    {
        $result = $this->D('public_key')->whereOr(['client_id' => $client_id, 'client_id' => 0])->order(['client_id' => 'desc'])->value('encryption_algorithm');

        return $result || 'RS256';
    }

    /**
     * DDL to create OAuth2 database and tables for PDO storage
     *
     * @see https://github.com/dsquier/oauth2-server-php-mysql
     *
     * @param string $dbName
     * @return string
     */
    public function getBuildSql($dbName = 'oauth2_server_php')
    {
        $sql = "
        CREATE TABLE {$this->config['client_table']} (
          `client_id`             CHAR(30) NOT NULL DEFAULT '' COMMENT '客户端',
          `client_secret`         CHAR(80) NOT NULL DEFAULT '' COMMENT '客户端密钥',
          `redirect_uri`          CHAR(200) NOT NULL DEFAULT '' COMMENT '回调地址',
          `grant_types`           CHAR(80) NOT NULL DEFAULT '' COMMENT '授权类型',
          `scope`                 CHAR(200) NOT NULL DEFAULT '' COMMENT '范围',
          `user_id`               INT(12) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户',
          PRIMARY KEY (client_id),
          INDEX `client_id`(`client_id`) USING BTREE,
          INDEX `user_id`(`user_id`) USING BTREE
        )ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '客户端';

            CREATE TABLE {$this->config['access_token_table']} (
              access_token         CHAR(40) NOT NULL DEFAULT '' COMMENT '访问token',
              client_id            CHAR(30) NOT NULL DEFAULT '' COMMENT '客户端',
              user_id              INT(12) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户',
              expires              TIMESTAMP NOT NULL COMMENT '过期时间',
              scope                CHAR(200) NOT NULL DEFAULT '' COMMENT '范围',
              PRIMARY KEY (access_token),
              INDEX `client_id`(`client_id`) USING BTREE
              INDEX `user_id`(`user_id`) USING BTREE,
              INDEX `expires`(`expires`) USING BTREE
            )ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '访问TOKEN';

            CREATE TABLE {$this->config['code_table']} (
              authorization_code  CHAR(40) NOT NULL DEFAULT '' COMMENT '授权码',
              client_id           CHAR(30) NOT NULL DEFAULT '' COMMENT '客户端',
              user_id             INT(12) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户',
              redirect_uri        CHAR(200) NOT NULL DEFAULT '' COMMENT '回调地址',
              expires             TIMESTAMP NOT NULL COMMENT '过期时间',
              scope               CHAR(200) NOT NULL DEFAULT '' COMMENT '范围',
              id_token            CHAR(200) NOT NULL DEFAULT '' COMMENT '范围',
              PRIMARY KEY (authorization_code),
              INDEX `client_id`(`client_id`) USING BTREE,
              INDEX `user_id`(`user_id`) USING BTREE,
              INDEX `expires`(`expires`) USING BTREE
            )ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '授权码';

            CREATE TABLE {$this->config['refresh_token_table']} (
              refresh_token       CHAR(40) NOT NULL DEFAULT '' COMMENT '刷新token',
              client_id           CHAR(30) NOT NULL DEFAULT '' COMMENT '客户端',
              user_id             INT(12) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户',
              expires             TIMESTAMP NOT NULL COMMENT '过期时间',
              scope               CHAR(200) NOT NULL DEFAULT '' COMMENT '范围',
              PRIMARY KEY (refresh_token),
              INDEX `client_id`(`client_id`) USING BTREE,
              INDEX `user_id`(`user_id`) USING BTREE,
              INDEX `expires`(`expires`) USING BTREE
            )ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '刷新TOKEN';

            CREATE TABLE {$this->config['user_table']} (
              username            CHAR(80) NOT NULL DEFAULT '' COMMENT '用户名',
              password            CHAR(40) NOT NULL DEFAULT '' COMMENT '密码',
              first_name          CHAR(80) NOT NULL DEFAULT '' COMMENT '姓',
              last_name           CHAR(80) NOT NULL DEFAULT '' COMMENT '名',
              email               CHAR(80) NOT NULL DEFAULT '' COMMENT '邮箱',
              email_verified      TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '邮箱是否已验证',
              scope               CHAR(200) NOT NULL DEFAULT '' COMMENT '范围'
            );

            CREATE TABLE {$this->config['scope_table']} (
              scope               CHAR(80) NOT NULL DEFAULT '' COMMENT '范围',
              is_default          TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否默认',
              PRIMARY KEY (scope),
              INDEX `is_default`(`is_default`) USING BTREE
            )ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '使用范围';

            CREATE TABLE {$this->config['jwt_table']} (
              client_id           CHAR(80) NOT NULL DEFAULT '' COMMENT '客户端',
              subject             CHAR(80) NOT NULL DEFAULT '' COMMENT '是否默认',
              public_key          CHAR(200) NOT NULL DEFAULT '' COMMENT '公钥',
              INDEX `client_id`(`client_id`) USING BTREE
            )ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'JWT';

            CREATE TABLE {$this->config['jti_table']} (
              issuer              CHAR(80) NOT NULL DEFAULT '' COMMENT '',
              subject             CHAR(80) NOT NULL DEFAULT '' COMMENT '',
              audiance            CHAR(80) NOT NULL DEFAULT '' COMMENT '是否默认',
              expires             TIMESTAMP NOT NULL,
              jti                 CHAR(200) NOT NULL
            )ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'JTI';

            CREATE TABLE {$this->config['public_key_table']} (
              client_id            CHAR(30) NOT NULL DEFAULT '' COMMENT '客户端',
              public_key           CHAR(200) NOT NULL DEFAULT '' COMMENT '公钥',
              private_key          CHAR(200) NOT NULL DEFAULT '' COMMENT '私钥',
              encryption_algorithm CHAR(100) NOT NULL DEFAULT 'RS256' COMMENT '加密方式',
              INDEX `client_id`(`client_id`) USING BTREE
            )ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '公钥'
        ";

        return $sql;
    }
}
