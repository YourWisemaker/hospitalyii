<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\data\ActiveDataProvider;
use app\controllers\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BaseController
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
                            'roles' => ['admin'],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
            'sort' => [
                'defaultOrder' => [
                    'username' => SORT_ASC,
                ],
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
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new User();
        $model->scenario = User::SCENARIO_CREATE;
        
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Pengguna berhasil dibuat.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
            $model->status = User::STATUS_ACTIVE;
        }

        return $this->render('create', [
            'model' => $model,
            'roles' => $this->getRoleList(),
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = User::SCENARIO_UPDATE;
        
        // Don't allow updating admin user if current user is not admin
        if ($model->username === 'admin' && Yii::$app->user->identity->username !== 'admin') {
            throw new ForbiddenHttpException('Anda tidak diizinkan mengubah pengguna admin utama.');
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Pengguna berhasil diperbarui.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'roles' => $this->getRoleList(),
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Don't allow deleting admin user
        if ($model->username === 'admin') {
            Yii::$app->session->setFlash('error', 'Pengguna admin utama tidak dapat dihapus.');
            return $this->redirect(['index']);
        }
        
        // Don't allow deleting current user
        if ($model->id === Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'Anda tidak dapat menghapus akun sendiri.');
            return $this->redirect(['index']);
        }
        
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Pengguna berhasil dihapus.');
        } else {
            Yii::$app->session->setFlash('error', 'Gagal menghapus pengguna.');
        }

        return $this->redirect(['index']);
    }
    
    /**
     * Reset password for a user
     * @param int $id User ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionResetPassword($id)
    {
        $model = $this->findModel($id);
        $model->scenario = User::SCENARIO_RESET_PASSWORD;
        
        // Don't allow resetting admin user password if current user is not admin
        if ($model->username === 'admin' && Yii::$app->user->identity->username !== 'admin') {
            throw new ForbiddenHttpException('Anda tidak diizinkan mengubah kata sandi pengguna admin utama.');
        }
        
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->resetPassword()) {
                Yii::$app->session->setFlash('success', 'Kata sandi berhasil direset.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        
        return $this->render('reset-password', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Halaman yang diminta tidak ditemukan.');
    }
    
    /**
     * Get list of available roles for dropdown
     * @return array
     */
    protected function getRoleList()
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRoles();
        $result = [];
        
        foreach ($roles as $name => $role) {
            $result[$name] = $role->description ?: $name;
        }
        
        return $result;
    }
}
