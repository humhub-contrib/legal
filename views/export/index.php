<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\legal\services\ExportService;
use humhub\widgets\Button;

/* @var ExportService $service */
?>
<div class="panel-heading">
    <?= Yii::t('LegalModule.base', '<strong>Personal</strong> Data Export') ?>
</div>
<div class="panel-body">
    <?php if ($service->hasPackage()) : ?>
        <?= Yii::t('LegalModule.base', 'Data package is available for download.') ?>
    <?php else : ?>
        <?= Yii::t('LegalModule.base', 'After click on generate package, your package will be created. This process may take some time. Come back in some minutes. The package will be automatically deleted in {countDays} days.', [
            'countDays' => $service->getModule()->getExportUserDays(),
        ]) ?>
    <?php endif; ?>
</div>

<div class="panel-body">
    <?php if ($service->hasPackage()) : ?>
        <?= Button::info(Yii::t('LegalModule.base', 'Download Package'))
            ->icon('download')
            ->link(['/legal/export/download'])
            ->loader(false) ?>
        <?= Button::danger(Yii::t('LegalModule.base', 'Delete Package'))
            ->icon('trash')
            ->link(['/legal/export/delete'])
            ->right()
            ->confirm() ?>
    <?php else : ?>
        <?= Button::primary(Yii::t('LegalModule.base', 'Generate Package'))
            ->icon('rocket')
            ->link(['/legal/export/generate']) ?>
    <?php endif; ?>
</div>
