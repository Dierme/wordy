<?php
/**
 * Created by PhpStorm.
 * User: kalim_000
 * Date: 2/23/2017
 * Time: 2:18 PM
 */

namespace backend\models;

use common\models\LoginForm;
use common\validators\RoleValidator;

class AdminLoginForm extends LoginForm
{
    const TYPE = 'admin';

    public function rules()
    {
        $adminRules = [
            ['username', RoleValidator::className()]
        ];

        return array_merge($adminRules, parent::rules());
    }

}