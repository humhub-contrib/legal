<?php

namespace humhub\modules\legal\validators;

use DateTime;
use Yii;
use yii\validators\Validator;
use humhub\modules\user\models\User;

/**
 * AgeValidator validates that the given value represents an age greater than or equal to a specified minimum age.
 */
class AgeValidator extends Validator
{
    /**
     * @var int The minimum age required
     */
    public $minimumAge;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->minimumAge === null) {
            $this->minimumAge = Yii::$app->getModule('legal')->getMinimumAge();
        }
    }

    /**
     * Validates the age of the user based on the given attribute value.
     *
     * @param \yii\base\Model $model the data model being validated
     * @param string $attribute the name of the attribute to be validated
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if (!$value instanceof DateTime) {
            try {
                $value = new DateTime($value);
            } catch (\Exception $e) {
                $this->addError($model, $attribute, Yii::t('LegalModule.base', 'Invalid date format.'));
                return;
            }
        }

        $today = new DateTime();
        $age = $today->diff($value)->y;

        if ($age < $this->minimumAge) {
            $message = Yii::t('LegalModule.base', 'You must be at least {age} years old.', ['age' => $this->minimumAge]);
            $this->addError($model, $attribute, $message);
        }
    }
}
