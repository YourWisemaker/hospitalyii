<?php

require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/vendor/yiisoft/yii2/Yii.php');
$config = require(__DIR__ . '/config/web.php');
new yii\web\Application($config);

$admin = \app\models\User::findByUsername('admin');

if ($admin) {
    // Set password to 'admin'
    $admin->setPassword('admin');
    // Generate new auth key
    $admin->generateAuthKey();
    
    if ($admin->save()) {
        echo "Admin password has been reset to 'admin' successfully!\n";
    } else {
        echo "Failed to reset admin password. Errors:\n";
        print_r($admin->errors);
    }
} else {
    echo "Admin user not found in the database.\n";
}
