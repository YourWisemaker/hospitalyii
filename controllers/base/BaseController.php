<?php

namespace app\controllers\base;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * BaseController implements common methods for all controllers.
 */
class BaseController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
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
     * Check if current user has a specific role
     * 
     * @param string $roleName Role name to check
     * @return boolean Whether user has the role
     */
    protected function hasRole($roleName)
    {
        $user = Yii::$app->user->identity;
        if ($user && method_exists($user, 'hasRole')) {
            return $user->hasRole($roleName);
        }
        return false;
    }
    
    /**
     * Check if current user has any of the specified roles
     * 
     * @param array $roleNames Array of role names to check
     * @return boolean Whether user has any of the roles
     */
    protected function hasAnyRole($roleNames)
    {
        foreach ($roleNames as $roleName) {
            if ($this->hasRole($roleName)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Get current user ID
     * 
     * @return integer Current user ID
     */
    protected function getUserId()
    {
        return Yii::$app->user->id;
    }
}
