<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\legal\models;

use humhub\modules\legal\Events;
use humhub\modules\legal\Module;
use Yii;
use yii\base\Exception;
use yii\base\Model;

class ConfigureForm extends Model
{
    public $enabledPages;
    public $externalLinks;
    public $showPagesAfterRegistration;
    public $defaultLanguage;
    public $showAgeCheck;
    public $minimumAge;
    public $exportUserData;
    public $exportUserDays;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enabledPages'], 'in', 'range' => array_keys(Page::getPages())],
            [['externalLinks'], 'in', 'range' => ['icon', 'modal']],
            [['defaultLanguage'], 'in', 'range' => array_keys(Yii::$app->i18n->getAllowedLanguages())],
            [['showPagesAfterRegistration', 'showAgeCheck', 'exportUserData'], 'boolean'],
            ['minimumAge', 'integer', 'min' => 16, 'max' => 99],
            ['exportUserDays', 'integer', 'min' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'enabledPages' => Yii::t('LegalModule.base', 'Enabled pages and features'),
            'showPagesAfterRegistration' => Yii::t('LegalModule.base', 'For new account creation, show pages in full screen just after profile creation'),
            'defaultLanguage' => Yii::t('LegalModule.base', 'Default language'),
            'showAgeCheck' => Yii::t('LegalModule.base', 'Show age verification {age}', ['age' => $this->minimumAge]),
            'minimumAge' => Yii::t('LegalModule.base', 'Minimum age'),
            'exportUserData' => Yii::t('LegalModule.base', 'Enable user data export'),
            'exportUserDays' => Yii::t('LegalModule.base', 'Number of days while a download package will be kept'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'defaultLanguage' => Yii::t('LegalModule.base', 'Will be used as default, if the legal texts are not available in the users language.'),
            'exportUserData' => $this->getModule()->isAllowedExportUserData()
                ? ''
                : '<span class="text-danger">' .
                    Yii::t('LegalModule.base', 'To enable the user data export, please enable the REST API module.') .
                  '</span>',
        ];
    }

    public function loadSettings()
    {
        $this->defaultLanguage = $this->getModule()->getDefaultLanguage();
        $this->enabledPages = $this->getModule()->getEnabledPages();
        $this->externalLinks = $this->getModule()->getExternalLinksConfig();
        $this->showPagesAfterRegistration = $this->getModule()->showPagesAfterRegistration();
        $this->showAgeCheck = $this->getModule()->showAgeCheck();
        $this->minimumAge = $this->getModule()->getMinimumAge();
        $this->exportUserData = $this->getModule()->isEnabledExportUserData();
        $this->exportUserDays = $this->getModule()->getExportUserDays();
    }

    /**
     * @return bool
     */
    public function saveSettings()
    {
        $settings = $this->getModule()->settings;

        if (!is_array($this->enabledPages)) {
            $this->enabledPages = [];
        }

        try {
            $settings->set('defaultLanguage', $this->defaultLanguage);
            $settings->set('enabledPages', implode(',', $this->enabledPages));
            $settings->set('externalLinks', empty($this->externalLinks) ? '' : implode(',', $this->externalLinks));
            $settings->set('showPagesAfterRegistration', $this->showPagesAfterRegistration);
            $settings->set('showAgeCheck', $this->showAgeCheck);
            $settings->set('minimumAge', $this->minimumAge);
            if ($this->getModule()->isAllowedExportUserData()) {
                $settings->set('exportUserData', $this->exportUserData);
                $settings->set('exportUserDays', $this->exportUserDays);
            }
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            return false;
        }

        // Show legal pages to check
        Yii::$app->session->remove(Events::SESSION_KEY_LEGAL_CHECK);

        return true;
    }

    /**
     * @return Module
     */
    public function getModule()
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('legal');
        return $module;
    }

}
