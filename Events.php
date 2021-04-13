<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\legal;

use humhub\components\Controller;
use humhub\modules\legal\models\Page;
use humhub\modules\legal\models\RegistrationChecks;
use humhub\modules\legal\widgets\CookieNote;
use humhub\modules\user\models\forms\Registration;
use humhub\modules\user\models\User;
use humhub\widgets\LayoutAddons;
use Yii;
use yii\base\ActionEvent;
use yii\helpers\Url;
use yii\web\UserEvent;


/**
 * @author luke
 */
class Events
{

    const SESSION_KEY_LEGAL_CHECK = 'legalModuleChecked';
    const SESSION_KEY_LEGAL_AFTER_REGISTRATION = 'legalModuleAfterRegistration';

    public static function onFooterMenuInit($event)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('legal');

        $sortOrder = 100;
        foreach (Page::getPages() as $pageKey => $title) {
            if (!$module->isPageEnabled($pageKey) || !in_array($pageKey, Page::getFooterMenuPages())) {
                // Cookie notice is not a navigation page
                continue;
            }

            $page = Page::getPage($pageKey);
            if ($page !== null) {
                $sortOrder += 10;
                $event->sender->addItem(array(
                    'label' => $page->title,
                    'url' => Url::to(['/legal/page/view', 'pageKey' => $pageKey], true),
                    'sortOrder' => $sortOrder,
                ));
            }
        }

    }

    public static function onLayoutAddonInit($event)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('legal');

        /** @var LayoutAddons $layoutAddons */
        $layoutAddons = $event->sender;

        if ($module->isPageEnabled(Page::PAGE_KEY_COOKIE_NOTICE)) {
            $layoutAddons->addWidget(CookieNote::class);
        }

    }

    public static function onBeforeControllerAction(ActionEvent $event)
    {
        if (Yii::$app->user->isGuest) {
            return;
        }

        // Legal already checked
        if (!empty(Yii::$app->session->get(static::SESSION_KEY_LEGAL_CHECK))) {
            return;
        }

        // Allow actions which should not be intercepted
        if ($event->action->controller instanceof Controller && $event->action->controller->isNotInterceptedAction()) {
            return;
        }

        /** @var Module $module */
        $module = Yii::$app->getModule('legal');

        $registrationCheck = new RegistrationChecks(['user' => Yii::$app->user->getIdentity()]);
        if (!$registrationCheck->hasOpenCheck()) {
            Yii::$app->session->set(static::SESSION_KEY_LEGAL_CHECK, 'true');
            Yii::$app->session->remove(static::SESSION_KEY_LEGAL_AFTER_REGISTRATION);
            return;
        }

        // Allow legal module usage
        if ($event->action->controller->module->id === 'legal') {
            if (Yii::$app->controller->id === 'page') {
                $event->sender->layout = '@user/views/layouts/main';
                $event->sender->subLayout = '@legal/views/page/layout_login';
            }
            return;
        }

        // Show legal update?
        if (empty(Yii::$app->session->get(static::SESSION_KEY_LEGAL_AFTER_REGISTRATION)) && $module->isPageEnabled(Page::PAGE_KEY_LEGAL_UPDATE) && Page::getPage(Page::PAGE_KEY_LEGAL_UPDATE) !== null) {
            $event->isValid = false;
            $event->result = Yii::$app->response->redirect(['/legal/page/update']);
        }
        // Show legal pages in full screen with confirm form, one by one (after account creation)
        elseif($registrationCheck->showTermsCheck() || $registrationCheck->showPrivacyCheck()) {
            $event->isValid = false;
            $event->result = Yii::$app->response->redirect(['/legal/page/confirm']);
        }
    }

    public static function onRegistrationFormInit($event)
    {
        // Do not store on console request
        if (Yii::$app->request->isConsoleRequest) {
            return;
        }

        /** @var Registration $hForm */
        $hForm = $event->sender;

        /** @var Module $module */
        $module = Yii::$app->getModule('legal');

        $hForm->models['RegistrationChecks'] = new RegistrationChecks(['restrictToSettingKey' => $module->showPagesAfterRegistration() ? RegistrationChecks::SETTING_KEY_AGE : false]);

        if ($module->showPagesAfterRegistration()) {
            Yii::$app->session->set(static::SESSION_KEY_LEGAL_AFTER_REGISTRATION, 'true');
        }
    }

    public static function onRegistrationFormRender($event)
    {
        // Do not store on console request
        if (Yii::$app->request->isConsoleRequest) {
            return;
        }

        /** @var Registration $hForm */
        $hForm = $event->sender;

        /** @var RegistrationChecks $model */
        $model = $hForm->models['RegistrationChecks'];

        /** @var Module $module */
        $module = Yii::$app->getModule('legal');

        $elements = [];

        if ($model->showTermsCheck()) {
            $elements['termsCheck'] = [
                'type' => 'checkbox',
                'class' => 'form-control',
            ];
        }
        if ($model->showPrivacyCheck()) {
            $elements['dataPrivacyCheck'] = [
                'type' => 'checkbox',
                'class' => 'form-control',
            ];
        }

        if ($module->showAgeCheck()) {
            $elements['ageCheck'] = [
                'type' => 'checkbox',
                'class' => 'form-control',
            ];
        }

        $hForm->definition['elements']['RegistrationChecks'] = [
            'type' => 'form',
            'elements' => $elements
        ];
    }

    /**
     * @param UserEvent $event
     * @throws \yii\base\Exception
     */
    public static function onRegistrationAfterRegistration(UserEvent $event)
    {
        // Do not store on console request
        if (Yii::$app->request->isConsoleRequest) {
            return;
        }

        /** @var User $user */
        $user = $event->identity;

        /** @var Module $module */
        $module = Yii::$app->getModule('legal');

        $model = new RegistrationChecks(['user' => $user, 'restrictToSettingKey' => $module->showPagesAfterRegistration() ? RegistrationChecks::SETTING_KEY_AGE : false]);
        $model->load(Yii::$app->request->post());
        $model->save();
    }
}
