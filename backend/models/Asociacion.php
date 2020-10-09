<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "asociacion".
 *
 * @property int $IdAsociacion
 * @property int $IdEntidad1
 * @property int $IdEntidad2
 * @property int $IdTipoAsociacion
 * @property int $IdEstudiante
 * @property int $IdProfesor
 * @property int $Evaluacion
 * @property int $Estado
 * @property double $Nivel
 * @property string $Comentario
 *
 * @property DocEstudiante $estudiante
 * @property DocProfesor $profesor
 * @property Entidad $entidad1
 * @property Entidad $entidad2
 * @property TipoAsociacion $tipoAsociacion
 * @property AsociacionMultiple[] $asociacionMultiples
 */
class Asociacion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'asociacion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdEntidad1', 'IdEntidad2', 'IdTipoAsociacion', 'IdEstudiante', 'Estado'], 'required'],
            [['Nivel'], 'double'],
            [['IdEntidad1', 'IdEntidad2', 'IdTipoAsociacion', 'IdEstudiante', 'IdProfesor', 'Evaluacion', 'Estado'], 'integer'],
            [['Comentario'], 'string'],
            [
                ['IdEstudiante'], 'exist', 'skipOnError' => true, 'targetClass' => DocEstudiante::className(),
                'targetAttribute' => ['IdEstudiante' => 'IdEstudiante'], 'message' => 'El estudiante que seleccionó no existe en la Base de Datos del Sistema.'
            ],
            /*[
                ['IdProfesor'], 'exist', 'skipOnError' => true, 'targetClass' => DocProfesor::className(),
                'targetAttribute' => ['IdProfesor' => 'IdProfesor'], 'message' => 'El profesor que seleccionó no existe en la Base de Datos del Sistema.'
            ],*/
            [
                ['IdEntidad1'], 'exist', 'skipOnError' => true, 'targetClass' => Entidad::className(),
                'targetAttribute' => ['IdEntidad1' => 'IdEntidad'], 'message' => 'La entidad que seleccionó no existe en la Base de Datos del Sistema.'
            ],
            [
                ['IdEntidad2'], 'exist', 'skipOnError' => true, 'targetClass' => Entidad::className(),
                'targetAttribute' => ['IdEntidad2' => 'IdEntidad'], 'message' => 'La entidad que seleccionó no existe en la Base de Datos del Sistema.'
            ],
            [
                ['IdTipoAsociacion'], 'exist', 'skipOnError' => true, 'targetClass' => TipoAsociacion::className(),
                'targetAttribute' => ['IdTipoAsociacion' => 'IdTipoAsociacion'], 'message' => 'El tipo asociación que seleccionó no existe en la Base de Datos del Sistema.'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdAsociacion' => 'Id Asociacion',
            'IdEntidad1' => 'Id Entidad1',
            'IdEntidad2' => 'Id Entidad2',
            'IdTipoAsociacion' => 'Id Tipo Asociacion',
            'IdEstudiante' => 'Id Estudiante',
            'IdProfesor' => 'Id Profesor',
            'Evaluacion' => 'Evaluacion',
            'Estado' => 'Estado',
            'Nivel' => 'Nivel',
            'Comentario' => 'Comentario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstudiante()
    {
        return $this->hasOne(DocEstudiante::className(), ['IdEstudiante' => 'IdEstudiante']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfesor()
    {
        return $this->hasOne(DocProfesor::className(), ['IdProfesor' => 'IdProfesor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntidad1()
    {
        return $this->hasOne(Entidad::className(), ['IdEntidad' => 'IdEntidad1']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntidad2()
    {
        return $this->hasOne(Entidad::className(), ['IdEntidad' => 'IdEntidad2']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoAsociacion()
    {
        return $this->hasOne(TipoAsociacion::className(), ['IdTipoAsociacion' => 'IdTipoAsociacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsociacionMultiples()
    {
        return $this->hasMany(AsociacionMultiple::className(), ['IdAsociacion' => 'IdAsociacion']);
    }

    static public function search($params)
    {
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if (isset($search)) {
            $params = $search;
        }

        $query = Asociacion::find()
            ->select([
                '{{asociacion}}.*', "CONCAT(est.PrimerNombre, ' ', IFNULL(est.SegundoNombre, ''), 
                ' ', est.ApellidoPaterno, ' ', est.ApellidoMaterno) AS Estudiante",
                "CONCAT(prof.PrimerNombre, ' ', IFNULL(prof.SegundoNombre, ''), 
                ' ', prof.ApellidoPaterno, ' ', prof.ApellidoMaterno) AS Profesor",
                '(SELECT Entidad FROM detalle_entidad WHERE ent1.IdEntidad = detalle_entidad.IdEntidad LIMIT 1) AS Entidad1',
                '(SELECT Entidad FROM detalle_entidad WHERE ent2.IdEntidad = detalle_entidad.IdEntidad LIMIT 1) AS Entidad2',
                'TipoAsociacion'
            ])
            ->leftJoin('entidad AS ent1', '`asociacion`.`IdEntidad1` = `ent1`.`IdEntidad`')
            ->leftJoin('entidad AS ent2', '`asociacion`.`IdEntidad2` = `ent2`.`IdEntidad`')
            ->leftJoin('tipo_asociacion', '`asociacion`.`IdTipoAsociacion` = `tipo_asociacion`.`IdTipoAsociacion`')
            ->leftJoin('doc_estudiante', '`asociacion`.`IdEstudiante` = `doc_estudiante`.`IdEstudiante`')
            ->leftJoin('adm_persona AS est', '`doc_estudiante`.`IdPersona` = `est`.`IdPersona`')
            ->leftJoin('doc_profesor', '`asociacion`.`IdProfesor` = `doc_profesor`.`IdProfesor`')
            ->leftJoin('adm_persona AS prof', '`doc_profesor`.`IdPersona` = `prof`.`IdPersona`')
            ->asArray(true);


        if (isset($params['IdAsociacion'])) {
            $query->andFilterWhere(['IdAsociacion' => $params['IdAsociacion']]);
        }
        if (isset($params['IdEntidad1'])) {
            $query->andFilterWhere(['IdEntidad1' => $params['IdEntidad1']]);
        }
        if (isset($params['IdEntidad2'])) {
            $query->andFilterWhere(['IdEntidad2' => $params['IdEntidad2']]);
        }
        if (isset($params['IdTipoAsociacion'])) {
            $query->andFilterWhere(['asociacion.IdTipoAsociacion' => $params['IdTipoAsociacion']]);
        }
        if (isset($params['IdEstudiante'])) {
            $query->andFilterWhere(['asociacion.IdEstudiante' => $params['IdEstudiante']]);
        }
        if (isset($params['IdProfesor'])) {
            $query->andFilterWhere(['asociacion.IdProfesor' => $params['IdProfesor']]);
        }
        if (isset($params['Evaluacion'])) {
            $query->andFilterWhere(['Evaluacion' => $params['Evaluacion']]);
        }
        if (isset($params['Estado'])) {
            $query->andFilterWhere(['Estado' => $params['Estado']]);
        }
        if (isset($params['Nivel'])) {
            $query->andFilterWhere(['Nivel' => $params['Nivel']]);
        }
        if (isset($params['Comentario'])) {
            $query->andFilterWhere(['like', 'Comentario', $params['Comentario']]);
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
