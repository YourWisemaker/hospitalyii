<?php

require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/vendor/yiisoft/yii2/Yii.php');
$config = require(__DIR__ . '/config/web.php');
new yii\web\Application($config);

$admin = \app\models\User::findByUsername('admin');

echo "Admin User Information:\n";
echo "----------------------\n";
if ($admin) {
    echo "ID: " . $admin->id . "\n";
    echo "Username: " . $admin->username . "\n";
    echo "Email: " . $admin->email . "\n";
    echo "Status: " . $admin->status . " (" . ($admin->status == 10 ? "ACTIVE" : "INACTIVE") . ")\n";
    echo "Auth Key exists: " . (!empty($admin->auth_key) ? "Yes" : "No") . "\n";
    echo "Password Hash exists: " . (!empty($admin->password_hash) ? "Yes" : "No") . "\n";
    
    // Test password validation
    echo "\nTesting password validation with 'admin': " . 
        ($admin->validatePassword('admin') ? "SUCCESS" : "FAILED") . "\n";
} else {
    echo "Admin user not found in the database.\n";
}
