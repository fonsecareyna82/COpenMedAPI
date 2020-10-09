<?php

namespace backend\controllers;

use app\models\DocProfesor;
use app\models\DocGrupo;
use backend\behaviours\Verbcheck;
use backend\behaviours\Apiauth;

use Yii;
use app\models\DocProfesorHasDocGrupo;
use app\models\DocEspecialidad;
use app\models\DocProfesorHasDocEspecialidad;

class DocProfesorController extends RestController
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
                    'especialidades' => ['GET'],
                    'unsolicited-especialidades' => ['GET'],
                    'create-especialidad' => ['POST'],
                    'delete-especialidad' => ['DELETE'],
                    'grupos' => ['GET'],
                    'unsolicited-groups' => ['GET'],
                    'create-grupo' => ['POST'],
                    'delete-grupo' => ['DELETE'],
                ],
            ],

        ];
    }

    public function actionIndex()
    {
        $params = $this->request['search'];
        $response = DocProfesor::search($params);
        Yii::$app->api->sendSuccessResponse($response['data'], $response['info']);
    }

    public function actionCreate()
    {
        $model = new DocProfesor;
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
        $persona = $model->getPersona()->select(["CONCAT(
            PrimerNombre, ' ', IFNULL(SegundoNombre, ''), ' ', 
            ApellidoPaterno, ' ', ApellidoMaterno) AS NombreCompleto"])->asArray(true)->one();
        $response = array_merge($model->attributes, $persona);        
        Yii::$app->api->sendSuccessResponse($response);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->api->sendSuccessResponse($model->attributes);
    }

    protected function findModel($id)
    {
        if (($model = DocProfesor::findOne($id)) !== null) {
            return $model;
        } else {
            Yii::$app->api->sendFailedResponse("El Registro requerido no existe");
        }
    }

    // Especialidad - Profesor
    public  function actionEspecialidades($id)
    {
        $model = $this->findModel($id);
        $especialidades = $model->getEspecialidads()->asArray(true)->all();
        Yii::$app->api->sendSuccessResponse($especialidades);
    }

    public function actionUnsolicitedEspecialidades($id)
    {
        $model = $this->findModel($id);
        $profEsp = $model->getEspecialidads()->asArray(true)->all();
        $especialidades = DocEspecialidad::find()->asArray(true)->all();

        $unsEsp = [];
        foreach ($especialidades as $especialidad) {
            if (!in_array($especialidad, $profEsp)) {
                $unsEsp[] = $especialidad;
            }
        }

        Yii::$app->api->sendSuccessResponse($unsEsp);
    }

    public function actionCreateEspecialidad()
    {
        $model = new DocProfesorHasDocEspecialidad();
        $model->attributes = $this->request;

        if ($model->save()) {
            Yii::$app->api->sendSuccessResponse($model->attributes);
        } else {
            Yii::$app->api->sendFailedResponse($model->errors);
        }
    }    

    public function actionDeleteEspecialidad($idprofesor, $idespecialidad)
    {
        $model = $this->findModelEspecialidad($idprofesor, $idespecialidad);
        $model->delete();
        Yii::$app->api->sendSuccessResponse($model->attributes);
    }

    protected function findModelEspecialidad($idProfesor, $idespecialidad)
    {
        $model = DocProfesorHasDocEspecialidad::find()->where(['IdProfesor' => $idProfesor])->andWhere(['IdEspecialidad' => $idespecialidad])->one();
        if ($model !== null) {
            return $model;
        } else {
            Yii::$app->api->sendFailedResponse("El Registro requerido no existe");
        }
    }

    // Grupos - Profesor
    public  function actionGrupos($id)
    {
        $model = $this->findModel($id);
        $grupos = $model->getGrupos()->asArray(true)->all();
        Yii::$app->api->sendSuccessResponse($grupos);
    }

    public function actionUnsolicitedGroups($id)
    {
        $model = $this->findModel($id);
        $profGrupos = $model->getGrupos()->asArray(true)->all();
        $groups = DocGrupo::find()->asArray(true)->all();

        $unsGroup = [];
        foreach ($groups as $grupo) {
            if (!in_array($grupo, $profGrupos)) {
                $unsGroup[] = $grupo;
            }
        }

        Yii::$app->api->sendSuccessResponse($unsGroup);
    }

    public function actionCreateGrupo()
    {
        $model = new DocProfesorHasDocGrupo;
        $model->attributes = $this->request;

        if ($model->save()) {
            Yii::$app->api->sendSuccessResponse($model->attributes);
        } else {
            Yii::$app->api->sendFailedResponse($model->errors);
        }
    }    

    public function actionDeleteGrupo($idprofesor, $idgrupo)
    {
        $model = $this->findModelGrupo($idprofesor, $idgrupo);
        $model->delete();
        Yii::$app->api->sendSuccessResponse($model->attributes);
    }

    protected function findModelGrupo($idProfesor, $idgrupo)
    {
        $model = DocProfesorHasDocGrupo::find()->where(['IdProfesor' => $idProfesor])->andWhere(['IdGrupo' => $idgrupo])->one();
        if ($model !== null) {
            return $model;
        } else {
            Yii::$app->api->sendFailedResponse("El Registro requerido no existe");
        }
    }
}
