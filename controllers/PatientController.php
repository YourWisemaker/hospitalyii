<?php

namespace app\controllers;

use Yii;
use app\models\Patient;
use app\models\MedicalRecord;
use yii\data\ActiveDataProvider;
use app\controllers\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * PatientController implements the CRUD actions for Patient model.
 */
class PatientController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['admin', 'receptionist', 'doctor', 'nurse'],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Patient models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Patient::find(),
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ]
            ],
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Patient model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        // Get patient's medical records
        $medicalRecords = new ActiveDataProvider([
            'query' => MedicalRecord::find()
                ->where(['patient_id' => $id])
                ->orderBy(['treatment_date' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);
        
        return $this->render('view', [
            'model' => $model,
            'medicalRecords' => $medicalRecords,
        ]);
    }

    /**
     * Creates a new Patient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Patient();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Debug: log validation errors if save fails
                if (!$model->save()) {
                    Yii::$app->session->setFlash('error', 'Error saving patient: ' . json_encode($model->errors));
                    Yii::error('Patient validation errors: ' . json_encode($model->errors), 'app\controllers\PatientController');
                } else {
                    Yii::$app->session->setFlash('success', 'Pasien berhasil ditambahkan.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } else {
                Yii::$app->session->setFlash('error', 'Error loading patient data from form');
            }
        } else {
            $model->loadDefaultValues();
            
            // Generate unique patient number
            $lastPatient = Patient::find()->orderBy(['id' => SORT_DESC])->one();
            $nextId = $lastPatient ? $lastPatient->id + 1 : 1;
            $model->registration_number = 'P' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            
            // Set default registration date to today if not already set
            if (empty($model->registration_date)) {
                $model->registration_date = date('Y-m-d');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Patient model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Debug: log validation errors if save fails
                if (!$model->save()) {
                    Yii::$app->session->setFlash('error', 'Error saving patient: ' . json_encode($model->errors));
                    Yii::error('Patient validation errors: ' . json_encode($model->errors), 'app\controllers\PatientController');
                } else {
                    Yii::$app->session->setFlash('success', 'Pasien berhasil diperbarui.');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } else {
                Yii::$app->session->setFlash('error', 'Error loading patient data from form');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Patient model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Check if patient has medical records
        if ($model->getMedicalRecords()->exists()) {
            Yii::$app->session->setFlash('error', 'Pasien tidak dapat dihapus karena memiliki rekam medis terkait.');
            return $this->redirect(['index']);
        }
        
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Pasien berhasil dihapus.');
        } else {
            Yii::$app->session->setFlash('error', 'Gagal menghapus pasien.');
        }

        return $this->redirect(['index']);
    }
    
    /**
     * Search for patients
     * @return string|\yii\web\Response
     */
    public function actionSearch()
    {
        $query = Patient::find();
        
        $searchTerm = Yii::$app->request->get('search', '');
        
        if ($searchTerm) {
            $query->andFilterWhere(['or',
                ['like', 'name', $searchTerm],
                ['like', 'registration_number', $searchTerm],
                ['like', 'contact', $searchTerm],
            ]);
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ]
            ],
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);
        
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchTerm' => $searchTerm,
        ]);
    }

    /**
     * Finds the Patient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Patient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Patient::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang diminta tidak ditemukan.');
    }
}
