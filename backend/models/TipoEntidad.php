<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_entidad".
 *
 * @property int $IdTipoEntidad
 * @property string $TipoEntidad
 *
 * @property Entidad[] $entidads
 * @property TipoAsociacion[] $tipoAsociacions
 * @property TipoAsociacion[] $tipoAsociacions0
 * @property TipoAsociacionMultiple[] $tipoAsociacionMultiples
 */
class TipoEntidad extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_entidad';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['TipoEntidad'], 'required'],
            [['TipoEntidad'], 'string'],
            [['TipoEntidad'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdTipoEntidad' => 'Id Tipo Entidad',
            'TipoEntidad' => 'Tipo Entidad',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntidads()
    {
        return $this->hasMany(Entidad::className(), ['IdTipoEntidad' => 'IdTipoEntidad']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoAsociacions()
    {
        return $this->hasMany(TipoAsociacion::className(), ['IdTipoEntidad1' => 'IdTipoEntidad']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoAsociacions0()
    {
        return $this->hasMany(TipoAsociacion::className(), ['IdTipoEntidad2' => 'IdTipoEntidad']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoAsociacionMultiples()
    {
        return $this->hasMany(TipoAsociacionMultiple::className(), ['IdTipoEntidad' => 'IdTipoEntidad']);
    }

    static public function search($params)
    {
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if (isset($search)) {
            $params = $search;
        }


        $query = TipoEntidad::find()
            ->select(['{{tipo_entidad}}.*'])
            ->asArray(true);


        if (isset($params['IdTipoEntidad'])) {
            $query->andFilterWhere(['IdTipoEntidad' => $params['IdTipoEntidad']]);
        }        
        if (isset($params['TipoEntidad'])) {
            $query->andFilterWhere(['like', 'TipoEntidad', $params['TipoEntidad']]);
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
