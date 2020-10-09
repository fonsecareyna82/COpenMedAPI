<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detalle_entidad".
 *
 * @property int $IdRecurso
 * @property int $IdIdioma
 * @property int $IdEntidad
 * @property string $Entidad
 * @property int $Nivel
 *
 * @property Entidad $entidad
 * @property Idioma $idioma
 */
class DetalleEntidad extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'detalle_entidad';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdIdioma', 'IdEntidad', 'Entidad'], 'required'],
            [['IdIdioma', 'IdEntidad', 'Nivel'], 'integer'],
            [['Entidad'], 'string'],
            [
                ['IdEntidad'], 'exist', 'skipOnError' => true, 'targetClass' => Entidad::className(),
                'targetAttribute' => ['IdEntidad' => 'IdEntidad'], 'message' => 'La Entidad que seleccionó no existe en la Base de Datos del Sistema.'
            ],
            [
                ['IdIdioma'], 'exist', 'skipOnError' => true, 'targetClass' => Idioma::className(),
                'targetAttribute' => ['IdIdioma' => 'IdIdioma'], 'message' => 'El Idioma que seleccionó no existe en la Base de Datos del Sistema.'
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
            'IdIdioma' => 'Id Idioma',
            'IdEntidad' => 'Id Entidad',
            'Entidad' => 'Entidad',
            'Nivel' => 'Nivel',
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
    public function getIdioma()
    {
        return $this->hasOne(Idioma::className(), ['IdIdioma' => 'IdIdioma']);
    }

    static public function search($params)
    {
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if (isset($search)) {
            $params = $search;
        }


        $query = DetalleEntidad::find()
            ->select(['{{detalle_entidad}}.*', 'Idioma'])            
            ->leftJoin('idioma', '`detalle_entidad`.`IdIdioma` = `idioma`.`IdIdioma`')
            ->asArray(true);


        if (isset($params['IdRecurso'])) {
            $query->andFilterWhere(['IdRecurso' => $params['IdRecurso']]);
        }
        if (isset($params['IdIdioma'])) {
            $query->andFilterWhere(['detalle_entidad.IdIdioma' => $params['IdIdioma']]);
        }
        if (isset($params['IdEntidad'])) {
            $query->andFilterWhere(['IdEntidad' => $params['IdEntidad']]);
        }        
        if (isset($params['Entidad'])) {
            $query->andFilterWhere(['like', 'Entidad', $params['Entidad']]);
        }
        if (isset($params['Nivel'])) {
            $query->andFilterWhere(['Nivel' => $params['Nivel']]);
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
