<?php /** @noinspection MissedFieldInspection */

use humhub\commands\CronController;
use humhub\components\Controller;
use humhub\modules\content\widgets\richtext\ProsemirrorRichText;
use humhub\modules\user\models\forms\Registration;
use humhub\modules\user\widgets\AccountSettingsMenu;
use humhub\modules\user\models\Profile;
use humhub\widgets\FooterMenu;
use humhub\widgets\LayoutAddons;

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

return [
    'id' => 'legal',
    'class' => 'humhub\modules\legal\Module',
    'namespace' => 'humhub\modules\legal',
    'events' => [
        ['class' => FooterMenu::class, 'event' => FooterMenu::EVENT_INIT, 'callback' => ['humhub\modules\legal\Events', 'onFooterMenuInit']],
        ['class' => LayoutAddons::class, 'event' => LayoutAddons::EVENT_INIT, 'callback' => ['humhub\modules\legal\Events', 'onLayoutAddonInit']],
        ['class' => Registration::class, 'event' => Registration::EVENT_BEFORE_RENDER, 'callback' => ['humhub\modules\legal\Events', 'onRegistrationFormRender']],
        ['class' => Registration::class, 'event' => Registration::EVENT_AFTER_INIT, 'callback' => ['humhub\modules\legal\Events', 'onRegistrationFormInit']],
        ['class' => Registration::class, 'event' => Registration::EVENT_AFTER_REGISTRATION, 'callback' => ['humhub\modules\legal\Events', 'onRegistrationAfterRegistration']],
        ['class' => Controller::class, 'event' => Controller::EVENT_BEFORE_ACTION, 'callback' => ['humhub\modules\legal\Events', 'onBeforeControllerAction']],
        ['class' => ProsemirrorRichText::class, 'event' => ProsemirrorRichText::EVENT_AFTER_RUN, 'callback' => ['humhub\modules\legal\Events', 'onAfterRunRichText']],
        ['class' => AccountSettingsMenu::class, 'event' => AccountSettingsMenu::EVENT_INIT, 'callback' => ['humhub\modules\legal\Events', 'onAccountSettingsMenuInit']],
        ['class' => CronController::class, 'event' => CronController::EVENT_ON_DAILY_RUN, 'callback' => ['humhub\modules\legal\Events', 'onCronDailyRun']],
        ['class' => Profile::class, 'event' => Profile::EVENT_BEFORE_VALIDATE, 'callback' => ['humhub\modules\legal\Events', 'onBeforeValidate']],
    ],
];
