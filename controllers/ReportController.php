<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\models\Patient;
use app\models\MedicalRecord;
use app\models\Payment;
use app\models\Medicine;
use app\models\Treatment;
use app\models\Employee;
use app\models\base\BaseModel;

/**
 * ReportController implements various report generation actions.
 */
class ReportController extends \app\controllers\base\BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'patient', 'medical-record', 'payment', 'medicine', 'treatment', 'employee'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Displays the report index page
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Generates patient report
     */
    public function actionPatient()
    {
        $startDate = Yii::$app->request->get('start_date', date('Y-m-01'));
        $endDate = Yii::$app->request->get('end_date', date('Y-m-t'));
        
        // Total patients count
        $totalPatients = Patient::find()->count();
        
        // New patients within date range
        $newPatients = Patient::find()
            ->where(['between', 'created_at', strtotime($startDate), strtotime($endDate . ' 23:59:59')])
            ->count();
        
        // Patients by gender
        $patientsByGender = Patient::find()
            ->select(['gender', 'COUNT(*) as count'])
            ->groupBy(['gender'])
            ->asArray()
            ->all();
        
        // Patients by age group (0-10, 11-20, 21-30, etc.)
        $currentYear = date('Y');
        $ageGroupData = [];
        $patients = Patient::find()->all();
        foreach ($patients as $patient) {
            if ($patient->birth_date) {
                $birthYear = date('Y', strtotime($patient->birth_date));
                $age = $currentYear - $birthYear;
                $ageGroup = floor($age / 10) * 10 . '-' . (floor($age / 10) * 10 + 9);
                if (isset($ageGroupData[$ageGroup])) {
                    $ageGroupData[$ageGroup]++;
                } else {
                    $ageGroupData[$ageGroup] = 1;
                }
            }
        }
        
        // Region distribution
        $patientsByRegion = Patient::find()
            ->select(['r.name as region_name', 'COUNT(*) as count'])
            ->join('LEFT JOIN', 'region r', 'patient.region_id = r.id')
            ->groupBy(['region_name'])
            ->asArray()
            ->all();
        
        // Patients with dataProvider for grid view
        $patientsProvider = new ActiveDataProvider([
            'query' => Patient::find()
                ->where(['between', 'created_at', strtotime($startDate), strtotime($endDate . ' 23:59:59')]),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);
        
        return $this->render('patient', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalPatients' => $totalPatients,
            'newPatients' => $newPatients,
            'patientsByGender' => $patientsByGender,
            'ageGroupData' => $ageGroupData,
            'patientsByRegion' => $patientsByRegion,
            'patientsProvider' => $patientsProvider,
        ]);
    }

    /**
     * Generates medical record report
     */
    public function actionMedicalRecord()
    {
        $startDate = Yii::$app->request->get('start_date', date('Y-m-01'));
        $endDate = Yii::$app->request->get('end_date', date('Y-m-t'));
        
        // Total medical records count
        $totalRecords = MedicalRecord::find()->count();
        
        // Medical records within date range
        $periodRecords = MedicalRecord::find()
            ->where(['between', 'treatment_date', $startDate, $endDate . ' 23:59:59'])
            ->count();
        
        // Records by status
        $recordsByStatus = MedicalRecord::find()
            ->select(['status', 'COUNT(*) as count'])
            ->where(['between', 'treatment_date', $startDate, $endDate . ' 23:59:59'])
            ->groupBy(['status'])
            ->asArray()
            ->all();
        
        // Records by payment status
        $recordsByPaymentStatus = MedicalRecord::find()
            ->select(['payment_status', 'COUNT(*) as count'])
            ->where(['between', 'treatment_date', $startDate, $endDate . ' 23:59:59'])
            ->groupBy(['payment_status'])
            ->asArray()
            ->all();
        
        // Daily records trend
        $dailyRecords = MedicalRecord::find()
            ->select(['DATE(treatment_date) as date', 'COUNT(*) as count'])
            ->where(['between', 'treatment_date', $startDate, $endDate . ' 23:59:59'])
            ->groupBy(['DATE(treatment_date)'])
            ->asArray()
            ->all();
        
        $dailyRecordsData = [];
        foreach ($dailyRecords as $record) {
            $dailyRecordsData[$record['date']] = (int)$record['count'];
        }
        
        // Medical records with dataProvider for grid view
        $recordsProvider = new ActiveDataProvider([
            'query' => MedicalRecord::find()
                ->with(['patient'])
                ->where(['between', 'treatment_date', $startDate, $endDate . ' 23:59:59']),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'treatment_date' => SORT_DESC,
                ]
            ],
        ]);
        
        return $this->render('medical-record', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalRecords' => $totalRecords,
            'periodRecords' => $periodRecords,
            'recordsByStatus' => $recordsByStatus,
            'recordsByPaymentStatus' => $recordsByPaymentStatus,
            'dailyRecordsData' => $dailyRecordsData,
            'recordsProvider' => $recordsProvider,
        ]);
    }

    /**
     * Generates payment report
     */
    public function actionPayment()
    {
        $startDate = Yii::$app->request->get('start_date', date('Y-m-01'));
        $endDate = Yii::$app->request->get('end_date', date('Y-m-t'));
        
        // Total revenue for the period
        $totalRevenue = Payment::find()
            ->where(['between', 'payment_date', $startDate, $endDate . ' 23:59:59'])
            ->sum('amount');
        
        // Revenue by payment method
        $revenueByMethod = Payment::find()
            ->select(['payment_method', 'SUM(amount) as total'])
            ->where(['between', 'payment_date', $startDate, $endDate . ' 23:59:59'])
            ->andWhere(['status' => Payment::STATUS_PAID])
            ->groupBy(['payment_method'])
            ->orderBy(['payment_method' => SORT_ASC])
            ->asArray()
            ->all();
        
        // Daily revenue trend
        $dailyRevenue = Payment::find()
            ->select(['DATE(payment_date) as date', 'SUM(amount) as total'])
            ->where(['between', 'payment_date', $startDate, $endDate . ' 23:59:59'])
            ->groupBy(['DATE(payment_date)'])
            ->asArray()
            ->all();
        
        $dailyRevenueData = [];
        foreach ($dailyRevenue as $revenue) {
            $dailyRevenueData[$revenue['date']] = (float)$revenue['total'];
        }
        
        // Monthly revenue trend - last 12 months
        $lastYear = date('Y-m-d', strtotime('-12 months'));
        $monthlyRevenue = Payment::find()
            ->select(['DATE_FORMAT(payment_date, "%Y-%m") as month', 'SUM(amount) as total'])
            ->where(['>=', 'payment_date', $lastYear])
            ->groupBy(['month'])
            ->asArray()
            ->all();
        
        $monthlyRevenueData = [];
        foreach ($monthlyRevenue as $revenue) {
            // Format month as human-readable (e.g., "Jan 2023")
            $dateObj = \DateTime::createFromFormat('Y-m', $revenue['month']);
            $formattedMonth = $dateObj->format('M Y');
            $monthlyRevenueData[$formattedMonth] = (float)$revenue['total'];
        }
        
        // Payments with dataProvider for grid view
        $paymentsProvider = new ActiveDataProvider([
            'query' => Payment::find()
                ->with(['medicalRecord', 'medicalRecord.patient'])
                ->where(['between', 'payment_date', $startDate, $endDate . ' 23:59:59']),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'payment_date' => SORT_DESC,
                ]
            ],
        ]);
        
        return $this->render('payment', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalRevenue' => $totalRevenue,
            'revenueByMethod' => $revenueByMethod,
            'dailyRevenueData' => $dailyRevenueData,
            'monthlyRevenueData' => $monthlyRevenueData,
            'paymentsProvider' => $paymentsProvider,
        ]);
    }

    /**
     * Generates medicine inventory report
     */
    public function actionMedicine()
    {
        // Medicines by stock level
        $lowStockMedicines = Medicine::find()
            ->where(['<', 'stock', 10])
            ->count();
            
        $adequateStockMedicines = Medicine::find()
            ->where(['between', 'stock', 10, 50])
            ->count();
            
        $highStockMedicines = Medicine::find()
            ->where(['>', 'stock', 50])
            ->count();
        
        // Total medicines
        $totalMedicines = $lowStockMedicines + $adequateStockMedicines + $highStockMedicines;
        
        // Most prescribed medicines
        $mostPrescribedMedicines = Medicine::find()
            ->select([
                'medicine.id',
                'medicine.code',
                'medicine.name',
                'COUNT(medicine_detail.id) as prescription_count'
            ])
            ->leftJoin('medicine_detail', 'medicine.id = medicine_detail.medicine_id')
            ->groupBy(['medicine.id', 'medicine.code', 'medicine.name'])
            ->orderBy(['prescription_count' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();
        
        // Medicine stock value
        $medicineStockValue = Medicine::find()
            ->select(['SUM(stock * price) as total_value'])
            ->scalar();
        
        // Medicines with dataProvider for grid view
        $medicinesProvider = new ActiveDataProvider([
            'query' => Medicine::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ]
            ],
        ]);
        
        return $this->render('medicine', [
            'totalMedicines' => $totalMedicines,
            'lowStockMedicines' => $lowStockMedicines,
            'adequateStockMedicines' => $adequateStockMedicines,
            'highStockMedicines' => $highStockMedicines,
            'mostPrescribedMedicines' => $mostPrescribedMedicines,
            'medicineStockValue' => $medicineStockValue,
            'medicinesProvider' => $medicinesProvider,
        ]);
    }

    /**
     * Generates treatment report
     */
    public function actionTreatment()
    {
        $startDate = Yii::$app->request->get('start_date', date('Y-m-01'));
        $endDate = Yii::$app->request->get('end_date', date('Y-m-t'));
        
        // Total treatments
        $totalTreatments = Treatment::find()->count();
        
        // Most performed treatments
        $mostPerformedTreatments = Treatment::find()
            ->select([
                'treatment.id',
                'treatment.code',
                'treatment.name',
                'COUNT(treatment_detail.id) as perform_count'
            ])
            ->leftJoin('treatment_detail', 'treatment.id = treatment_detail.treatment_id')
            ->leftJoin('medical_record', 'treatment_detail.medical_record_id = medical_record.id')
            ->where(['between', 'medical_record.treatment_date', $startDate, $endDate . ' 23:59:59'])
            ->groupBy(['treatment.id', 'treatment.code', 'treatment.name'])
            ->orderBy(['perform_count' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();
        
        // Revenue by treatment category
        $revenueByCategory = Treatment::find()
            ->select([
                'treatment.category',
                'SUM(treatment_detail.price * treatment_detail.quantity) as total_revenue'
            ])
            ->leftJoin('treatment_detail', 'treatment.id = treatment_detail.treatment_id')
            ->leftJoin('medical_record', 'treatment_detail.medical_record_id = medical_record.id')
            ->where(['between', 'medical_record.treatment_date', $startDate, $endDate . ' 23:59:59'])
            ->groupBy(['treatment.category'])
            ->asArray()
            ->all();
        
        // Treatments with dataProvider for grid view
        $treatmentsProvider = new ActiveDataProvider([
            'query' => Treatment::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ]
            ],
        ]);
        
        return $this->render('treatment', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalTreatments' => $totalTreatments,
            'mostPerformedTreatments' => $mostPerformedTreatments,
            'revenueByCategory' => $revenueByCategory,
            'treatmentsProvider' => $treatmentsProvider,
        ]);
    }

    /**
     * Generates employee report
     */
    public function actionEmployee()
    {
        // Total employees
        $totalEmployees = Employee::find()->count();
        
        // Employees by position
        $employeesByPosition = Employee::find()
            ->select(['position', 'COUNT(*) as count'])
            ->groupBy(['position'])
            ->asArray()
            ->all();
        
        // Employees by status
        $employeesByStatus = Employee::find()
            ->select(['status', 'COUNT(*) as count'])
            ->groupBy(['status'])
            ->asArray()
            ->all();
        
        // Employees with dataProvider for grid view
        $employeesProvider = new ActiveDataProvider([
            'query' => Employee::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'name' => SORT_ASC,
                ]
            ],
        ]);
        
        return $this->render('employee', [
            'totalEmployees' => $totalEmployees,
            'employeesByPosition' => $employeesByPosition,
            'employeesByStatus' => $employeesByStatus,
            'employeesProvider' => $employeesProvider,
        ]);
    }
}
