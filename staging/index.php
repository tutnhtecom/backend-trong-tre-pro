<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require((__DIR__) . '/vendor/autoload.php');
require((__DIR__) . '/vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/common/config/bootstrap.php');
require(__DIR__ . '/backend/config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/common/config/main.php'),
    require(__DIR__ . '/common/config/main-local.php'),
    require(__DIR__ . '/backend/config/main.php'),
    require(__DIR__ . '/backend/config/main-local.php')
);
date_default_timezone_set("Asia/Ho_Chi_Minh");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization");
$application = new yii\web\Application($config);
$application->run();
