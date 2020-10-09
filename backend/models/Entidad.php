<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "entidad".
 *
 * @property int $IdEntidad
 * @property int $IdTipoEntidad
 * @property int $IdEstudiante
 * @property int $IdProfesor
 * @property int $Evaluacion
 * @property int $Estado
 * @property string $Comentario
 *
 * @property Asociacion[] $asociacions
 * @property Asociacion[] $asociacions0
 * @property AsociacionMultiple[] $asociacionMultiples
 * @property DetalleEntidad[] $detalleEntidads
 * @property DocEstudiante $estudiante
 * @property DocProfesor $profesor
 * @property TipoEntidad $tipoEntidad
 * @property Recurso[] $recursos
 */
class Entidad extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entidad';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdTipoEntidad', 'IdEstudiante', 'Estado'], 'required'],
            [['IdTipoEntidad', 'IdEstudiante', 'IdProfesor', 'Evaluacion', 'Estado'], 'integer'],
            [['Comentario'], 'string'],
            [
                ['IdEstudiante'], 'exist', 'skipOnError' => true, 'targetClass' => DocEstudiante::className(),
                'targetAttribute' => ['IdEstudiante' => 'IdEstudiante'], 'message' => 'El estudiante que seleccionó no existe en la Base de Datos del Sistema.'
            ],
            [
                ['IdTipoEntidad'], 'exist', 'skipOnError' => true, 'targetClass' => TipoEntidad::className(),
                'targetAttribute' => ['IdTipoEntidad' => 'IdTipoEntidad'], 'message' => 'El tipo entidad que seleccionó no existe en la Base de Datos del Sistema.'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdEntidad' => 'Id Entidad',
            'IdTipoEntidad' => 'Id Tipo Entidad',
            'IdEstudiante' => 'Id Estudiante',
            'IdProfesor' => 'Id Profesor',
            'Evaluacion' => 'Evaluacion',
            'Estado' => 'Estado',
            'Comentario' => 'Comentario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsociacions()
    {
        return $this->hasMany(Asociacion::className(), ['IdEntidad1' => 'IdEntidad']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsociacions0()
    {
        return $this->hasMany(Asociacion::className(), ['IdEntidad2' => 'IdEntidad']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsociacionMultiples()
    {
        return $this->hasMany(AsociacionMultiple::className(), ['IdEntidad' => 'IdEntidad']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleEntidads()
    {
        return $this->hasMany(DetalleEntidad::className(), ['IdEntidad' => 'IdEntidad']);
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
    public function getTipoEntidad()
    {
        return $this->hasOne(TipoEntidad::className(), ['IdTipoEntidad' => 'IdTipoEntidad']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecursos()
    {
        return $this->hasMany(Recurso::className(), ['IdEntidad' => 'IdEntidad']);
    }
        
    static public function search($params)
    {
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if (isset($search)) {
            $params = $search;
        }


        $query = Entidad::find()
            ->select([
                '{{entidad}}.*', "CONCAT(est.PrimerNombre, ' ', IFNULL(est.SegundoNombre, ''), 
                ' ', est.ApellidoPaterno, ' ', est.ApellidoMaterno) AS Estudiante",
                "CONCAT(prof.PrimerNombre, ' ', IFNULL(prof.SegundoNombre, ''), 
                ' ', prof.ApellidoPaterno, ' ', prof.ApellidoMaterno) AS Profesor ,(Estado+entidad) as suma",
                '(SELECT COUNT(IdAsociacion)               
                FROM asociacion
                WHERE ((asociacion.IdEntidad1 = entidad.IdEntidad AND (SELECT Estado FROM entidad WHERE entidad.IdEntidad = asociacion.IdEntidad2 LIMIT 1) = 1)
                OR ( asociacion.IdEntidad2 = entidad.IdEntidad AND (SELECT Estado FROM entidad WHERE entidad.IdEntidad = asociacion.IdEntidad1 LIMIT 1) = 1))
                AND asociacion.Estado = 0) as countAsociacionesEspera',   
                '(SELECT COUNT(IdAsociacion)
                FROM asociacion
                WHERE ((asociacion.IdEntidad1 = entidad.IdEntidad AND (SELECT Estado FROM entidad WHERE entidad.IdEntidad = asociacion.IdEntidad2 LIMIT 1) = 1)
                OR ( asociacion.IdEntidad2 = entidad.IdEntidad AND (SELECT Estado FROM entidad WHERE entidad.IdEntidad = asociacion.IdEntidad1 LIMIT 1) = 1))
                AND asociacion.Estado = 1 AND asociacion.Evaluacion = 0) as countAsociacionesMal',                 
                'TipoEntidad', 'IdRecurso', 'detalle_entidad.IdIdioma', 'Entidad', 'detalle_entidad.Nivel', 'Idioma'
            ])
            ->distinct()
            ->leftJoin('tipo_entidad', '`entidad`.`IdTipoEntidad` = `tipo_entidad`.`IdTipoEntidad`')
            ->leftJoin('detalle_entidad', '`entidad`.`IdEntidad` = `detalle_entidad`.`IdEntidad`')
            ->leftJoin('idioma', '`detalle_entidad`.`IdIdioma` = `idioma`.`IdIdioma`')
            ->leftJoin('doc_estudiante', '`entidad`.`IdEstudiante` = `doc_estudiante`.`IdEstudiante`')
            ->leftJoin('adm_persona AS est', '`doc_estudiante`.`IdPersona` = `est`.`IdPersona`')
            ->leftJoin('doc_profesor', '`entidad`.`IdProfesor` = `doc_profesor`.`IdProfesor`')
            ->leftJoin('adm_persona AS prof', '`doc_profesor`.`IdPersona` = `prof`.`IdPersona`')
            ->orderBy('suma ASC,countAsociacionesMal DESC,countAsociacionesEspera DESC')
            ->asArray(true);

        if (isset($params['IdEntidad'])) {
            $query->andFilterWhere(['entidad.IdEntidad' => $params['IdEntidad']]);
        }
        if (isset($params['IdTipoEntidad'])) {
            $query->andFilterWhere(['entidad.IdTipoEntidad' => $params['IdTipoEntidad']]);
        }        
         if (isset($params['IdEstudiante'])) {
             $query->andFilterWhere(['entidad.IdEstudiante' => $params['IdEstudiante']]);
         }
        if (isset($params['IdProfesor'])) {
            $query->andFilterWhere(['entidad.IdProfesor' => $params['IdProfesor']]);
        }
        if (isset($params['Entidad'])) {
            $query->andFilterWhere(['like', 'Entidad', $params['Entidad']]);
        }
        if (isset($params['Evaluacion'])) {
            $query->andFilterWhere(['Evaluacion' => $params['Evaluacion']]);
        }
        if (isset($params['Estado'])) {
            $query->andFilterWhere(['Estado' => $params['Estado']]);
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
