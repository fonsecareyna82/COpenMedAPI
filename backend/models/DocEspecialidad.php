<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "doc_especialidad".
 *
 * @property int $IdEspecialidad
 * @property string $Especialidad
 *
 * @property DocProfesorHasDocEspecialidad[] $docProfesorHasDocEspecialidads
 * @property DocProfesor[] $profesors
 */
class DocEspecialidad extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'doc_especialidad';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Especialidad'], 'required'],
            [['Especialidad'], 'string', 'max' => 150],
            [['Especialidad'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdEspecialidad' => 'Id Especialidad',
            'Especialidad' => 'Especialidad',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocProfesorHasDocEspecialidads()
    {
        return $this->hasMany(DocProfesorHasDocEspecialidad::className(), ['IdEspecialidad' => 'IdEspecialidad']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfesors()
    {
        return $this->hasMany(DocProfesor::className(), ['IdProfesor' => 'IdProfesor'])->viaTable('doc_profesor_has_doc_especialidad', ['IdEspecialidad' => 'IdEspecialidad']);
    }

    static public function search($params)
    {
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if (isset($search)) {
            $params = $search;
        }

        $query = DocEspecialidad::find()
            ->select(['IdEspecialidad', 'Especialidad'])
            ->asArray(true);

        if (isset($params['IdEspecialidad'])) {
            $query->andFilterWhere(['IdEspecialidad' => $params['IdEspecialidad']]);
        }
        if (isset($params['Especialidad'])) {
            $query->andFilterWhere(['like', 'Especialidad', $params['Especialidad']]);
        }

        if (isset($order)) {
            $query->orderBy($order);
        }


        $additional_info = [
            'page' => 'No Define',
            'size' => 'No Define',
            'totalCount' => (int) $query->count()
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }
}
