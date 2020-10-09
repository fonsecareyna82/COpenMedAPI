<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
             'dsn' => 'mysql:host=216.227.216.46;dbname=audiria0_copenmed',
            'username' => 'audiria0',
            'password' => 'psAhhupClpwd2',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
