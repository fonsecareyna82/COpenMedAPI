<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use \common\models\User;

/**
 * This is the model class for table "seg_rol".
 *
 * @property int $IdRol
 * @property string $Rol
 *
 * @property SegUsuario[] $segUsuarios
 */
class SegRol extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'seg_rol';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Rol'], 'required'],
            [['Rol'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdRol' => 'Id Rol',
            'Rol' => 'Rol',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSegUsuarios()
    {
        return $this->hasMany(User::className(), ['IdRol' => 'IdRol']);
    }

    static public function search($params)
    {

        //$page = Yii::$app->getRequest()->getQueryParam('page');
        //$limit = Yii::$app->getRequest()->getQueryParam('pageSize');
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if(isset($search)){
            $params=$search;
        }



        //$limit = isset($limit) ? $limit : 10;
        //$page = isset($page) ? $page : 1;


        //$offset = ($page - 1) * $limit;

        $query = SegRol::find()
            ->select(['IdRol', 'Rol'])
            ->asArray(true);
            //->limit($limit)
            //->offset($offset);

        if(isset($params['IdRol'])) {
            $query->andFilterWhere(['IdRol' => $params['IdRol']]);
        }        
        if(isset($params['Rol'])) {
            $query->andFilterWhere(['like', 'Rol', $params['Rol']]);
        }

        
        if(isset($order)){
            $query->orderBy($order);
        }
        

        $additional_info = [
            'page' => 'No Define',
            'size' => 'No Define',
            'totalCount' => (int)$query->count()
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];

        return $query;
        
    }
}
