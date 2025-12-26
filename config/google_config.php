<?php
/**
 * Google OAuth Configuration
 * Cấu hình Google OAuth 2.0 cho GamerWiki
 */

// Google OAuth credentials
// NOTE: Replace these with your actual credentials from Google Cloud Console
define('GOOGLE_CLIENT_ID', '1024113193318-l5u955c2m8b23msv17motopmk7lf4k23.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'http://localhost/GamerWiki/auth/google_callback.php');

/**
 * Get configured Google Client
 * @return Google_Client
 */
function get_google_client() {
    require_once __DIR__ . '/../vendor/autoload.php';
    
    $client = new Google_Client();
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLE_REDIRECT_URI);
    
    // Request user profile information
    $client->addScope('email');
    $client->addScope('profile');
    
    return $client;
}
?>
