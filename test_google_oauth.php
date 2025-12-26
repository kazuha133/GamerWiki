<?php
/**
 * Test script to verify Google OAuth implementation
 * This script checks if all required components are in place
 */

echo "=== Google OAuth 2.0 Implementation Test ===\n\n";

$errors = [];
$warnings = [];
$success = [];

// Test 1: Check if composer.json exists
echo "1. Checking composer.json... ";
if (file_exists(__DIR__ . '/composer.json')) {
    $composer_data = json_decode(file_get_contents(__DIR__ . '/composer.json'), true);
    if (isset($composer_data['require']['google/apiclient'])) {
        echo "✅ PASS\n";
        $success[] = "composer.json exists with google/apiclient dependency";
    } else {
        echo "❌ FAIL\n";
        $errors[] = "google/apiclient not found in composer.json";
    }
} else {
    echo "❌ FAIL\n";
    $errors[] = "composer.json not found";
}

// Test 2: Check if google_config.php exists
echo "2. Checking config/google_config.php... ";
if (file_exists(__DIR__ . '/config/google_config.php')) {
    echo "✅ PASS\n";
    $success[] = "google_config.php exists";
    
    // Check if function exists
    require_once __DIR__ . '/config/google_config.php';
    if (function_exists('get_google_client')) {
        echo "   → get_google_client() function found ✓\n";
    } else {
        $errors[] = "get_google_client() function not found";
    }
    
    // Check constants
    if (defined('GOOGLE_CLIENT_ID')) {
        echo "   → GOOGLE_CLIENT_ID defined ✓\n";
    } else {
        $errors[] = "GOOGLE_CLIENT_ID not defined";
    }
    
    if (defined('GOOGLE_CLIENT_SECRET')) {
        echo "   → GOOGLE_CLIENT_SECRET defined ✓\n";
    } else {
        $errors[] = "GOOGLE_CLIENT_SECRET not defined";
    }
    
    if (defined('GOOGLE_REDIRECT_URI')) {
        echo "   → GOOGLE_REDIRECT_URI defined ✓\n";
    } else {
        $errors[] = "GOOGLE_REDIRECT_URI not defined";
    }
} else {
    echo "❌ FAIL\n";
    $errors[] = "config/google_config.php not found";
}

// Test 3: Check if login.php has been updated
echo "3. Checking auth/login.php... ";
if (file_exists(__DIR__ . '/auth/login.php')) {
    $login_content = file_get_contents(__DIR__ . '/auth/login.php');
    
    $checks = [
        'google_config.php require' => strpos($login_content, 'google_config.php') !== false,
        'Google login button' => strpos($login_content, 'bi-google') !== false,
        'Debug mode disabled' => strpos($login_content, '$debug_mode = false') !== false,
        'Divider (hoặc)' => strpos($login_content, 'hoặc') !== false,
    ];
    
    $all_passed = true;
    foreach ($checks as $check => $passed) {
        if (!$passed) {
            $errors[] = "login.php missing: $check";
            $all_passed = false;
        }
    }
    
    if ($all_passed) {
        echo "✅ PASS\n";
        $success[] = "login.php has all required updates";
    } else {
        echo "❌ FAIL\n";
    }
} else {
    echo "❌ FAIL\n";
    $errors[] = "auth/login.php not found";
}

// Test 4: Check if google_callback.php exists
echo "4. Checking auth/google_callback.php... ";
if (file_exists(__DIR__ . '/auth/google_callback.php')) {
    $callback_content = file_get_contents(__DIR__ . '/auth/google_callback.php');
    
    $checks = [
        'Authorization code check' => strpos($callback_content, '$_GET[\'code\']') !== false,
        'Token fetching' => strpos($callback_content, 'fetchAccessTokenWithAuthCode') !== false,
        'User info retrieval' => strpos($callback_content, 'Google_Service_Oauth2') !== false,
        'Prepared statements' => strpos($callback_content, 'prepare(') !== false,
        'Session regeneration' => strpos($callback_content, 'session_regenerate_id') !== false,
        'Username collision handling' => strpos($callback_content, 'max_attempts') !== false,
    ];
    
    $all_passed = true;
    foreach ($checks as $check => $passed) {
        if (!$passed) {
            $errors[] = "google_callback.php missing: $check";
            $all_passed = false;
        }
    }
    
    if ($all_passed) {
        echo "✅ PASS\n";
        $success[] = "google_callback.php has all required features";
    } else {
        echo "❌ FAIL\n";
    }
} else {
    echo "❌ FAIL\n";
    $errors[] = "auth/google_callback.php not found";
}

// Test 5: Check if database migration exists
echo "5. Checking database/add_google_id.sql... ";
if (file_exists(__DIR__ . '/database/add_google_id.sql')) {
    $sql_content = file_get_contents(__DIR__ . '/database/add_google_id.sql');
    
    $checks = [
        'google_id column' => strpos($sql_content, 'ADD COLUMN google_id') !== false,
        'google_id index' => strpos($sql_content, 'idx_google_id') !== false,
        'email unique constraint' => strpos($sql_content, 'idx_email') !== false,
    ];
    
    $all_passed = true;
    foreach ($checks as $check => $passed) {
        if (!$passed) {
            $errors[] = "add_google_id.sql missing: $check";
            $all_passed = false;
        }
    }
    
    if ($all_passed) {
        echo "✅ PASS\n";
        $success[] = "Database migration SQL is complete";
    } else {
        echo "❌ FAIL\n";
    }
} else {
    echo "❌ FAIL\n";
    $errors[] = "database/add_google_id.sql not found";
}

// Test 6: Check if documentation exists
echo "6. Checking docs/GOOGLE_LOGIN_SETUP.md... ";
if (file_exists(__DIR__ . '/docs/GOOGLE_LOGIN_SETUP.md')) {
    echo "✅ PASS\n";
    $success[] = "Setup documentation exists";
} else {
    echo "❌ FAIL\n";
    $errors[] = "docs/GOOGLE_LOGIN_SETUP.md not found";
}

// Test 7: Check if vendor/autoload.php exists
echo "7. Checking if Composer dependencies installed... ";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "✅ PASS\n";
    $success[] = "Composer dependencies are installed";
} else {
    echo "⚠️  WARNING\n";
    $warnings[] = "Composer dependencies not installed yet. Run 'composer install' to install them.";
}

// Summary
echo "\n=== SUMMARY ===\n";
echo "✅ Success: " . count($success) . "\n";
echo "⚠️  Warnings: " . count($warnings) . "\n";
echo "❌ Errors: " . count($errors) . "\n\n";

if (count($errors) > 0) {
    echo "ERRORS:\n";
    foreach ($errors as $error) {
        echo "  ❌ $error\n";
    }
    echo "\n";
}

if (count($warnings) > 0) {
    echo "WARNINGS:\n";
    foreach ($warnings as $warning) {
        echo "  ⚠️  $warning\n";
    }
    echo "\n";
}

if (count($errors) === 0 && count($warnings) <= 1) {
    echo "🎉 All tests passed! Google OAuth implementation is complete.\n";
    echo "\nNext steps:\n";
    echo "1. Run 'composer install' to install Google API Client\n";
    echo "2. Run the database migration (database/add_google_id.sql)\n";
    echo "3. Configure Google OAuth credentials in config/google_config.php\n";
    echo "4. Follow setup guide in docs/GOOGLE_LOGIN_SETUP.md\n";
} else {
    echo "❌ Please fix the errors above before proceeding.\n";
}
?>
