<?php

namespace backend\controllers;

use app\models\TipoAsociacion;
use backend\behaviours\Verbcheck;
use backend\behaviours\Apiauth;

use Yii;

class TipoAsociacionController extends RestController
{

    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [

            'apiauth' => [
                'class' => Apiauth::className(),
                'exclude' => [],
                'callback' => []
            ],
            'verbs' => [
                'class' => Verbcheck::className(),
                'actions' => [
                    'index' => ['GET', 'POST'],
                    'create' => ['POST'],
                    'update' => ['PUT'],
                    'view' => ['GET'],
                    'delete' => ['DELETE'],
                    'relationship' => ['GET'],
                ],
            ],

        ];
    }

    public function actionIndex()
    {
        $params = $this->request['search'];
        $response = TipoAsociacion::search($params);
        Yii::$app->api->sendSuccessResponse($response['data'], $response['info']);
    }

    public function actionCreate()
    {
        $model = new TipoAsociacion;
        $model->attributes = $this->request;

        if ($model->save()) {
            Yii::$app->api->sendSuccessResponse($model->attributes);
        } else {
            Yii::$app->api->sendFailedResponse($model->errors);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->attributes = $this->request;

        if ($model->save()) {
            Yii::$app->api->sendSuccessResponse($model->attributes);
        } else {
            Yii::$app->api->sendFailedResponse($model->errors);
        }
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        Yii::$app->api->sendSuccessResponse($model->attributes);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->api->sendSuccessResponse($model->attributes);
    }

    protected function findModel($id)
    {
        if (($model = TipoAsociacion::findOne($id)) !== null) {
            return $model;
        } else {
            Yii::$app->api->sendFailedResponse("El Registro requerido no existe");
        }
    }

    public function actionRelationship($idEntidad1, $idEntidad2)
    {
        $query = TipoAsociacion::find()
            ->select([
                '{{tipo_asociacion}}.*',
                'tent1.TipoEntidad AS TipoEntidad1',
                'tent2.TipoEntidad AS TipoEntidad2'
            ])
            ->leftJoin('tipo_entidad AS tent1', 'tipo_asociacion.`IdTipoEntidad1` = tent1.`IdTipoEntidad`')
            ->leftJoin('tipo_entidad AS tent2', 'tipo_asociacion.`IdTipoEntidad2` = tent2.`IdTipoEntidad`')
            ->andWhere("
                (((tipo_asociacion.IdTipoEntidad1 = (SELECT entidad.IdTipoEntidad FROM entidad WHERE entidad.IdEntidad = " . $idEntidad1 . " LIMIT 1) 
                    AND tipo_asociacion.IdTipoEntidad2 = (SELECT entidad.IdTipoEntidad FROM entidad WHERE entidad.IdEntidad = " . $idEntidad2 . " LIMIT 1) ) 
               ))")
            ->asArray(true);
            
        $additional_info = [
            'page' => 'No Define',
            'size' => 'No Define',
            'totalCount' => (int) $query->count()
        ];

        $response = [
            'data' => $query->all(),
            'info' => $additional_info
        ];
        Yii::$app->api->sendSuccessResponse($response['data'], $response['info']);
    }
}
