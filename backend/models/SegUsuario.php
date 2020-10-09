<?php

namespace backend\models;

use yii\base\Model;
use common\models\User;
use common\models\LoginForm;
use app\models\DocEstudiante;
use app\models\DocProfesor;
use Yii;

/**
 * Signup form
 */
class SegUsuario extends Model
{
    public $id;
    public $username;
    public $oldpassword;
    public $email;
    public $password;
    public $status;
    public $changePass;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Esta Dirección de Email ya está en uso.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            [['status'], 'number'],
            [['changePass'], 'boolean'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Id',
            'password' => 'Password',
            'email' => 'Email'
        ];
    }

    /**
     * Update user information.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function update()
    {
        if (!$this->validate()) {
            if (array_key_exists('email', $this->errors)) {
                $userHas = User::find()->where(['email' => $this->email])->one();
                if ($userHas) {
                    if ($userHas->id != $this->id) {
                        Yii::$app->api->sendFailedResponse($this->errors['email']);
                        //return null;
                    }
                }
            } else if (array_key_exists('password', $this->errors)) {
                if ($this->changePass) {
                    Yii::$app->api->sendFailedResponse($this->errors);
                    //return null;
                }
            } else {
                Yii::$app->api->sendFailedResponse($this->errors);
                //return null;
            }
        }

        $user = User::findOne($this->id);
        $user->email = $this->email;
        if ($this->changePass) {
            $user->setPassword($this->password);
            $user->generateAuthKey();
        }
        $user->status = $this->status;
        if ($user->save()) {

            $data = $user->attributes;
            unset($data['auth_key']);
            unset($data['password_hash']);
            unset($data['password_reset_token']);

            Yii::$app->api->sendSuccessResponse($data);
        } else {
            Yii::$app->api->sendFailedResponse($user->errors);
        }
    }

    public function getUsuario()
    {
        $data = User::findOne($this->id);
        if ($data === null) {
            Yii::$app->api->sendFailedResponse("El Registro requerido no existe");
        }
        $persona = $data->persona;

        $rol = $data->rol;
        $data = $data->attributes;
        $data['NombreCompleto'] = $persona->PrimerNombre . ' ' . $persona->SegundoNombre . ' ' . $persona->ApellidoPaterno . ' ' . $persona->ApellidoMaterno;
        $data['Rol'] = $rol->Rol;
        $tempData = DocEstudiante::findOne(['IdPersona' => $persona->IdPersona]);
        if ($tempData) {
            $data['IdEstudiante'] = $tempData->IdEstudiante;
        } else {
            $tempData = DocProfesor::findOne(['IdPersona' => $persona->IdPersona]);
            if ($tempData) {
                $data['IdProfesor'] = $tempData->IdProfesor;
            }
        }

        unset($data['auth_key']);
        unset($data['password_hash']);
        unset($data['password_reset_token']);

        Yii::$app->api->sendSuccessResponse($data);
    }

    /**
     * Change user password.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function changePassword()
    {
        if (!$this->validate()) {
            if (array_key_exists('password', $this->errors)) {
                Yii::$app->api->sendFailedResponse($this->errors);
                //return null;
            }
        }

        $verifyUser = new LoginForm();
        $verifyUser->username = $this->username;
        $verifyUser->password = $this->oldpassword;

        if (!$verifyUser->validate()) {
            Yii::$app->api->sendFailedResponse("La contraseña anterior no es correcta.");
        }

        $user = User::findOne($this->id);
        $user->setPassword($this->password);
        $user->generateAuthKey();
        if ($user->save()) {

            $data = $user->attributes;
            unset($data['auth_key']);
            unset($data['password_hash']);
            unset($data['password_reset_token']);

            Yii::$app->api->sendSuccessResponse($data);
        } else {
            Yii::$app->api->sendFailedResponse($user->errors);
        }
    }

    public function existUserName()
    {
        $data = User::findOne(['username' => $this->username]);
        if ($data === null) {
            Yii::$app->api->sendSuccessResponse($data);
        }
        $persona = $data->persona;

        $rol = $data->rol;
        $data = $data->attributes;
        $data['NombreCompleto'] = $persona->PrimerNombre . ' ' . $persona->SegundoNombre . ' ' . $persona->ApellidoPaterno . ' ' . $persona->ApellidoMaterno;
        $data['Rol'] = $rol->Rol;
        $tempData = DocEstudiante::findOne(['IdPersona' => $persona->IdPersona]);
        if ($tempData) {
            $data['IdEstudiante'] = $tempData->IdEstudiante;
        } else {
            $tempData = DocProfesor::findOne(['IdPersona' => $persona->IdPersona]);
            if ($tempData) {
                $data['IdProfesor'] = $tempData->IdProfesor;
            }
        }

        unset($data['auth_key']);
        unset($data['password_hash']);
        unset($data['password_reset_token']);

        Yii::$app->api->sendSuccessResponse($data);
    }
}
