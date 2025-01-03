<?php
/**
 * Created by PhpStorm.
 * User: HungLuongHien
 * Date: 7/27/2016
 * Time: 3:02 PM
 */

namespace common\models;


use yii\filters\AccessRule;

class AccessRules extends AccessRule
{
    /**
     * @inheritdoc
     */
    protected function matchRole($user)
    {
        if (empty($this->roles)) {
            return true;
        }
        foreach ($this->roles as $role) {
            if ($role === '?') {

                if ($user->getIsGuest()) {
                    return true;
                }
            } elseif ($role === '@') {
                if (!$user->getIsGuest()) {
                    return true;
                }
                // Check if the user is logged in, and the roles match
            } elseif (!$user->getIsGuest() && (int)$role === $user->identity->user_role) {
                return true;
            }
        }

        return false;
    }
}