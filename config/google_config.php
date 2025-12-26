<?php
/**
 * Google OAuth Configuration
 * Cấu hình Google OAuth 2.0 cho GamerWiki
 */

// Google OAuth credentials
// NOTE: Replace these with your actual credentials from Google Cloud Console
define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID_HERE');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET_HERE');
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
