<?php

namespace humhub\modules\legal\validators;

use Yii;
use DateTime;
use yii\validators\Validator;
use humhub\modules\user\models\User;

/**
 * AgeValidator validates that the given value represents an age greater than or equal to a specified minimum age.
 */
class AgeValidator extends Validator
{
    /**
     * Validates the age of the user based on the given attribute value.
     *
     * @param \yii\base\Model $model the data model being validated
     * @param string $attribute the name of the attribute to be validated
     */
    public function validateAttribute($model, $attribute)
    {
        $user = $model->user;

        // Get the birthday attribute directly from the user model
        $birthday = $user->profile->birthday;

        // Ensure the value represents a valid date
        if ($birthday instanceof DateTime) {
            // Get minimum age from the module
            $minimumAge = Yii::$app->getModule('legal')->getMinimumAge();

            // Calculate the age
            $today = new DateTime();
            $age = $today->diff($birthday)->y;

            // Check if the age meets the minimum requirement
            if ($age < $minimumAge) {
                // Set error message
                $message = Yii::t('LegalModule.base', 'You must be at least {age} years old.', ['age' => $minimumAge]);

                // Add error to the model attribute
                $model->addError($attribute, $message);

                // Disable the user account if age requirement is not met
                $user->status = User::STATUS_DISABLED;
                $user->save(false);
            }
        }
    }
}
