<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $pages Page[] */
/* @var $languages array */
/* @var $defaultLanguage string */
/* @var $pageKey string */

use humhub\libs\Html;
use humhub\modules\legal\models\Page;
use humhub\modules\legal\models\RegistrationChecks;
use humhub\modules\content\widgets\richtext\RichTextField;
use yii\bootstrap\ActiveForm;

?>


<?php $this->beginContent('@legal/views/admin/layout.php') ?>
<div class="panel-body">
    <?php $form = ActiveForm::begin(['id' => 'configure-form', 'enableClientValidation' => false, 'enableClientScript' => false]); ?>
    <br/>
    <?php if ($pageKey === Page::PAGE_KEY_LEGAL_UPDATE): ?>
        <p><?= Yii::t('LegalModule.base', 'Informs the users that you have changed your Privacy Policy or your Terms and Conditions. In order to trigger it, the „Reset confirmation“-Option of said legal documents need to be activated.'); ?></p>
    <?php elseif ($pageKey === Page::PAGE_KEY_PRIVACY_PROTECTION): ?>
        <p><?= Yii::t('LegalModule.base', 'This page is added to the footer navigation and the registration process. You can add a different text for every available language.'); ?> <?= Yii::t('LegalModule.base', 'If you update your Privacy Policy you can use the „Reset confirmation“-Option to inform your users and invite them to reagree. '); ?></p>
    <?php elseif ($pageKey === Page::PAGE_KEY_TERMS): ?>
        <p><?= Yii::t('LegalModule.base', 'This page is added to the footer navigation and the registration process. You can add a different text for every available language.'); ?> <?= Yii::t('LegalModule.base', 'If you update your Terms and Conditions you can use the „Reset confirmation“-Option to inform your users and invite them to reagree. '); ?></p>
    <?php elseif ($pageKey === Page::PAGE_KEY_IMPRINT): ?>
        <p><?= Yii::t('LegalModule.base', 'This page is added to the footer navigation and the registration process. You can add a different text for every available language.'); ?></p>
    <?php endif; ?>

    <br/>

    <div class="pull-right">
        <strong><?= Yii::t('LegalModule.base', 'Page language:'); ?></strong>
        <?= Html::dropDownList('lang', $defaultLanguage, $languages, ['class' => 'form-input', 'data-ui-select2' => '', 'id' => 'pageLangSelector']); ?>
    </div>

    <br/>
    <br/>
    <br/>

    <?php foreach ($languages as $languageKey => $languageTitle): ?>
        <div id="page_<?= $languageKey ?>" class="page_language" style="display:none">
            <?= $form->field($pages[$languageKey], '[' . $languageKey . ']title')->textInput(); ?>
            <?= $form->field($pages[$languageKey], '[' . $languageKey . ']content')->widget(RichTextField::class, ['layout' => RichTextField::LAYOUT_BLOCK, 'pluginOptions' => ['maxHeight' => '300px']]); ?>
        </div>
    <?php endforeach; ?>

    <?php if ($pageKey === Page::PAGE_KEY_PRIVACY_PROTECTION): ?>
        <?= Html::a(Yii::t('LegalModule.base', 'Reset confirmation'), ['/legal/admin/reset', 'key' => RegistrationChecks::SETTING_KEY_PRIVACY], ['class' => 'btn btn-danger btn-sm pull-right', 'data-confirm' => Yii::t('LegalModule.base', 'Are you really sure? Please save changes before proceed!')]); ?>
    <?php elseif ($pageKey === Page::PAGE_KEY_TERMS): ?>
        <?= Html::a(Yii::t('LegalModule.base', 'Reset confirmation'), ['/legal/admin/reset', 'key' => RegistrationChecks::SETTING_KEY_TERMS], ['class' => 'btn btn-danger btn-sm pull-right', 'data-confirm' => Yii::t('LegalModule.base', 'Are you really sure? Please save changes before proceed!')]); ?>
    <?php endif; ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('base', 'Save'), ['class' => 'btn btn-primary', 'data-ui-loader' => '']) ?>
    </div>

    <?php ActiveForm::end(); ?>


    <script <?= Html::nonce() ?>>
        showLanguage();
        var select2Interval = setInterval(function() {
            if(jQuery.fn.select2) {
                clearInterval(select2Interval);
                $("#pageLangSelector").select2().on("select2:select", function (e) {
                    showLanguage();
                });
            }
        }, 50 );

        function showLanguage() {
            curLang = $('#pageLangSelector').val();
            $('.page_language').hide();
            $('#page_' + curLang).show();
        }
    </script>

</div>
<?php $this->endContent() ?>

