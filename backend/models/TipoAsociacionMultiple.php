<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_asociacion_multiple".
 *
 * @property int $IdTipoAsociacionMultiple
 * @property int $IdTipoEntidad
 * @property string $TipoAsociacion
 *
 * @property AsociacionMultiple[] $asociacionMultiples
 * @property TipoEntidad $tipoEntidad
 */
class TipoAsociacionMultiple extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_asociacion_multiple';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdTipoEntidad', 'TipoAsociacion'], 'required'],
            [['IdTipoEntidad'], 'integer'],
            [['TipoAsociacion'], 'string'],
            [
                ['IdTipoEntidad'], 'exist', 'skipOnError' => true, 'targetClass' => TipoEntidad::className(),
                'targetAttribute' => ['IdTipoEntidad' => 'IdTipoEntidad'], 'message' => 'El Tipo Entidad que seleccionó no existe en la Base de Datos del Sistema.'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdTipoAsociacionMultiple' => 'Id Tipo Asociacion Multiple',
            'IdTipoEntidad' => 'Id Tipo Entidad',
            'TipoAsociacion' => 'Tipo Asociacion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsociacionMultiples()
    {
        return $this->hasMany(AsociacionMultiple::className(), ['IdTipoAsociacionMultiple' => 'IdTipoAsociacionMultiple']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoEntidad()
    {
        return $this->hasOne(TipoEntidad::className(), ['IdTipoEntidad' => 'IdTipoEntidad']);
    }

    static public function search($params)
    {
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if (isset($search)) {
            $params = $search;
        }


        $query = TipoAsociacionMultiple::find()
            ->select(['{{tipo_asociacion_multiple}}.*', 'TipoEntidad'])
            ->leftJoin('tipo_entidad', '`tipo_asociacion_multiple`.`IdTipoEntidad` = `tipo_entidad`.`IdTipoEntidad`')
            ->asArray(true);


        if (isset($params['IdTipoAsociacionMultiple'])) {
            $query->andFilterWhere(['IdTipoAsociacionMultiple' => $params['IdTipoAsociacionMultiple']]);
        }
        if (isset($params['IdTipoEntidad'])) {
            $query->andFilterWhere(['tipo_asociacion_multiple.IdTipoEntidad' => $params['IdTipoEntidad']]);
        }
        if (isset($params['TipoAsociacion'])) {
            $query->andFilterWhere(['like', 'TipoAsociacion', $params['TipoAsociacion']]);
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
