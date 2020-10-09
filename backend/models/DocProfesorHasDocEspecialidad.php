<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doc_profesor_has_doc_especialidad".
 *
 * @property int $IdProfesor
 * @property int $IdEspecialidad
 *
 * @property DocEspecialidad $especialidad
 * @property DocProfesor $profesor
 */
class DocProfesorHasDocEspecialidad extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'doc_profesor_has_doc_especialidad';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdProfesor', 'IdEspecialidad'], 'required'],
            [['IdProfesor', 'IdEspecialidad'], 'integer'],
            [['IdProfesor', 'IdEspecialidad'], 'unique', 'targetAttribute' => ['IdProfesor', 'IdEspecialidad']],
            [
                ['IdEspecialidad'], 'exist', 'skipOnError' => true, 'targetClass' => DocEspecialidad::className(),
                'targetAttribute' => ['IdEspecialidad' => 'IdEspecialidad'], 'message' => 'La especialidad que seleccionó no existe en la Base de Datos del Sistema.'
            ],
            [
                ['IdProfesor'], 'exist', 'skipOnError' => true, 'targetClass' => DocProfesor::className(),
                'targetAttribute' => ['IdProfesor' => 'IdProfesor'], 'message' => 'El profesor que seleccionó no existe en la Base de Datos del Sistema.'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdProfesor' => 'Id Profesor',
            'IdEspecialidad' => 'Id Especialidad',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEspecialidad()
    {
        return $this->hasOne(DocEspecialidad::className(), ['IdEspecialidad' => 'IdEspecialidad']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfesor()
    {
        return $this->hasOne(DocProfesor::className(), ['IdProfesor' => 'IdProfesor']);
    }
}
