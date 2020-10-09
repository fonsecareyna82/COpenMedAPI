<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "asociacion_multiple".
 *
 * @property int $IdAsociacionMultiple
 * @property int $IdAsociacion
 * @property int $IdEntidad
 * @property int $IdTipoEntidad
 * @property int $IdTipoAsociacionMultiple
 *
 * @property Asociacion $asociacion
 * @property Entidad $entidad
 * @property TipoAsociacionMultiple $tipoAsociacionMultiple
 */
class AsociacionMultiple extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'asociacion_multiple';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdAsociacion'], 'required'],
            [['Nivel'], 'double'],
            [['Comentario'], 'string'],
            [['IdAsociacion', 'IdEntidad', 'IdTipoEntidad', 'IdTipoAsociacionMultiple'], 'integer'],
            [
                ['IdAsociacion'], 'exist', 'skipOnError' => true, 'targetClass' => Asociacion::className(),
                'targetAttribute' => ['IdAsociacion' => 'IdAsociacion'], 'message' => 'La Asociación que seleccionó no existe en la Base de Datos del Sistema.'
            ],
            /*[
                ['IdEntidad'], 'exist', 'skipOnError' => true, 'targetClass' => Entidad::className(),
                'targetAttribute' => ['IdEntidad' => 'IdEntidad'], 'message' => 'La Entidad que seleccionó no existe en la Base de Datos del Sistema.'
            ],
            [
                ['IdTipoAsociacionMultiple'], 'exist', 'skipOnError' => true, 'targetClass' => TipoAsociacionMultiple::className(),
                'targetAttribute' => ['IdTipoAsociacionMultiple' => 'IdTipoAsociacionMultiple'],
                'message' => 'El Tipo de Asociacion Múltiple que seleccionó no existe en la Base de Datos del Sistema.'
            ],*/
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdAsociacionMultiple' => 'Id Asociación Múltiple',
            'IdAsociacion' => 'Id Asociación',
            'IdEntidad' => 'Id Entidad',
            'IdTipoEntidad' => 'Id Tipo Entidad',
            'IdTipoAsociacionMultiple' => 'Id Tipo Asociación Múltiple',
            'Nivel' => 'Nivel',
            'Comentario' => 'Comentario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsociacion()
    {
        return $this->hasOne(Asociacion::className(), ['IdAsociacion' => 'IdAsociacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntidad()
    {
        return $this->hasOne(Entidad::className(), ['IdEntidad' => 'IdEntidad']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoAsociacionMultiple()
    {
        return $this->hasOne(TipoAsociacionMultiple::className(), ['IdTipoAsociacionMultiple' => 'IdTipoAsociacionMultiple']);
    }

    static public function search($params)
    {
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if (isset($search)) {
            $params = $search;
        }


        $query = AsociacionMultiple::find()
            ->select([
                '{{asociacion_multiple}}.*', 'TipoEntidad', 'TipoAsociacion',
                '(SELECT Entidad FROM detalle_entidad WHERE asociacion_multiple.IdEntidad = detalle_entidad.IdEntidad LIMIT 1) AS Entidad'
            ])
            ->leftJoin('tipo_entidad', '`asociacion_multiple`.`IdTipoEntidad` = `tipo_entidad`.`IdTipoEntidad`')
            ->leftJoin('tipo_asociacion_multiple', '`asociacion_multiple`.`IdTipoAsociacionMultiple` = `tipo_asociacion_multiple`.`IdTipoAsociacionMultiple`')
            ->asArray(true);


        if (isset($params['IdAsociacionMultiple'])) {
            $query->andFilterWhere(['IdAsociacionMultiple' => $params['IdAsociacionMultiple']]);
        }
        if (isset($params['IdAsociacion'])) {
            $query->andFilterWhere(['IdAsociacion' => $params['IdAsociacion']]);
        }
        if (isset($params['IdEntidad'])) {
            $query->andFilterWhere(['IdEntidad' => $params['IdEntidad']]);
        }
        if (isset($params['IdTipoEntidad'])) {
            $query->andFilterWhere(['asociacion_multiple.IdTipoEntidad' => $params['IdTipoEntidad']]);
        }
        if (isset($params['TipoAsociacion'])) {
            $query->andFilterWhere(['like', 'TipoAsociacion', $params['TipoAsociacion']]);
        }
        if (isset($params['IdTipoAsociacionMultiple'])) {
            $query->andFilterWhere(['IdTipoAsociacionMultiple' => $params['IdTipoAsociacionMultiple']]);
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
