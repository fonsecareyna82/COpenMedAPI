<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_asociacion".
 *
 * @property int $IdTipoAsociacion
 * @property int $IdTipoEntidad1
 * @property int $IdTipoEntidad2
 * @property string $TipoAsociacion
 *
 * @property Asociacion[] $asociacions
 * @property TipoEntidad $tipoEntidad1
 * @property TipoEntidad $tipoEntidad2
 */
class TipoAsociacion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_asociacion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdTipoEntidad1', 'IdTipoEntidad2', 'TipoAsociacion'], 'required'],
            [['IdTipoEntidad1', 'IdTipoEntidad2'], 'integer'],
            [['TipoAsociacion'], 'string'],           
            [
                ['IdTipoEntidad1'], 'exist', 'skipOnError' => true, 'targetClass' => TipoEntidad::className(),
                'targetAttribute' => ['IdTipoEntidad1' => 'IdTipoEntidad'], 'message' => 'El tipo entidad que seleccionó no existe en la Base de Datos del Sistema.'
            ],
            [
                ['IdTipoEntidad2'], 'exist', 'skipOnError' => true, 'targetClass' => TipoEntidad::className(),
                'targetAttribute' => ['IdTipoEntidad2' => 'IdTipoEntidad'], 'message' => 'El tipo entidad que seleccionó no existe en la Base de Datos del Sistema.'
            ],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdTipoAsociacion' => 'Id Tipo Asociacion',
            'IdTipoEntidad1' => 'Id Tipo Entidad1',
            'IdTipoEntidad2' => 'Id Tipo Entidad2',
            'TipoAsociacion' => 'Tipo Asociacion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsociacions()
    {
        return $this->hasMany(Asociacion::className(), ['IdTipoAsociacion' => 'IdTipoAsociacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoEntidad1()
    {
        return $this->hasOne(TipoEntidad::className(), ['IdTipoEntidad' => 'IdTipoEntidad1']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoEntidad2()
    {
        return $this->hasOne(TipoEntidad::className(), ['IdTipoEntidad' => 'IdTipoEntidad2']);
    }

    static public function search($params)
    {
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if (isset($search)) {
            $params = $search;
        }


        $query = TipoAsociacion::find()
            ->select([
                '{{tipo_asociacion}}.*',
                'tent1.TipoEntidad AS TipoEntidad1',
                'tent2.TipoEntidad AS TipoEntidad2'
            ])
            ->leftJoin('tipo_entidad AS tent1', '`tipo_asociacion`.`IdTipoEntidad1` = `tent1`.`IdTipoEntidad`')
            ->leftJoin('tipo_entidad AS tent2', '`tipo_asociacion`.`IdTipoEntidad2` = `tent2`.`IdTipoEntidad`')
            ->asArray(true);


        if (isset($params['IdTipoAsociacion'])) {
            $query->andFilterWhere(['IdTipoAsociacion' => $params['IdTipoAsociacion']]);
        }
        if (isset($params['IdTipoEntidad1'])) {
            $query->andFilterWhere(['IdTipoEntidad1' => $params['IdTipoEntidad1']]);
        }
        if (isset($params['IdTipoEntidad2'])) {
            $query->andFilterWhere(['IdTipoEntidad2' => $params['IdTipoEntidad2']]);
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
