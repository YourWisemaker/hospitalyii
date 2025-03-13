<?php

namespace app\controllers;

use Yii;
use app\models\Payment;
use app\models\MedicalRecord;
use yii\data\ActiveDataProvider;
use app\controllers\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * PaymentController implements the CRUD actions for Payment model.
 */
class PaymentController extends BaseController
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
                            'roles' => ['admin', 'receptionist'],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Payment models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Payment::find()
                ->with(['medicalRecord', 'medicalRecord.patient'])
                ->orderBy(['payment_date' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Payment model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Payment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Payment();
        $model->payment_date = date('Y-m-d');

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Pembayaran berhasil dibuat.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        // Get list of medical records that need payment
        $medicalRecords = MedicalRecord::find()
            ->where(['status' => MedicalRecord::STATUS_ONGOING])
            ->orWhere(['status' => MedicalRecord::STATUS_WAITING_PAYMENT])
            ->with('patient')
            ->orderBy(['treatment_date' => SORT_DESC])
            ->all();
            
        $medicalRecordOptions = [];
        foreach ($medicalRecords as $record) {
            $patientName = $record->patient ? $record->patient->name : 'Unknown';
            $medicalRecordOptions[$record->id] = "#{$record->id} - {$patientName} - " . Yii::$app->formatter->asDate($record->treatment_date);
        }

        return $this->render('create', [
            'model' => $model,
            'medicalRecordOptions' => $medicalRecordOptions,
            'paymentMethods' => Payment::getPaymentMethodOptions(),
        ]);
    }

    /**
     * Updates an existing Payment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Don't allow updating payments if the medical record is completed
        $medicalRecord = $model->medicalRecord;
        if ($medicalRecord && $medicalRecord->status === MedicalRecord::STATUS_COMPLETED) {
            Yii::$app->session->setFlash('error', 'Pembayaran untuk rekam medis yang sudah selesai tidak dapat diubah.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Pembayaran berhasil diperbarui.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'paymentMethods' => Payment::getPaymentMethodOptions(),
        ]);
    }

    /**
     * Deletes an existing Payment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Don't allow deleting payments if the medical record is completed
        $medicalRecord = $model->medicalRecord;
        if ($medicalRecord && $medicalRecord->status === MedicalRecord::STATUS_COMPLETED) {
            Yii::$app->session->setFlash('error', 'Pembayaran untuk rekam medis yang sudah selesai tidak dapat dihapus.');
            return $this->redirect(['index']);
        }
        
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Pembayaran berhasil dihapus.');
        } else {
            Yii::$app->session->setFlash('error', 'Gagal menghapus pembayaran.');
        }

        return $this->redirect(['index']);
    }
    
    /**
     * Generate payment report
     * @return string
     */
    public function actionReport()
    {
        $startDate = Yii::$app->request->get('start_date', date('Y-m-01')); // Default to first day of current month
        $endDate = Yii::$app->request->get('end_date', date('Y-m-t')); // Default to last day of current month
        
        $query = Payment::find()
            ->with(['medicalRecord', 'medicalRecord.patient'])
            ->where(['status' => Payment::STATUS_PAID])
            ->andWhere(['between', 'payment_date', $startDate, $endDate])
            ->orderBy(['payment_date' => SORT_ASC]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        
        // Calculate totals
        $totalAmount = $query->sum('amount');
        $paymentCount = $query->count();
        
        // Group by payment method
        $paymentsByMethod = ArrayHelper::map(
            $query->select(['payment_method', 'total' => 'SUM(amount)'])
                 ->groupBy('payment_method')
                 ->orderBy(['payment_method' => SORT_ASC])
                 ->asArray()
                 ->all(),
            'payment_method',
            'total'
        );
        
        return $this->render('report', [
            'dataProvider' => $dataProvider,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalAmount' => $totalAmount,
            'paymentCount' => $paymentCount,
            'paymentsByMethod' => $paymentsByMethod,
            'paymentMethods' => Payment::getPaymentMethodOptions(),
        ]);
    }

    /**
     * Finds the Payment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Payment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Payment::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang diminta tidak ditemukan.');
    }
}
