<?php
/**
 * Created by PhpStorm.
 * User: kalim_000
 * Date: 2/23/2017
 * Time: 2:36 PM
 */

namespace backend\models;

use common\models\SignupForm;
use common\models\User;

class AdminSignupForm extends SignupForm
{
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->save(false);

            // the following three lines were added:
            $auth = \Yii::$app->authManager;
            $adminRole = $auth->getRole(User::ROLE_ADMIN);
            $auth->assign($adminRole, $user->getId());

            return $user;
        }

        return null;
    }

}