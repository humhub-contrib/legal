<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\legal\models\ConfigureForm;
use humhub\modules\legal\models\Page;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $module \humhub\modules\legal\Module */
/* @var $model ConfigureForm */

$enabledPages = [];
foreach (Page::getPages() as $key => $title) {
    $disabledWarning = (Page::getPage($key, $module->getDefaultLanguage()) === null) ? Yii::t('LegalModule.base', '(Disabled - please add content in default language!)') : '';
    $enabledPages[$key] = $title . ' ' . $disabledWarning;
}
?>

<?php $this->beginContent('@legal/views/admin/layout.php') ?>
<div class="panel-body">

    <?php $form = ActiveForm::begin(['id' => 'configure-form', 'enableClientValidation' => false, 'enableClientScript' => false]); ?>

    <?= $form->field($model, 'enabledPages')->checkboxList($enabledPages); ?>
    <?= $form->field($model, 'externalLinks')->checkboxList([
            'icon' => Yii::t('LegalModule.base', 'Add notice icons before external links in Posts and Comments'),
            'modal' => Yii::t('LegalModule.base', 'Show notice modal on external links in Posts and Comments')
        ])->label(false); ?>
    <?= $form->field($model, 'showPagesAfterRegistration')->checkbox(); ?>
    <?= $form->field($model, 'defaultLanguage')->dropDownList(Yii::$app->i18n->getAllowedLanguages(), ['data-ui-select2' => '']); ?>

    <?= $form->field($model, 'showAgeCheck')->checkbox(); ?>
    <?= $form->field($model, 'minimumAge')->textInput()->hint(\Yii::t('LegalModule.base', 'Please enter a number value.')); ?>

    <?= $form->field($model, 'exportUserData')->checkbox(['disabled' => !$model->getModule()->isAllowedExportUserData()]) ?>
    <?= $form->field($model, 'exportUserDays')->textInput(['disabled' => !$model->getModule()->isAllowedExportUserData()]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('base', 'Save'), ['class' => 'btn btn-primary', 'data-ui-loader' => '']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php $this->endContent() ?>
