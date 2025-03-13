<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\MedicalRecord;
use app\models\Patient;
use app\models\Medicine;
use app\models\Treatment;
use app\models\Payment;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'index', 'dashboard'],
                'rules' => [
                    [
                        'actions' => ['logout', 'index', 'dashboard'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login']);
        }
        
        return $this->actionDashboard();
    }
    
    /**
     * Displays dashboard with statistics.
     *
     * @return string
     */
    public function actionDashboard()
    {
        $totalPatients = Patient::find()->count();
        $totalMedicines = Medicine::find()->count();
        $totalTreatments = Treatment::find()->count();
        
        // Get today's appointments (medical records)
        $today = date('Y-m-d');
        $todayAppointments = MedicalRecord::find()
            ->where(['between', 'treatment_date', $today . ' 00:00:00', $today . ' 23:59:59'])
            ->count();
        
        // Get pending payments
        $pendingPayments = Payment::find()
            ->where(['status' => Payment::STATUS_PENDING])
            ->count();
        
        // Get total income this month
        $firstDayOfMonth = date('Y-m-01');
        $lastDayOfMonth = date('Y-m-t');
        $totalIncome = Payment::find()
            ->where(['status' => Payment::STATUS_PAID])
            ->andWhere(['between', 'payment_date', $firstDayOfMonth, $lastDayOfMonth])
            ->sum('amount');
        
        // Get recent medical records
        $recentMedicalRecords = MedicalRecord::find()
            ->with(['patient'])
            ->orderBy(['treatment_date' => SORT_DESC])
            ->limit(5)
            ->all();
            
        // Get latest patients
        $latestPatients = Patient::find()
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->all();
            
        // Get medicines with low stock
        $lowStockMedicines = Medicine::find()
            ->where('stock <= 10')
            ->orderBy(['stock' => SORT_ASC])
            ->limit(5)
            ->all();
            
        // Daily patient count for chart (last 7 days)
        $dailyPatientData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $count = MedicalRecord::find()
                ->where(['between', 'treatment_date', $date . ' 00:00:00', $date . ' 23:59:59'])
                ->count();
            $dailyPatientData[date('d M', strtotime($date))] = $count;
        }
        
        // Monthly revenue data (last 6 months)
        $revenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $startDate = date('Y-m-01', strtotime($month));
            $endDate = date('Y-m-t', strtotime($month));
            $revenue = Payment::find()
                ->where(['status' => Payment::STATUS_PAID])
                ->andWhere(['between', 'payment_date', $startDate, $endDate])
                ->sum('amount') ?: 0;
            $revenueData[date('M Y', strtotime($month))] = $revenue;
        }
        
        return $this->render('dashboard', [
            'totalPatients' => $totalPatients,
            'totalMedicines' => $totalMedicines, 
            'totalTreatments' => $totalTreatments,
            'todayAppointments' => $todayAppointments,
            'pendingPayments' => $pendingPayments,
            'totalIncome' => $totalIncome,
            'recentMedicalRecords' => $recentMedicalRecords,
            'latestPatients' => $latestPatients,
            'lowStockMedicines' => $lowStockMedicines,
            'dailyPatientData' => $dailyPatientData,
            'revenueData' => $revenueData,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
