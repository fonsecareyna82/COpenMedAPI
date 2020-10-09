<?php

namespace backend\controllers;

use app\models\Entidad;
use app\models\DocProfesor;
use backend\behaviours\Verbcheck;
use backend\behaviours\Apiauth;

use Yii;

class EntidadController extends RestController
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
                    'view-detalles' => ['GET'],
                    'profesor-evaluations' => ['GET'],
                    'delete' => ['DELETE']
                ],
            ],

        ];
    }

    public function actionIndex()
    {
        $params = $this->request['search'];
        $response = Entidad::search($params);
        Yii::$app->api->sendSuccessResponse($response['data'], $response['info']);
    }

    public function actionCreate()
    {
        $model = new Entidad;
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

    public function actionViewDetalles($id)
    {
        $model = $this->findModel($id);
        $detalles = $model->getDetalleEntidads()
            ->select(['{{detalle_entidad}}.*', 'Idioma'])
            ->leftJoin('idioma', '`detalle_entidad`.`IdIdioma` = `idioma`.`IdIdioma`')
            ->asArray(true);
        $additional_info = [
            'page' => 'No Define',
            'size' => 'No Define',
            'totalCount' => (int) $detalles->count()
        ];

        $response = [
            'data' => $detalles->all(),
            'info' => $additional_info
        ];
        Yii::$app->api->sendSuccessResponse($response['data'], $response['info']);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->api->sendSuccessResponse($model->attributes);
    }

    protected function findModel($id)
    {
        if (($model = Entidad::findOne($id)) !== null) {
            return $model;
        } else {
            Yii::$app->api->sendFailedResponse("El Registro requerido no existe");
        }
    }

    protected function findProfesorModel($id)
    {
        if (($model = DocProfesor::findOne($id)) !== null) {
            return $model;
        } else {
            Yii::$app->api->sendFailedResponse("El Profesor requerido no existe");
        }
    }

    public  function actionProfesorEvaluations($idprofesor, $estado)
    {
        $this->findProfesorModel($idprofesor);
        $model = (new \yii\db\Query())
            ->select([
                '{{entidad}}.*',
                "CONCAT(est.PrimerNombre, ' ', IFNULL(est.SegundoNombre, ''), 
                ' ', est.ApellidoPaterno, ' ', est.ApellidoMaterno) AS Estudiante",
                '(SELECT Entidad FROM detalle_entidad WHERE entidad.IdEntidad = detalle_entidad.IdEntidad LIMIT 1) AS Entidad',
                '(SELECT IdIdioma FROM detalle_entidad WHERE entidad.IdEntidad = detalle_entidad.IdEntidad LIMIT 1) AS DetalleIdEntidad',
                '(SELECT idioma FROM idioma WHERE IdIdioma = DetalleIdEntidad LIMIT 1) AS Idioma',
                '(SELECT COUNT(IdAsociacion)
                FROM asociacion
                WHERE ((asociacion.IdEntidad1 = IdEntidad AND (SELECT Estado FROM entidad WHERE entidad.IdEntidad = asociacion.IdEntidad2 LIMIT 1) = 1))
                AND asociacion.Estado = 0) as countAsociaciones',
                '(SELECT TipoEntidad FROM tipo_entidad WHERE IdTipoEntidad = entidad.IdTipoEntidad LIMIT 1) AS TipoEntidad',
            ])
            ->from('doc_profesor_has_doc_grupo, doc_estudiante,entidad, adm_persona AS est')
            ->where('doc_profesor_has_doc_grupo.IdGrupo = doc_estudiante.IdGrupo')
            ->andWhere('entidad.IdEstudiante = doc_estudiante.IdEstudiante')
            ->andWhere('est.IdPersona = doc_estudiante.IdPersona')
            ->andFilterWhere(['doc_profesor_has_doc_grupo.IdProfesor' => $idprofesor])
            ->andFilterWhere(['entidad.Estado' => $estado])
            ->orderBy('countAsociaciones DESC');
          

        $additional_info = [
            'page' => 'No Define',
            'size' => 'No Define',
            'totalCount' => (int) $model->count()
        ];

        $response = [
            'data' => $model->all(),
            'info' => $additional_info
        ];
        Yii::$app->api->sendSuccessResponse($response['data'], $response['info']);
    }    
 
}
