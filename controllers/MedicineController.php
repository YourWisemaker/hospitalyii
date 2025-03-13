<?php

namespace app\controllers;

use Yii;
use app\models\Medicine;
use yii\data\ActiveDataProvider;
use app\controllers\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * MedicineController implements the CRUD actions for Medicine model.
 */
class MedicineController extends BaseController
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
                            'roles' => ['admin', 'pharmacist'],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Medicine models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Medicine::find(),
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ]
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Medicine model.
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
     * Creates a new Medicine model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Medicine();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Obat berhasil ditambahkan.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Medicine model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Obat berhasil diperbarui.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Medicine model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Check if medicine has been used in medical records
        if ($model->getMedicineDetails()->exists()) {
            Yii::$app->session->setFlash('error', 'Obat tidak dapat dihapus karena telah digunakan dalam rekam medis.');
            return $this->redirect(['index']);
        }
        
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Obat berhasil dihapus.');
        } else {
            Yii::$app->session->setFlash('error', 'Gagal menghapus obat.');
        }

        return $this->redirect(['index']);
    }
    
    /**
     * Action to update stock of medicine
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionStock($id)
    {
        $model = $this->findModel($id);
        
        if ($this->request->isPost) {
            $quantity = (int)Yii::$app->request->post('quantity', 0);
            
            if ($quantity > 0) {
                $model->stock += $quantity;
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', "Stok obat {$model->name} berhasil ditambahkan sebanyak {$quantity}.");
                } else {
                    Yii::$app->session->setFlash('error', 'Gagal menambahkan stok obat.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Jumlah stok yang ditambahkan harus lebih dari 0.');
            }
            
            return $this->redirect(['view', 'id' => $model->id]);
        }
        
        return $this->render('stock', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Medicine model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Medicine the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Medicine::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang diminta tidak ditemukan.');
    }
}
