<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "doc_estudiante".
 *
 * @property int $IdEstudiante
 * @property int $IdPersona
 * @property int $IdGrupo
 *
 * @property Asociacion[] $asociacions
 * @property AdmPersona $persona
 * @property DocGrupo $grupo
 * @property Entidad[] $entidads
 */
class DocEstudiante extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'doc_estudiante';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdPersona', 'IdGrupo'], 'required'],
            [['IdPersona', 'IdGrupo'], 'integer'],
            [
                ['IdPersona'], 'exist', 'skipOnError' => true, 'targetClass' => AdmPersona::className(),
                'targetAttribute' => ['IdPersona' => 'IdPersona'], 'message' => 'La persona que seleccionó no existe en la Base de Datos del Sistema.'
            ],
            /*[
                ['IdGrupo'], 'exist', 'skipOnError' => true, 'targetClass' => DocGrupo::className(),
                'targetAttribute' => ['IdGrupo' => 'IdGrupo'], 'message' => 'El grupo que seleccionó no existe en la Base de Datos del Sistema.'
            ],*/
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdEstudiante' => 'Id Estudiante',
            'IdPersona' => 'Id Persona',
            'IdGrupo' => 'Id Grupo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsociacions()
    {
        return $this->hasMany(Asociacion::className(), ['IdEstudiante' => 'IdEstudiante']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersona()
    {
        return $this->hasOne(AdmPersona::className(), ['IdPersona' => 'IdPersona']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrupo()
    {
        return $this->hasOne(DocGrupo::className(), ['IdGrupo' => 'IdGrupo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntidads()
    {
        return $this->hasMany(Entidad::className(), ['IdEstudiante' => 'IdEstudiante']);
    }

    static public function search($params)
    {
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        $order = isset($order) ? $order : 'Grupo ASC, NombreCompleto ASC';

        if (isset($search)) {
            $params = $search;
        }

        $query = DocEstudiante::find()
            ->select(['{{doc_estudiante}}.*', "CONCAT(
                PrimerNombre, ' ', IFNULL(SegundoNombre, ''), ' ', 
                ApellidoPaterno, ' ', ApellidoMaterno) AS NombreCompleto", 'Grupo', 'username', 'status', 'id'])
            ->leftJoin('adm_persona', '`doc_estudiante`.`IdPersona` = `adm_persona`.`IdPersona`')
            ->leftJoin('seg_usuario', '`doc_estudiante`.`IdPersona` = `seg_usuario`.`IdPersona`')
            ->leftJoin('doc_grupo', '`doc_estudiante`.`IdGrupo` = `doc_grupo`.`IdGrupo`')
            ->asArray(true);

        if (isset($params['IdEstudiante'])) {
            $query->andFilterWhere(['IdEstudiante' => $params['IdEstudiante']]);
        }
        if (isset($params['IdPersona'])) {
            $query->andFilterWhere(['doc_estudiante.IdPersona' => $params['IdPersona']]);
        }
        if (isset($params['IdGrupo'])) {
            $query->andFilterWhere(['doc_estudiante.IdGrupo' => $params['IdGrupo']]);
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
