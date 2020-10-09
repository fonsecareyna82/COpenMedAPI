<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "seg_rol".
 *
 * @property int $IdRol
 * @property string $Rol
 *
 * @property User[] $user
 */
class SegRol extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'seg_rol';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Rol'], 'required'],
            [['Rol'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdRol' => 'Id Rol',
            'Rol' => 'Rol',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSegUsuarios()
    {
        return $this->hasMany(User::className(), ['IdRol' => 'IdRol']);
    }    
}
