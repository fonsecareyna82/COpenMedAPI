<?php

namespace backend\controllers;

use app\models\Entidad;
use app\models\DocProfesor;
use app\models\DocEstudiante;
use app\models\Asociacion;
use backend\behaviours\Verbcheck;
use backend\behaviours\Apiauth;

use Yii;

class ReportesController extends RestController
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
                    'count-evaluations-profesor' => ['GET'],
                    'count-evaluations-estudiante' => ['GET']
                ],
            ],

        ];
    }
    
    protected function findProfesorModel($id)
    {
        if (($model = DocProfesor::findOne($id)) !== null) {
            return $model;
        } else {
            Yii::$app->api->sendFailedResponse("El Profesor requerido no existe");
        }
    }

    public  function actionCountEvaluationsProfesor($idprofesor)
    {
        $this->findProfesorModel($idprofesor);
        $response = [];

        $entidadesMal = (new \yii\db\Query())
            ->select([
                '{{entidad}}.*',
                "CONCAT(est.PrimerNombre, ' ', IFNULL(est.SegundoNombre, ''), 
                ' ', est.ApellidoPaterno, ' ', est.ApellidoMaterno) AS Estudiante",
                '(SELECT Entidad FROM detalle_entidad WHERE Entidad.IdEntidad = detalle_entidad.IdEntidad LIMIT 1) AS Entidad',
                '(SELECT IdIdioma FROM detalle_entidad WHERE Entidad.IdEntidad = detalle_entidad.IdEntidad LIMIT 1) AS DetalleIdEntidad',
                '(SELECT idioma FROM Idioma WHERE IdIdioma = DetalleIdEntidad LIMIT 1) AS Idioma',
                '(SELECT TipoEntidad FROM tipo_entidad WHERE IdTipoEntidad = entidad.IdTipoEntidad LIMIT 1) AS TipoEntidad',
            ])
            ->from('doc_profesor_has_doc_grupo, doc_estudiante,entidad, adm_persona AS est')
            ->where('doc_profesor_has_doc_grupo.IdGrupo = doc_estudiante.IdGrupo')
            ->andWhere('entidad.IdEstudiante = doc_estudiante.IdEstudiante')
            ->andWhere('est.IdPersona = doc_estudiante.IdPersona')
            ->andFilterWhere(['doc_profesor_has_doc_grupo.IdProfesor' => $idprofesor])
            ->andWhere('entidad.Evaluacion = 0');

        $additional_info = [            
            'EntidadesBad' => (int) $entidadesMal->count()
        ];
        
        $response[] = $additional_info;

        $entidadesBien = (new \yii\db\Query())
            ->select([
                '{{entidad}}.*',
                "CONCAT(est.PrimerNombre, ' ', IFNULL(est.SegundoNombre, ''), 
                ' ', est.ApellidoPaterno, ' ', est.ApellidoMaterno) AS Estudiante",
                '(SELECT Entidad FROM detalle_entidad WHERE Entidad.IdEntidad = detalle_entidad.IdEntidad LIMIT 1) AS Entidad',
                '(SELECT IdIdioma FROM detalle_entidad WHERE Entidad.IdEntidad = detalle_entidad.IdEntidad LIMIT 1) AS DetalleIdEntidad',
                '(SELECT idioma FROM Idioma WHERE IdIdioma = DetalleIdEntidad LIMIT 1) AS Idioma',
                '(SELECT TipoEntidad FROM tipo_entidad WHERE IdTipoEntidad = entidad.IdTipoEntidad LIMIT 1) AS TipoEntidad',
            ])
            ->from('doc_profesor_has_doc_grupo, doc_estudiante,entidad, adm_persona AS est')
            ->where('doc_profesor_has_doc_grupo.IdGrupo = doc_estudiante.IdGrupo')
            ->andWhere('entidad.IdEstudiante = doc_estudiante.IdEstudiante')
            ->andWhere('est.IdPersona = doc_estudiante.IdPersona')
            ->andFilterWhere(['doc_profesor_has_doc_grupo.IdProfesor' => $idprofesor])
            ->andWhere('entidad.Evaluacion = 1');

        $additional_info = [            
            'EntidadesOK' => (int) $entidadesBien->count()
        ];

        $response[] = $additional_info;

        $asociacionesMal = (new \yii\db\Query())
            ->select([
                '{{asociacion}}.*',
                "CONCAT(est.PrimerNombre, ' ', IFNULL(est.SegundoNombre, ''), 
                ' ', est.ApellidoPaterno, ' ', est.ApellidoMaterno) AS Estudiante",
                '(SELECT Asociacion FROM detalle_asociacion WHERE Asociacion.IdAsociacion = detalle_asociacion.IdAsociacion LIMIT 1) AS Asociacion',
                '(SELECT IdIdioma FROM detalle_asociacion WHERE Asociacion.IdAsociacion = detalle_asociacion.IdAsociacion LIMIT 1) AS DetalleIdAsociacion',
                '(SELECT idioma FROM Idioma WHERE IdIdioma = DetalleIdAsociacion LIMIT 1) AS Idioma',
                '(SELECT TipoAsociacion FROM tipo_asociacion WHERE IdTipoAsociacion = asociacion.IdTipoAsociacion LIMIT 1) AS TipoAsociacion',
            ])
            ->from('doc_profesor_has_doc_grupo, doc_estudiante,asociacion, adm_persona AS est')
            ->where('doc_profesor_has_doc_grupo.IdGrupo = doc_estudiante.IdGrupo')
            ->andWhere('asociacion.IdEstudiante = doc_estudiante.IdEstudiante')
            ->andWhere('est.IdPersona = doc_estudiante.IdPersona')
            ->andFilterWhere(['doc_profesor_has_doc_grupo.IdProfesor' => $idprofesor])
            ->andWhere('asociacion.Evaluacion = 0');

        $additional_info = [            
            'AsociacionesBad' => (int) $asociacionesMal->count()
        ];
        
        $response[] = $additional_info;

        $asociacionesBien = (new \yii\db\Query())
            ->select([
                '{{asociacion}}.*',
                "CONCAT(est.PrimerNombre, ' ', IFNULL(est.SegundoNombre, ''), 
                ' ', est.ApellidoPaterno, ' ', est.ApellidoMaterno) AS Estudiante",
                '(SELECT Asociacion FROM detalle_asociacion WHERE Asociacion.IdAsociacion = detalle_asociacion.IdAsociacion LIMIT 1) AS Asociacion',
                '(SELECT IdIdioma FROM detalle_asociacion WHERE Asociacion.IdAsociacion = detalle_asociacion.IdAsociacion LIMIT 1) AS DetalleIdAsociacion',
                '(SELECT idioma FROM Idioma WHERE IdIdioma = DetalleIdAsociacion LIMIT 1) AS Idioma',
                '(SELECT TipoAsociacion FROM tipo_asociacion WHERE IdTipoAsociacion = asociacion.IdTipoAsociacion LIMIT 1) AS TipoAsociacion',
            ])
            ->from('doc_profesor_has_doc_grupo, doc_estudiante,asociacion, adm_persona AS est')
            ->where('doc_profesor_has_doc_grupo.IdGrupo = doc_estudiante.IdGrupo')
            ->andWhere('asociacion.IdEstudiante = doc_estudiante.IdEstudiante')
            ->andWhere('est.IdPersona = doc_estudiante.IdPersona')
            ->andFilterWhere(['doc_profesor_has_doc_grupo.IdProfesor' => $idprofesor])
            ->andWhere('asociacion.Evaluacion = 1');

        $additional_info = [            
            'AsociacionesOK' => (int) $asociacionesBien->count()
        ];

        $response[] = $additional_info;

        Yii::$app->api->sendSuccessResponse($response);
    }

    protected function findEstudianteModel($id)
    {
        if (($model = DocEstudiante::findOne($id)) !== null) {
            return $model;
        } else {
            Yii::$app->api->sendFailedResponse("El Estudiante requerido no existe");
        }
    }

    public  function actionCountEvaluationsEstudiante($idestudiante)
    {
        $this->findEstudianteModel($idestudiante);
        $response = [];
        $entidadesMal = Entidad::find()
        ->select(['entidad.IdEntidad'])
        ->where('Evaluacion = 0')
        ->andFilterWhere(['IdEstudiante' => $idestudiante]);

        $additional_info = [            
            'EntidadesBad' => (int) $entidadesMal->count()
        ];
        
        $response[] = $additional_info;

        $entidadesBien = Entidad::find()
        ->select(['entidad.IdEntidad'])
        ->where('Evaluacion = 1')
        ->andFilterWhere(['IdEstudiante' => $idestudiante]);

        $additional_info = [            
            'EntidadesOK' => (int) $entidadesBien->count()
        ];

        $response[] = $additional_info;

        $asociacionesMal = Asociacion::find()
        ->select(['asociacion.IdAsociacion'])
        ->where('Evaluacion = 0')
        ->andFilterWhere(['IdEstudiante' => $idestudiante]);

        $additional_info = [            
            'AsociacionesBad' => (int) $asociacionesMal->count()
        ];
        
        $response[] = $additional_info;

        $asociacionesBien = Asociacion::find()
        ->select(['asociacion.IdAsociacion'])
        ->where('Evaluacion = 1')
        ->andFilterWhere(['IdEstudiante' => $idestudiante]);

        $additional_info = [            
            'AsociacionesOK' => (int) $asociacionesBien->count()
        ];

        $response[] = $additional_info;

        Yii::$app->api->sendSuccessResponse($response);

    }
}
