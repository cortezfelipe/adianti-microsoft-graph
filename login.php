<?php
require_once 'init.php';

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

session_start();

$oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
    'clientId'                => '67938d06-5ac6-4707-acd2-460030edf946',
    'clientSecret'            => '2bS8Uo2j.lg64S5~Q9bRhCtB74odd_9T6~',
    'redirectUri'             => 'http://localhost/adianti-microsoft-graph/login.php',
    'urlAuthorize'            => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
    'urlAccessToken'          => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
    'urlResourceOwnerDetails' =>  null,
    'scopes'                  => 'openid profile offline_access user.read'
  ]);

  $authUrl = $oauthClient->getAuthorizationUrl();

  // Save client state so we can validate in callback
  //session(['oauthState' => $oauthClient->getState()]);

 // Make the token request
$accessToken = $oauthClient->getAccessToken('authorization_code', [
    'code' => $_GET['code']
  ]);

        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        $user = (array) $graph->createRequest("GET", "/me")
                      ->setReturnType(Model\User::class)
                      ->execute();
        foreach ($user as $obj) {
            $usuario = $obj['displayName'];
            $email = $obj['userPrincipalName'];

        }

$_SESSION["token"] = $accessToken;
$_SESSION["email"] = $email;    

header ("Location: index.php");         