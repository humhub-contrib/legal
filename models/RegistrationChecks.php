<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\legal\models;

use humhub\libs\Html;
use humhub\modules\legal\Module;
use humhub\modules\user\models\User;
use Yii;
use yii\base\Exception;
use yii\base\Model;

/* @var $this \humhub\modules\ui\view\components\View */

/**
 * Class RegistrationChecks
 * @package humhub\modules\legal\models
 */
class RegistrationChecks extends Model
{

    const SETTING_KEY_TERMS = 'acceptedTerms';
    const SETTING_KEY_PRIVACY = 'acceptedPrivacy';
    const SETTING_KEY_AGE = 'acceptedAge';

    public $ageCheck;
    public $termsCheck;
    public $dataPrivacyCheck;

    /**
     * @var false|string static SETTING_KEY const
     */
    public $restrictToSettingKey = false;

    /**
     * @var User
     */
    public $user;

    public function rules()
    {
        $rules = [];

        if ($this->showAgeCheck()) {
            $rules[] = [['ageCheck'], 'required', 'requiredValue' => 1, 'message' => ''];
        }

        if ($this->showPrivacyCheck()) {
            $rules[] = [['dataPrivacyCheck'], 'required', 'requiredValue' => 1, 'message' => ''];
        }

        if ($this->showTermsCheck()) {
            $rules[] = [['termsCheck'], 'required', 'requiredValue' => 1, 'message' => ''];
        }

        return $rules;
    }

    public function attributeLabels()
    {
        return [
            'ageCheck' => Yii::t('LegalModule.base', 'I am older than {age} years', ['age' => \Yii::$app->getModule('legal')->getMinimumAge()]),
            'termsCheck' => Yii::t('LegalModule.base', 'I have read and agree to the Terms and Conditions'),
            'dataPrivacyCheck' => Yii::t('LegalModule.base', 'I have read and agree to the Privacy Policy')
        ];
    }

    public function attributeHints()
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('legal');

        $spacing = '&nbsp;&nbsp;&nbsp;&nbsp;';
        $hints = [];

        $privacyPage = Page::getPage(Page::PAGE_KEY_PRIVACY_PROTECTION);
        if ($privacyPage !== null && $module->isPageEnabled(Page::PAGE_KEY_PRIVACY_PROTECTION)) {
            $link = Html::a($privacyPage->title, ['/legal/page/view', 'pageKey' => Page::PAGE_KEY_PRIVACY_PROTECTION], ['data-pjax-prevent' => 1, 'target' => '_blank']);
            $hints['dataPrivacyCheck'] = $spacing . Yii::t('LegalModule.base', 'More information: {link}', ['link' => $link]);
        }

        $termsPage = Page::getPage(Page::PAGE_KEY_TERMS);
        if ($termsPage !== null && $module->isPageEnabled(Page::PAGE_KEY_TERMS)) {
            $link = Html::a($termsPage->title, ['/legal/page/view', 'pageKey' => Page::PAGE_KEY_TERMS], ['data-pjax-prevent' => 1, 'target' => '_blank']);
            $hints['termsCheck'] = $spacing . Yii::t('LegalModule.base', 'More information: {link}', ['link' => $link]);
        }

        return $hints;
    }

    public function showPrivacyCheck()
    {
        if ($this->restrictToSettingKey && $this->restrictToSettingKey !== static::SETTING_KEY_PRIVACY) {
            return false;
        }

        /** @var Module $module */
        $module = Yii::$app->getModule('legal');
        if (Page::getPage(Page::PAGE_KEY_PRIVACY_PROTECTION) && $module->isPageEnabled(Page::PAGE_KEY_PRIVACY_PROTECTION)) {
            if ($this->user === null || empty($module->settings->user($this->user)->get(static::SETTING_KEY_PRIVACY))) {
                return true;
            }
        }

        return false;
    }

    public function showTermsCheck()
    {
        if ($this->restrictToSettingKey && $this->restrictToSettingKey !== static::SETTING_KEY_TERMS) {
            return false;
        }

        /** @var Module $module */
        $module = Yii::$app->getModule('legal');
        if (Page::getPage(Page::PAGE_KEY_TERMS) && $module->isPageEnabled(Page::PAGE_KEY_TERMS)) {
            if ($this->user === null || empty($module->settings->user($this->user)->get(static::SETTING_KEY_TERMS))) {
                return true;
            }
        }

        return false;
    }

    public function showAgeCheck()
    {
        if ($this->restrictToSettingKey && $this->restrictToSettingKey !== static::SETTING_KEY_AGE) {
            return false;
        }

        /** @var Module $module */
        $module = Yii::$app->getModule('legal');
        if ($module->showAgeCheck()) {
            if ($this->user === null || empty($module->settings->user($this->user)->get(static::SETTING_KEY_AGE))) {
                return true;
            }
        }
        return false;
    }

    public function hasOpenCheck()
    {
        if ($this->showAgeCheck() || $this->showTermsCheck() || $this->showPrivacyCheck()) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function save()
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('legal');

        if ($this->user === null) {
            if (Yii::$app->user->isGuest) {
                throw new Exception('Could not save with valid user object!');
            }
            $this->user = Yii::$app->user->getIdentity();
        }

        if (!$this->validate()) {
            return false;
        }

        if ($this->showTermsCheck() && $this->termsCheck) {
            $module->settings->user($this->user)->set(static::SETTING_KEY_TERMS, true);
            $module->settings->user($this->user)->set(static::SETTING_KEY_TERMS . 'Time', time());
        }

        if ($this->showPrivacyCheck() && $this->dataPrivacyCheck) {
            $module->settings->user($this->user)->set(static::SETTING_KEY_PRIVACY, true);
            $module->settings->user($this->user)->set(static::SETTING_KEY_PRIVACY . 'Time', time());
        }

        if ($this->showAgeCheck() && $this->ageCheck) {
            $module->settings->user($this->user)->set(static::SETTING_KEY_AGE, true);
            $module->settings->user($this->user)->set(static::SETTING_KEY_AGE . 'Time', time());
        }

        return true;
    }
}
