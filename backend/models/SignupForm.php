<?php
namespace backend\models;

use yii\base\Model;
use common\models\User;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $IdRol;
    public $IdPersona;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'],'required'],
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Este Nombre de Usuario ya está en uso.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Esta Dirección de Email ya está en uso.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            
            ['IdRol', 'required'],
            ['IdRol', 'integer'],
            
            ['IdPersona', 'required'],
            ['IdPersona', 'integer'],            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [            
            'username' => 'Username',
            'password' => 'Password',
            'email' => 'Email',
            'IdRol' => 'Id Rol',
            'IdPersona' => 'Id Persona',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {

        if (!$this->validate()) {

            Yii::$app->api->sendFailedResponse($this->errors);
            //return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();        
        $user->IdRol = $this->IdRol;
        $user->IdPersona = $this->IdPersona;
        if ( $user->insert() ) {

            $data=$user->attributes;
            unset($data['auth_key']);
            unset($data['password_hash']);
            unset($data['password_reset_token']);

            Yii::$app->api->sendSuccessResponse($data);

        }
        else {
            Yii::$app->api->sendFailedResponse( $user->errors );
        }
    }
}
