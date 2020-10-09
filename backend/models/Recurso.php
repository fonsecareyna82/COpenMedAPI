<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "recurso".
 *
 * @property int $IdRecurso
 * @property int $IdEntidad
 * @property int $Nivel
 * @property string $URL
 * @property bool $IsImage
 *
 * @property Entidad $entidad
 * @property RecursoDescripcion[] $recursoDescripcions
 */
class Recurso extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'recurso';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdEntidad', 'Nivel', 'URL'], 'required'],
            [['IdEntidad', 'Nivel'], 'integer'],
            [['IsImage'], 'boolean'],
            [['URL'], 'string'],
            [
                ['IdEntidad'], 'exist', 'skipOnError' => true, 'targetClass' => Entidad::className(),
                'targetAttribute' => ['IdEntidad' => 'IdEntidad'], 'message' => 'La Entidad que seleccionó no existe en la Base de Datos del Sistema.'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdRecurso' => 'Id Recurso',
            'IdEntidad' => 'Id Entidad',
            'Nivel' => 'Nivel',
            'URL' => 'Url',
            'IsImage' => 'Is Image',
        ];
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
    public function getRecursoDescripcions()
    {
        return $this->hasMany(RecursoDescripcion::className(), ['IdRecurso' => 'IdRecurso']);
    }

    static public function search($params)
    {
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if (isset($search)) {
            $params = $search;
        }


        $query = Recurso::find()
            ->select(['{{recurso}}.*'])
            ->asArray(true);


        if (isset($params['IdRecurso'])) {
            $query->andFilterWhere(['IdRecurso' => $params['IdRecurso']]);
        }
        if (isset($params['IdEntidad'])) {
            $query->andFilterWhere(['IdEntidad' => $params['IdEntidad']]);
        }
        if (isset($params['Nivel'])) {
            $query->andFilterWhere(['Nivel' => $params['Nivel']]);
        }
        if (isset($params['URL'])) {
            $query->andFilterWhere(['like', 'URL', $params['URL']]);
        }
        if (isset($params['IsImage'])) {
            $query->andFilterWhere(['IsImage' => $params['IsImage']]);
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
