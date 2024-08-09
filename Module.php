<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\legal;

use Yii;
use yii\helpers\Url;

class Module extends \humhub\components\Module
{
    /**
     * @inheritdoc
     */
    public function getConfigUrl()
    {
        return Url::to(['/legal/admin']);
    }

    /**
     * @return array of page keys
     */
    public function getEnabledPages()
    {
        if (empty($this->settings->get('enabledPages'))) {
            return [];
        }

        return explode(',', $this->settings->get('enabledPages'));
    }

    /**
     * @param string|null $setting Setting name: 'icon', 'modal', null - to return array of all enabled settings
     * @return array|bool Config for external links
     */
    public function getExternalLinksConfig(?string $setting = null)
    {
        $config = empty($this->settings->get('externalLinks'))
            ? []
            : explode(',', $this->settings->get('externalLinks'));

        return $setting === null ? $config : in_array($setting, $config);
    }

    /**
     * @return bool
     */
    public function showPagesAfterRegistration()
    {
        return (bool)$this->settings->get('showPagesAfterRegistration', false);
    }

    /**
     * @param $pageKey
     * @return bool
     */
    public function isPageEnabled($pageKey)
    {
        if (in_array($pageKey, $this->getEnabledPages())) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getDefaultLanguage()
    {
        if (empty($this->settings->get('defaultLanguage'))) {
            return Yii::$app->language;
        }

        return $this->settings->get('defaultLanguage');
    }

    public function isAllowedExportUserData(): bool
    {
        return Yii::$app->hasModule('rest') && Yii::$app->getModule('rest')->isEnabled;
    }

    public function isEnabledExportUserData(): bool
    {
        return $this->isAllowedExportUserData() && $this->settings->get('exportUserData', false);
    }

    public function getExportUserDays(): int
    {
        return (int) $this->settings->get('exportUserDays', 1);
    }

    public function showAgeCheck(): bool
    {
        return (bool) $this->settings->get('showAgeCheck', false);
    }

    /**
     * @return string
     */
    public function getMinimumAge()
    {
        return $this->settings->get('minimumAge', 16);
    }

    public function getName()
    {
        return Yii::t('LegalModule.base', 'Legal Tools');
    }

    public function getDescription()
    {
        return Yii::t('LegalModule.base', 'Adds several editable legal options, like an imprint and a privacy policy.');
    }

}
