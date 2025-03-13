<?php

namespace app\controllers;

use Yii;
use app\models\MedicalRecord;
use app\models\MedicineDetail;
use app\models\TreatmentDetail;
use app\models\Payment;
use app\models\Patient;
use app\models\Medicine;
use app\models\Treatment;
use app\models\Employee;
use yii\data\ActiveDataProvider;
use app\controllers\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\db\Transaction;

/**
 * MedicalRecordController implements the CRUD actions for MedicalRecord model.
 */
class MedicalRecordController extends BaseController
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
                            'actions' => ['index', 'view', 'create', 'update'],
                            'allow' => true,
                            'roles' => ['admin', 'doctor', 'nurse', 'receptionist'],
                        ],
                        [
                            'actions' => ['delete'],
                            'allow' => true,
                            'roles' => ['admin'],
                        ],
                        [
                            'actions' => ['add-treatment', 'remove-treatment', 'add-medicine', 'remove-medicine'],
                            'allow' => true,
                            'roles' => ['admin', 'doctor', 'nurse'],
                        ],
                        [
                            'actions' => ['payment', 'process-payment'],
                            'allow' => true,
                            'roles' => ['admin', 'receptionist'],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all MedicalRecord models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => MedicalRecord::find()
                ->with(['patient', 'doctor'])
                ->orderBy(['treatment_date' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MedicalRecord model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        // Get treatment details
        $treatmentDetails = new ActiveDataProvider([
            'query' => $model->getTreatmentDetails(),
            'pagination' => false,
        ]);
        
        // Get medicine details
        $medicineDetails = new ActiveDataProvider([
            'query' => $model->getMedicineDetails(),
            'pagination' => false,
        ]);
        
        // Get payment details
        $payments = new ActiveDataProvider([
            'query' => $model->getPayments(),
            'pagination' => false,
        ]);
        
        return $this->render('view', [
            'model' => $model,
            'treatmentDetails' => $treatmentDetails,
            'medicineDetails' => $medicineDetails,
            'payments' => $payments,
        ]);
    }

    /**
     * Creates a new MedicalRecord model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new MedicalRecord();
        
        // Set default values
        $model->treatment_date = date('Y-m-d H:i:s');
        $model->status = MedicalRecord::STATUS_ONGOING;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Rekam Medis berhasil dibuat.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'patients' => ArrayHelper::map(Patient::find()->all(), 'id', 'name', function($model) {
                return $model->registration_number;
            }),
            'doctors' => ArrayHelper::map(Employee::find()->where(['position' => Employee::POSITION_DOCTOR])->all(), 'id', 'name'),
        ]);
    }

    /**
     * Updates an existing MedicalRecord model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        // Don't allow updating completed records
        if ($model->status === MedicalRecord::STATUS_COMPLETED) {
            Yii::$app->session->setFlash('error', 'Rekam Medis yang sudah selesai tidak dapat diubah.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Rekam Medis berhasil diperbarui.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'patients' => ArrayHelper::map(Patient::find()->all(), 'id', 'name', function($model) {
                return $model->registration_number;
            }),
            'doctors' => ArrayHelper::map(Employee::find()->where(['position' => Employee::POSITION_DOCTOR])->all(), 'id', 'name'),
        ]);
    }

    /**
     * Deletes an existing MedicalRecord model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Don't allow deleting completed records
        if ($model->status === MedicalRecord::STATUS_COMPLETED) {
            Yii::$app->session->setFlash('error', 'Rekam Medis yang sudah selesai tidak dapat dihapus.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // Delete all treatment and medicine details first
            $model->unlinkAll('treatmentDetails', true);
            $model->unlinkAll('medicineDetails', true);
            $model->unlinkAll('payments', true);
            
            // Then delete the medical record
            $model->delete();
            
            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Rekam Medis berhasil dihapus.');
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Gagal menghapus Rekam Medis: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }
    
    /**
     * Add a treatment to the medical record
     * @param int $id Medical Record ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionAddTreatment($id)
    {
        $medicalRecord = $this->findModel($id);
        $model = new TreatmentDetail();
        $model->medical_record_id = $id;
        
        // Don't allow adding treatments to completed records
        if ($medicalRecord->status === MedicalRecord::STATUS_COMPLETED) {
            Yii::$app->session->setFlash('error', 'Rekam Medis yang sudah selesai tidak dapat diubah.');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Tindakan berhasil ditambahkan.');
                return $this->redirect(['view', 'id' => $id]);
            }
        }
        
        return $this->render('add-treatment', [
            'model' => $model,
            'medicalRecord' => $medicalRecord,
            'treatments' => ArrayHelper::map(Treatment::find()->all(), 'id', 'name'),
        ]);
    }
    
    /**
     * Remove a treatment from the medical record
     * @param int $id Treatment Detail ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionRemoveTreatment($id)
    {
        $model = TreatmentDetail::findOne($id);
        
        if (!$model) {
            throw new NotFoundHttpException('Tindakan tidak ditemukan.');
        }
        
        $medicalRecordId = $model->medical_record_id;
        $medicalRecord = MedicalRecord::findOne($medicalRecordId);
        
        // Don't allow removing treatments from completed records
        if ($medicalRecord->status === MedicalRecord::STATUS_COMPLETED) {
            Yii::$app->session->setFlash('error', 'Rekam Medis yang sudah selesai tidak dapat diubah.');
            return $this->redirect(['view', 'id' => $medicalRecordId]);
        }
        
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Tindakan berhasil dihapus.');
        } else {
            Yii::$app->session->setFlash('error', 'Gagal menghapus tindakan.');
        }
        
        return $this->redirect(['view', 'id' => $medicalRecordId]);
    }
    
    /**
     * Add a medicine to the medical record
     * @param int $id Medical Record ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionAddMedicine($id)
    {
        $medicalRecord = $this->findModel($id);
        $model = new MedicineDetail();
        $model->medical_record_id = $id;
        
        // Don't allow adding medicines to completed records
        if ($medicalRecord->status === MedicalRecord::STATUS_COMPLETED) {
            Yii::$app->session->setFlash('error', 'Rekam Medis yang sudah selesai tidak dapat diubah.');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Check medicine stock
                $medicine = Medicine::findOne($model->medicine_id);
                
                if ($medicine && $medicine->stock < $model->quantity) {
                    Yii::$app->session->setFlash('error', "Stok obat {$medicine->name} tidak mencukupi. Stok tersedia: {$medicine->stock}");
                } else if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Obat berhasil ditambahkan.');
                    return $this->redirect(['view', 'id' => $id]);
                }
            }
        }
        
        return $this->render('add-medicine', [
            'model' => $model,
            'medicalRecord' => $medicalRecord,
            'medicines' => ArrayHelper::map(Medicine::find()->where(['>', 'stock', 0])->all(), 'id', function($model) {
                return $model->name . ' (Stok: ' . $model->stock . ')';
            }),
        ]);
    }
    
    /**
     * Remove a medicine from the medical record
     * @param int $id Medicine Detail ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionRemoveMedicine($id)
    {
        $model = MedicineDetail::findOne($id);
        
        if (!$model) {
            throw new NotFoundHttpException('Obat tidak ditemukan.');
        }
        
        $medicalRecordId = $model->medical_record_id;
        $medicalRecord = MedicalRecord::findOne($medicalRecordId);
        
        // Don't allow removing medicines from completed records
        if ($medicalRecord->status === MedicalRecord::STATUS_COMPLETED) {
            Yii::$app->session->setFlash('error', 'Rekam Medis yang sudah selesai tidak dapat diubah.');
            return $this->redirect(['view', 'id' => $medicalRecordId]);
        }
        
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Obat berhasil dihapus.');
        } else {
            Yii::$app->session->setFlash('error', 'Gagal menghapus obat.');
        }
        
        return $this->redirect(['view', 'id' => $medicalRecordId]);
    }
    
    /**
     * Process payment for a medical record
     * @param int $id Medical Record ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPayment($id)
    {
        $medicalRecord = $this->findModel($id);
        $model = new Payment();
        $model->medical_record_id = $id;
        $model->payment_date = date('Y-m-d');
        $model->amount = $medicalRecord->getTotalAmount();
        
        // Get existing payments
        $existingPayments = $medicalRecord->getPayments()->all();
        $totalPaid = 0;
        
        foreach ($existingPayments as $payment) {
            $totalPaid += $payment->amount;
        }
        
        $remainingAmount = $medicalRecord->getTotalAmount() - $totalPaid;
        
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Pembayaran berhasil diproses.');
                return $this->redirect(['view', 'id' => $id]);
            }
        }
        
        return $this->render('payment', [
            'model' => $model,
            'medicalRecord' => $medicalRecord,
            'totalPaid' => $totalPaid,
            'remainingAmount' => $remainingAmount,
            'paymentMethods' => Payment::getPaymentMethodOptions(),
        ]);
    }

    /**
     * Finds the MedicalRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return MedicalRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MedicalRecord::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang diminta tidak ditemukan.');
    }
}
