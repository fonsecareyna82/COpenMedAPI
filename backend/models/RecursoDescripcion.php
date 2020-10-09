<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "recurso_descripcion".
 *
 * @property int $IdRecursoDescripcion
 * @property int $IdRecurso
 * @property int $IdIdioma
 * @property string $Descripcion
 *
 * @property Idioma $idioma
 * @property Recurso $recurso
 */
class RecursoDescripcion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'recurso_descripcion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdRecurso', 'IdIdioma', 'Descripcion'], 'required'],
            [['IdRecurso', 'IdIdioma'], 'integer'],
            [['Descripcion'], 'string'],
            [
                ['IdIdioma'], 'exist', 'skipOnError' => true, 'targetClass' => Idioma::className(),
                'targetAttribute' => ['IdIdioma' => 'IdIdioma'], 'message' => 'La Entidad que seleccionó no existe en la Base de Datos del Sistema.'
            ],
            [
                ['IdRecurso'], 'exist', 'skipOnError' => true, 'targetClass' => Recurso::className(),
                'targetAttribute' => ['IdRecurso' => 'IdRecurso'], 'message' => 'La Entidad que seleccionó no existe en la Base de Datos del Sistema.'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdRecursoDescripcion' => 'Id Recurso Descripcion',
            'IdRecurso' => 'Id Recurso',
            'IdIdioma' => 'Id Idioma',
            'Descripcion' => 'Descripcion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdioma()
    {
        return $this->hasOne(Idioma::className(), ['IdIdioma' => 'IdIdioma']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecurso()
    {
        return $this->hasOne(Recurso::className(), ['IdRecurso' => 'IdRecurso']);
    }

    static public function search($params)
    {
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if (isset($search)) {
            $params = $search;
        }


        $query = RecursoDescripcion::find()
            ->select(['{{recurso_descripcion}}.*', 'Idioma'])
            ->leftJoin('idioma', '`recurso_descripcion`.`IdIdioma` = `idioma`.`IdIdioma`')
            ->asArray(true);


        if (isset($params['IdRecursoDescripcion'])) {
            $query->andFilterWhere(['IdRecursoDescripcion' => $params['IdRecursoDescripcion']]);
        }
        if (isset($params['IdRecurso'])) {
            $query->andFilterWhere(['IdRecurso' => $params['IdRecurso']]);
        }
        if (isset($params['IdIdioma'])) {
            $query->andFilterWhere(['recurso_descripcion.IdIdioma' => $params['IdIdioma']]);
        }
        if (isset($params['Descripcion'])) {
            $query->andFilterWhere(['like', 'Descripcion', $params['Descripcion']]);
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
