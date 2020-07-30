<?php
//inclusão arquivo do fw
require_once 'init.php';
//declaração para utilização da classe Graph
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

//inicio a sessão do php
session_start();

    //chamada a API para autenticar o Token
    $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
    'clientId'                => '67938d06-5ac6-4707-acd2-460030edf946',
    'clientSecret'            => '2bS8Uo2j.lg64S5~Q9bRhCtB74odd_9T6~',
    'redirectUri'             => 'http://localhost/adianti-microsoft-graph/login.php',
    'urlAuthorize'            => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
    'urlAccessToken'          => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
    'urlResourceOwnerDetails' =>  null,
    'scopes'                  => 'openid profile offline_access user.read'
    ]);
    
    if (!isset($_GET['code'])) {
    
    $authorizationUrl = $oauthClient->getAuthorizationUrl();

    $_SESSION['oauth2state'] = $oauthClient->getState();

    header('Location: ' . $authorizationUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {

    if (isset($_SESSION['oauth2state'])) {
        unset($_SESSION['oauth2state']);
    }
    
    exit('Invalid state');

} else {
    
    try {

        //recuperando o token 
        $accessToken = $oauthClient->getAccessToken('authorization_code', [
        'code' => $_GET['code']
        ]);
        
        //acessando o Graph buscando os dados do usuario
        $graph = new Graph();
        $graph->setAccessToken($accessToken);
        
        $user = (array) $graph->createRequest("GET", "/me")
                              ->setReturnType(Model\User::class)
                              ->execute();
        
        foreach ($user as $obj)
          {
            $usuario = $obj['displayName'];
            $email = $obj['userPrincipalName'];
          }
               
        //salva na sessão
        $_SESSION["token"] = $accessToken;
        $_SESSION["email"] = $email;    
        //redireciona
        header ("Location: index.php");         
        
    } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

        // Failed to get the access token or user details.
        exit($e->getMessage());

    }
}