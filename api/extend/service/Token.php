<?php
namespace service;

use OAuth2\Request;

class Token{
    private $server;
    private $storage;
    private $request;
    private $response;
    public $code;
    public $message;

    public function __construct()
    {
        // $dsn = 'mysql:dbname=' . config('database.database') .';host=' . config('database.hostname');

        // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
        // $this->storage = new \OAuth2\Storage\Pdo(['dsn' => $dsn, 'username' => config('database.username'), 'password' => config('database.password')]);
        $this->storage = new \service\Oauth([]);
        // Pass a storage object or array of storage objects to the OAuth2 server class
        $this->server = new \OAuth2\Server($this->storage);
        // Add the "Client Credentials" grant type (it is the simplest of the grant types)
        $this->server->addGrantType(new \OAuth2\GrantType\ClientCredentials($this->storage));
        // Add the "Authorization Code" grant type (this is where the oauth magic happens)
        $this->server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($this->storage));
        $this->response = new \OAuth2\Response();
        $this->request = Request::createFromGlobals();
    }

    /**
     * 授权
     */
    public function authorize()
    {
        // validate the authorize request
        if (!$this->server->validateAuthorizeRequest($this->request, $this->response)) {
            return false;
        }
        // print the authorization code if the user has authorized your client
        $this->server->handleAuthorizeRequest($this->request, $this->response, input('post.authorized'));
        return true;
        /*
        // this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
        $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
        exit("SUCCESS! Authorization Code: $code");

        $response->send();
        */
    }

    /**
     * 令牌
     */
    public function get(){
        // Handle a request for an OAuth2.0 Access Token and send the response to the client
        $grantType = new \OAuth2\GrantType\UserCredentials($this->storage);
        $this->server->addGrantType($grantType);

        $res = $this->server->grantAccessToken($this->request, $this->response);
        // $this->server->handleTokenRequest($this->request, $this->response)->send();
        // $userInfo =
        if($res){
            // $this->storage->getUserByToken($res['access-token']);
            return $res + $this->storage->getUserByToken($res['access_token']);
        }
        $this->message = $this->response->getParameters()['error'];
        $this->code = $this->response->getStatusCode();
        return false;
    }
    /**
     * 验证
     */
    public function verify()
    {
        // Handle a request to a resource and authenticate the access token
        if (!$this->server->verifyResourceRequest($this->request)) {
            // $this->server->getResponse()->send();
            return false;
        }
        return $this->server->getAccessTokenData($this->request);
    }
}
