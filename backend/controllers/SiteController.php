<?php

namespace backend\controllers;

use Yii;
use common\models\LoginForm;
use common\models\AuthorizationCodes;
use common\models\AccessTokens;

use backend\models\SignupForm;
use backend\models\SegUsuario;
use app\models\DocEstudiante;
use app\models\DocProfesor;
use backend\behaviours\Verbcheck;
use backend\behaviours\Apiauth;

/**
 * Site controller
 */
class SiteController extends RestController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [
            'apiauth' => [ // el codigo que valida una peticion api es este de la clase Apiauth es el que verifica el token
                'class' => Apiauth::className(),
                'exclude' => ['authorize', 'accesstoken', 'index', /*'register'*/],
            ],
            'verbs' => [
                'class' => Verbcheck::className(),
                'actions' => [
                    'logout' => ['GET'],
                    'authorize' => ['POST'],
                    'register' => ['POST'],
                    'accesstoken' => ['POST'],
                    'me' => ['GET'],
                    'update-user' => ['PUT'],
                    'view-user' => ['GET'],
                    'change-password' => ['PUT'],
                    'user-name-exist' => ['GET'],
                ],
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    /*public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }*/

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            //return $this->render('error', ['exception' => $exception]);
            Yii::$app->api->sendFailedResponse($exception->getMessage());
        }
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        Yii::$app->api->sendSuccessResponse(['CeuOpenMed RESTful API with OAuth2']);
        //return $this->render('index');        
    }

    public function actionRegister()
    {

        $model = new SignupForm();
        $model->attributes = $this->request;
        $user = $model->signup();
    }


    public function actionMe()
    {
        $data = Yii::$app->user->identity;
        $persona = $data->persona;

        $rol = $data->rol;
        $data = $data->attributes;
        $data['NombreCompleto'] = $persona->PrimerNombre . ' ' . $persona->SegundoNombre . ' ' . $persona->ApellidoPaterno . ' ' . $persona->ApellidoMaterno;
        $data['Rol'] = $rol->Rol;
        $tempData = DocEstudiante::findOne(['IdPersona' => $persona->IdPersona]);
        if ($tempData) {
            $data['IdEstudiante'] = $tempData->IdEstudiante;
            $data['IdProfesor'] = 0;
        } else {
            $tempData = DocProfesor::findOne(['IdPersona' => $persona->IdPersona]);
            if ($tempData) {
                $data['IdProfesor'] = $tempData->IdProfesor;
                $data['IdEstudiante'] = 0;
            } else {
                $data['IdEstudiante'] = 0;
                $data['IdProfesor'] = 0;
            }
        }

        unset($data['auth_key']);
        unset($data['password_hash']);
        unset($data['password_reset_token']);

        Yii::$app->api->sendSuccessResponse($data);
    }

    public function actionAccesstoken()
    {

        if (!isset($this->request["authorization_code"])) {
            Yii::$app->api->sendFailedResponse("Falta el Código de Autorización");
        }

        $authorization_code = $this->request["authorization_code"];

        $auth_code = AuthorizationCodes::isValid($authorization_code);
        if (!$auth_code) {
            Yii::$app->api->sendFailedResponse("Código de Autorización inválido");
        }

        $accesstoken = Yii::$app->api->createAccesstoken($authorization_code);

        $data = [];
        $data['access_token'] = $accesstoken->token;
        $data['expires_at'] = $accesstoken->expires_at;
        Yii::$app->api->sendSuccessResponse($data);
    }

    public function actionAuthorize()
    {
        $model = new LoginForm();

        $model->attributes = $this->request;


        if ($model->validate() && $model->login()) {

            $auth_code = Yii::$app->api->createAuthorizationCode(Yii::$app->user->identity['id']);

            $data = [];
            $data['authorization_code'] = $auth_code->code;
            $data['expires_at'] = $auth_code->expires_at;

            Yii::$app->api->sendSuccessResponse($data);
        } else {
            Yii::$app->api->sendFailedResponse($model->errors);
        }
    }

    public function actionLogout()
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        $access_token = $headers->get('x-access-token');

        if (!$access_token) {
            $access_token = Yii::$app->getRequest()->getQueryParam('access-token');
        }

        $model = AccessTokens::findOne(['token' => $access_token]);

        if ($model->delete()) {

            Yii::$app->api->sendSuccessResponse(["Sección cerrada correctamente"]);
        } else {
            Yii::$app->api->sendFailedResponse("Petición inválida");
        }
    }

    public function actionUpdateUser($id)
    {
        $model = new SegUsuario();
        $model->attributes = $this->request;
        $model->id = $id;
        $model->update();
    }

    public function actionViewUser($id)
    {
        $model = new SegUsuario();
        $model->attributes = $this->request;
        $model->id = $id;
        $model->getUsuario();
    }

    public function actionChangePassword($id)
    {
        $model = new SegUsuario();
        // $model->attributes = $this->request;
        $model->id = $id;
        $model->username = $this->request['uusername'];
        $model->oldpassword = $this->request['oldpassword'];
        $model->password = $this->request['upassword'];
        $model->changePassword();
    }

    public function actionUserNameExist($username)
    {
        $model = new SegUsuario();
        $model->username = $username;
        $model->existUserName();
    }
}
