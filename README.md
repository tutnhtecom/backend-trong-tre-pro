# Trông trẻ Pro

## Bắt đầu

Dự án Trông trẻ Pro, được sử dụng bộ core yii2

## Clone mã nguồn từ github

- [ ] [link github](https://github.com/hoanghuy22233/backend_trongtre.git) 
- [ ] [link vendor](https://drive.google.com/file/d/1BKkh8DjHMxGemDNh6tiy90MDkwiHF4Mh/view?usp=sharing)
- [ ] [link db](https://drive.google.com/file/d/16IB6JaA7EUIXZIygwp7ZkTlTfMRfpwsp/view?usp=sharing)

## Cấu hình file  index.php

Trỏ đường dẫn từ file index.php đến đúng đường dẫn của file vendor

```
require(dirname(__DIR__) . '/vendor/autoload.php');
require(dirname(__DIR__) . '/vendor/yiisoft/yii2/Yii.php');
```

## Cấu hình common/main.php

Trỏ đường dẫn từ file common/main.php đến đúng đường dẫn của file vendor

```
'vendorPath' => (dirname(dirname(__DIR__))) . '/vendor',
```

## Cấu hình database (common/main-local.php)

```
'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=trongtre_app',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'tablePrefix' => 'trong_tre_'
        ],
```
