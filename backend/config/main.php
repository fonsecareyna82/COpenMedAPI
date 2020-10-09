<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'language' => 'es-ES',
    'components' => [
        /* 'request' => [
             'csrfParam' => '_csrf-backend',
         ], */
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        /*'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            // ...
        ],*/
        /*'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->statusCode !== 200) {
                    $response->data = [
                        'success' => $response->isSuccessful,
                        'data' => $response->statusText,
                    ];
                    $response->statusCode = 200;
                }                
            },
        ],*/
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'register' => 'site/register',
                'authorize' => 'site/authorize',
                'accesstoken' => 'site/accesstoken',
                'me' => 'site/me',
                'logout' => 'site/logout',

                // Funciones Adicionales del SiteController
                'update-user/<id>' => 'site/update-user',
                'view-user/<id>' => 'site/view-user',
                'change-password/<id>' => 'site/change-password',
                'user-name-exist/<username>' => 'site/user-name-exist',

                // Seg_Rol
                'seg-rol' => 'seg-rol/index',
                'seg-rol/view/<id>' => 'seg-rol/view',
                'seg-rol/create' => 'seg-rol/create',
                'seg-rol/update/<id>' => 'seg-rol/update',
                'seg-rol/delete/<id>' => 'seg-rol/delete',

                // Adm_Persona
                'adm-persona' => 'adm-persona/index',
                'adm-persona/view/<id>' => 'adm-persona/view',
                'adm-persona/create' => 'adm-persona/create',
                'adm-persona/update/<id>' => 'adm-persona/update',
                'adm-persona/delete/<id>' => 'adm-persona/delete',

                // Doc_Especialidad
                'doc-especialidad' => 'doc-especialidad/index',
                'doc-especialidad/view/<id>' => 'doc-especialidad/view',
                'doc-especialidad/create' => 'doc-especialidad/create',
                'doc-especialidad/update/<id>' => 'doc-especialidad/update',
                'doc-especialidad/delete/<id>' => 'doc-especialidad/delete',

                // Doc_Estudiante
                'doc-estudiante' => 'doc-estudiante/index',
                'doc-estudiante/view/<id>' => 'doc-estudiante/view',
                'doc-estudiante/create' => 'doc-estudiante/create',
                'doc-estudiante/update/<id>' => 'doc-estudiante/update',
                'doc-estudiante/delete/<id>' => 'doc-estudiante/delete',

                // Doc_Grupo
                'doc-grupo' => 'doc-grupo/index',
                'doc-grupo/view/<id>' => 'doc-grupo/view',
                'doc-grupo/create' => 'doc-grupo/create',
                'doc-grupo/update/<id>' => 'doc-grupo/update',
                'doc-grupo/delete/<id>' => 'doc-grupo/delete',

                // Doc_Profesor
                'doc-profesor' => 'doc-profesor/index',
                'doc-profesor/view/<id>' => 'doc-profesor/view',
                'doc-profesor/create' => 'doc-profesor/create',
                'doc-profesor/update/<id>' => 'doc-profesor/update',
                'doc-profesor/delete/<id>' => 'doc-profesor/delete',

                'doc-profesor/especialidades/<id>' => 'doc-profesor/especialidades',
                'doc-profesor/unsolicited-especialidades/<id>' => 'doc-profesor/unsolicited-especialidades',
                'doc-profesor/create-especialidad' => 'doc-profesor/create-especialidad',
                'doc-profesor/delete-especialidad/<idprofesor:\d+>/<idespecialidad:\d+>' => 'doc-profesor/delete-especialidad',

                'doc-profesor/grupos/<id>' => 'doc-profesor/grupos',
                'doc-profesor/unsolicited-groups/<id>' => 'doc-profesor/unsolicited-groups',
                'doc-profesor/create-grupo' => 'doc-profesor/create-grupo',
                'doc-profesor/delete-grupo/<idprofesor:\d+>/<idgrupo:\d+>' => 'doc-profesor/delete-grupo',

                // Idioma
                'idioma' => 'idioma/index',
                'idioma/view/<id>' => 'idioma/view',
                'idioma/create' => 'idioma/create',
                'idioma/update/<id>' => 'idioma/update',
                'idioma/delete/<id>' => 'idioma/delete',

                // Tipo_Entidad
                'tipo-entidad' => 'tipo-entidad/index',
                'tipo-entidad/view/<id>' => 'tipo-entidad/view',
                'tipo-entidad/create' => 'tipo-entidad/create',
                'tipo-entidad/update/<id>' => 'tipo-entidad/update',
                'tipo-entidad/delete/<id>' => 'tipo-entidad/delete',

                // Entidad
                'entidad' => 'entidad/index',
                'entidad/view/<id>' => 'entidad/view',
                'entidad/view-detalles/<id>' => 'entidad/view-detalles',
                'entidad/create' => 'entidad/create',
                'entidad/update/<id>' => 'entidad/update',
                'entidad/delete/<id>' => 'entidad/delete',
                'entidad/profesor-evaluations/<idprofesor:\d+>/<estado:\d+>' => 'entidad/profesor-evaluations',

                // Detalle Entidad
                'detalle-entidad' => 'detalle-entidad/index',
                'detalle-entidad/view/<id>' => 'detalle-entidad/view',
                'detalle-entidad/create' => 'detalle-entidad/create',
                'detalle-entidad/update/<id>' => 'detalle-entidad/update',
                'detalle-entidad/delete/<id>' => 'detalle-entidad/delete',

                // Recurso Entidad
                'recurso' => 'recurso/index',
                'recurso/view/<id>' => 'recurso/view',
                'recurso/create' => 'recurso/create',
                'recurso/update/<id>' => 'recurso/update',
                'recurso/delete/<id>' => 'recurso/delete',

                // Recurso Entidad Descripcion
                'recurso-descripcion' => 'recurso-descripcion/index',
                'recurso-descripcion/view/<id>' => 'recurso-descripcion/view',
                'recurso-descripcion/create' => 'recurso-descripcion/create',
                'recurso-descripcion/update/<id>' => 'recurso-descripcion/update',
                'recurso-descripcion/delete/<id>' => 'recurso-descripcion/delete',

                // Tipo_Asociacion
                'tipo-asociacion' => 'tipo-asociacion/index',
                'tipo-asociacion/view/<id>' => 'tipo-asociacion/view',
                'tipo-asociacion/create' => 'tipo-asociacion/create',
                'tipo-asociacion/update/<id>' => 'tipo-asociacion/update',
                'tipo-asociacion/delete/<id>' => 'tipo-asociacion/delete',
                'tipo-asociacion/relationship/<idEntidad1:\d+>/<idEntidad2:\d+>' => 'tipo-asociacion/relationship',

                // Asociacion
                'asociacion' => 'asociacion/index',
                'asociacion/view/<id>' => 'asociacion/view',
                'asociacion/create' => 'asociacion/create',
                'asociacion/update/<id>' => 'asociacion/update',
                'asociacion/delete/<id>' => 'asociacion/delete',
                'asociacion/associate-entitys/<identidad:\d+>' => 'asociacion/associate-entitys',
                'asociacion/lista/<identidad:\d+>/<identidad2:\d+>' => 'asociacion/lista',
                'asociacion/evaluated-associate-entitys/<identidad:\d+>' => 'asociacion/evaluated-associate-entitys',

                // Reportes
                'reportes/count-evaluations-profesor/<idprofesor:\d+>' => 'reportes/count-evaluations-profesor',
                'reportes/count-evaluations-estudiante/<idestudiante:\d+>' => 'reportes/count-evaluations-estudiante',

                // Tipo Asociacion Multiple
                'tipo-asociacion-multiple' => 'tipo-asociacion-multiple/index',
                'tipo-asociacion-multiple/view/<id>' => 'tipo-asociacion-multiple/view',
                'tipo-asociacion-multiple/create' => 'tipo-asociacion-multiple/create',
                'tipo-asociacion-multiple/update/<id>' => 'tipo-asociacion-multiple/update',
                'tipo-asociacion-multiple/delete/<id>' => 'tipo-asociacion-multiple/delete',

                // Asociacion Multiple
                'asociacion-multiple' => 'asociacion-multiple/index',
                'asociacion-multiple/view/<id>' => 'asociacion-multiple/view',
                'asociacion-multiple/create' => 'asociacion-multiple/create',
                'asociacion-multiple/update/<id>' => 'asociacion-multiple/update',
                'asociacion-multiple/delete/<id>' => 'asociacion-multiple/delete',

                // Begin borrar ejemplo
                'employees' => 'employee/index',
                'employees/view/<id>' => 'employee/view',
                'employees/create' => 'employee/create',
                'employees/update/<id>' => 'employee/update',
                'employees/delete/<id>' => 'employee/delete',
                // End borrar ejemplo

                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<id:\d+>' => '<module>/<controller>/view',
                '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
                // '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<idprofesor:\d+>/<idgrupo:\d+>' => '<controller>/<action>',
            ],

        ],


        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'employee'],
            ],
        ],
        */


        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */

    ],
    'params' => $params,
];
