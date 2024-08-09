<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\legal\controllers;

use humhub\modules\legal\Module;
use humhub\modules\legal\services\ExportService;
use humhub\modules\user\components\BaseAccountController;
use Yii;

/* @property Module $module */
class ExportController extends BaseAccountController
{
    public function actionIndex()
    {
        return $this->render('index', [
            'service' => new ExportService(),
        ]);
    }

    public function actionGenerate()
    {
        if ((new ExportService())->generatePackage()) {
            $this->view->success(Yii::t('LegalModule.base', 'The exporting of your data has been started, please wait some time.'));
        } else {
            $this->view->error(Yii::t('LegalModule.base', 'Cannot start the exporting of your data, please try again.'));
        }

        return $this->redirect('index');
    }

    public function actionDownload()
    {
        return (new ExportService())->downloadPackage();
    }

    public function actionDelete()
    {
        if ((new ExportService())->deletePackage()) {
            $this->view->success(Yii::t('LegalModule.base', 'The package has been deleted.'));
        } else {
            $this->view->error(Yii::t('LegalModule.base', 'Cannot delete the package, please try again.'));
        }

        return $this->redirect('index');
    }
}
