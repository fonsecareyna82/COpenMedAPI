<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "doc_grupo".
 *
 * @property int $IdGrupo
 * @property string $Grupo
 *
 * @property DocEstudiante[] $docEstudiantes
 * @property DocProfesorHasDocGrupo[] $docProfesorHasDocGrupos
 * @property DocProfesor[] $profesors
 */
class DocGrupo extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'doc_grupo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Grupo'], 'required'],
            [['Grupo'], 'string'],
            [['Grupo'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdGrupo' => 'Id Grupo',
            'Grupo' => 'Grupo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocEstudiantes()
    {
        return $this->hasMany(DocEstudiante::className(), ['IdGrupo' => 'IdGrupo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocProfesorHasDocGrupos()
    {
        return $this->hasMany(DocProfesorHasDocGrupo::className(), ['IdGrupo' => 'IdGrupo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfesors()
    {
        return $this->hasMany(DocProfesor::className(), ['IdProfesor' => 'IdProfesor'])->viaTable('doc_profesor_has_doc_grupo', ['IdGrupo' => 'IdGrupo']);
    }

    static public function search($params)
    {        
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if(isset($search)){
            $params=$search;
        }
        
        $query = DocGrupo::find()
            ->select(['IdGrupo', 'Grupo'])
            ->asArray(true);            

        if(isset($params['IdGrupo'])) {
            $query->andFilterWhere(['IdGrupo' => $params['IdGrupo']]);
        }        
        if(isset($params['Grupo'])) {
            $query->andFilterWhere(['like', 'Grupo', $params['Grupo']]);
        }

        if(isset($order)){
            $query->orderBy($order);
        }


        $additional_info = [
            'page' => 'No Define',
            'size' => 'No Define',
            'totalCount' => (int)$query->count()
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }
}
