<?php

require_once('vendor/autoload.php');

$provider = new TheNetworg\OAuth2\Client\Provider\Azure([
    'clientId'          => '98c91a8f-07a8-4f69-82c7-45e2bcf53c90',
    'clientSecret'      => '~c_4.iGIg6dKq71wHiC.iy3f3.cy_9VQNE',
    'redirectUri'       => 'https://ipc-uat2.api.crm5.dynamics.com/api/data/v9.1/',
    //Optional
    //'scopes'            => 'openid',
    //Optional
    //'defaultEndPointVersion' => '2.0'
]);

// Set to use v2 API, skip the line or set the value to Azure::ENDPOINT_VERSION_1_0 if willing to use v1 API
$provider->defaultEndPointVersion = TheNetworg\OAuth2\Client\Provider\Azure::ENDPOINT_VERSION_2_0;

$baseGraphUri = $provider->getRootMicrosoftGraphUri(null);
$provider->scope = 'openid profile email offline_access ' . $baseGraphUri . '/User.Read';


if (isset($_GET['code']) && isset($_SESSION['OAuth2.state']) && isset($_GET['state'])) {
    if ($_GET['state'] == $_SESSION['OAuth2.state']) {
        unset($_SESSION['OAuth2.state']);

        // Try to get an access token (using the authorization code grant)
        /** @var AccessToken $token */
        $token = $provider->getAccessToken('authorization_code', [
            'scope' => $provider->scope,
            'code' => $_GET['code'],
        ]);

        // Verify token
        // Save it to local server session data
        
        return $token->getToken();
    } else {
        echo 'Invalid state';

        return null;
    }
} else {
    // // Check local server's session data for a token
    // // and verify if still valid 
    // /** @var ?AccessToken $token */
    // $token = // token cached in session data, null if not found;
    //
    // if (isset($token)) {
    //    $me = $provider->get($provider->getRootMicrosoftGraphUri($token) . '/v1.0/me', $token);
    //    $userEmail = $me['mail'];
    //
    //    if ($token->hasExpired()) {
    //        if (!is_null($token->getRefreshToken())) {
    //            $token = $provider->getAccessToken('refresh_token', [
    //                'scope' => $provider->scope,
    //                'refresh_token' => $token->getRefreshToken()
    //            ]);
    //        } else {
    //            $token = null;
    //        }
    //    }
    //}
    //
    // If the token is not found in 
    // if (!isset($token)) {
        $authorizationUrl = $provider->getAuthorizationUrl(['scope' => $provider->scope]);

        $_SESSION['OAuth2.state'] = $provider->getState();

        header('Location: ' . $authorizationUrl);

        exit;
    // }

    return $token->getToken();
}