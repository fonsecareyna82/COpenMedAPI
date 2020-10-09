<?php

namespace common\models;
use yii\db\ActiveRecord;

use Yii;

/**
 * This is the model class for table "adm_persona".
 *
 * @property int $IdPersona
 * @property string $PrimerNombre
 * @property string $SegundoNombre
 * @property string $ApellidoPaterno
 * @property string $ApellidoMaterno
 *
 * @property User[] $user
 */
class AdmPersona extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'adm_persona';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['PrimerNombre', 'ApellidoPaterno', 'ApellidoMaterno'], 'required'],
            [['PrimerNombre', 'SegundoNombre', 'ApellidoPaterno', 'ApellidoMaterno'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdPersona' => 'Id Persona',
            'PrimerNombre' => 'Primer Nombre',
            'SegundoNombre' => 'Segundo Nombre',
            'ApellidoPaterno' => 'Apellido Paterno',
            'ApellidoMaterno' => 'Apellido Materno',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSegUsuarios()
    {
        return $this->hasMany(User::className(), ['IdPersona' => 'IdPersona']);
    }
}
