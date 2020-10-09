<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "idioma".
 *
 * @property int $IdIdioma
 * @property string $Idioma
 *
 * @property DetalleEntidad[] $detalleEntidads
 * @property RecursoDescripcion[] $recursoDescripcions
 */
class Idioma extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'idioma';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Idioma'], 'required'],
            [['Idioma'], 'string'],
            [['Idioma'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdIdioma' => 'Id Idioma',
            'Idioma' => 'Idioma',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleEntidads()
    {
        return $this->hasMany(DetalleEntidad::className(), ['IdIdioma' => 'IdIdioma']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecursoDescripcions()
    {
        return $this->hasMany(RecursoDescripcion::className(), ['IdIdioma' => 'IdIdioma']);
    }

    static public function search($params)
    {
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if (isset($search)) {
            $params = $search;
        }


        $query = Idioma::find()
            ->select(['IdIdioma', 'Idioma'])
            ->asArray(true);


        if (isset($params['IdIdioma'])) {
            $query->andFilterWhere(['IdIdioma' => $params['IdIdioma']]);
        }
        if (isset($params['Idioma'])) {
            $query->andFilterWhere(['like', 'Idioma', $params['Idioma']]);
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
