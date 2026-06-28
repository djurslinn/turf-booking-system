<?php
class Google_Client {
    private $client_id;
    private $client_secret;
    private $redirect_uri;
    private $scopes = [];

    public function setClientId($id){ $this->client_id = $id; }
    public function setClientSecret($secret){ $this->client_secret = $secret; }
    public function setRedirectUri($uri){ $this->redirect_uri = $uri; }
    public function addScope($scope){ $this->scopes[] = $scope; }

    public function createAuthUrl(){
        $scope = implode(' ', $this->scopes);
        $url = "https://accounts.google.com/o/oauth2/v2/auth";
        $url .= "?client_id={$this->client_id}&redirect_uri={$this->redirect_uri}&response_type=code&scope=".urlencode($scope);
        return $url;
    }

    // Dummy token functions
    public function fetchAccessTokenWithAuthCode($code){ return ['access_token'=>'dummy']; }
    public function setAccessToken($token){}
}
