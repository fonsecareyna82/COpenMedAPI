<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "doc_profesor".
 *
 * @property int $IdProfesor
 * @property int $IdPersona
 *
 * @property Asociacion[] $asociacions
 * @property AdmPersona $persona
 * @property DocProfesorHasDocEspecialidad[] $docProfesorHasDocEspecialidads
 * @property DocEspecialidad[] $especialidads
 * @property DocProfesorHasDocGrupo[] $docProfesorHasDocGrupos
 * @property DocGrupo[] $grupos
 * @property Entidad[] $entidads
 */
class DocProfesor extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'doc_profesor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdPersona'], 'required'],
            [['IdPersona'], 'integer'],
            [
                ['IdPersona'], 'exist', 'skipOnError' => true, 'targetClass' => AdmPersona::className(), 'targetAttribute' => ['IdPersona' => 'IdPersona'],
                'message' => 'La persona que seleccionó no existe en la Base de Datos del Sistema.'
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
            'IdPersona' => 'Id Persona',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsociacions()
    {
        return $this->hasMany(Asociacion::className(), ['IdProfesor' => 'IdProfesor']);
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
    public function getDocProfesorHasDocEspecialidads()
    {
        return $this->hasMany(DocProfesorHasDocEspecialidad::className(), ['IdProfesor' => 'IdProfesor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEspecialidads()
    {
        return $this->hasMany(DocEspecialidad::className(), ['IdEspecialidad' => 'IdEspecialidad'])->viaTable('doc_profesor_has_doc_especialidad', ['IdProfesor' => 'IdProfesor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocProfesorHasDocGrupos()
    {
        return $this->hasMany(DocProfesorHasDocGrupo::className(), ['IdProfesor' => 'IdProfesor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrupos()
    {
        return $this->hasMany(DocGrupo::className(), ['IdGrupo' => 'IdGrupo'])->viaTable('doc_profesor_has_doc_grupo', ['IdProfesor' => 'IdProfesor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntidads()
    {
        return $this->hasMany(Entidad::className(), ['IdProfesor' => 'IdProfesor']);
    }

    static public function search($params)
    {
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if (isset($search)) {
            $params = $search;
        }

        $query = DocProfesor::find()
            ->select(['{{doc_profesor}}.*', "CONCAT(
                PrimerNombre, ' ', IFNULL(SegundoNombre, ''), ' ', 
                ApellidoPaterno, ' ', ApellidoMaterno) AS NombreCompleto", 'username', 'status', 'id'])
            ->leftJoin('adm_persona', '`doc_profesor`.`IdPersona` = `adm_persona`.`IdPersona`')            
            ->leftJoin('seg_usuario', '`doc_profesor`.`IdPersona` = `seg_usuario`.`IdPersona`')
            ->asArray(true);

        if (isset($params['IdProfesor'])) {
            $query->andFilterWhere(['IdProfesor' => $params['IdProfesor']]);
        }
        if (isset($params['IdPersona'])) {
            $query->andFilterWhere(['doc_profesor.IdPersona' => $params['IdPersona']]);
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
