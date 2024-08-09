<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\legal\services;

use humhub\modules\legal\Module;
use humhub\modules\user\models\User;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class ExportService
{
    private const PACKAGE_GENERATED = 'legal.packageDate';

    private ?User $user = null;

    public function __construct()
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('User must be logged in!');
        }

        $this->user = Yii::$app->user->getIdentity();
    }

    public function generatePackage(): bool
    {
        $this->user->settings->set(self::PACKAGE_GENERATED, time());

        // TODO: Create a package

        return true;
    }

    public function downloadPackage(): Response
    {
        // TODO: Download the package
        $content = $this->user->settings->get(self::PACKAGE_GENERATED);

        return Yii::$app->response->sendContentAsFile($content, 'user.json');
    }

    public function deletePackage(): bool
    {
        $this->user->settings->delete(self::PACKAGE_GENERATED);

        // TODO: Delete the package

        return true;
    }

    public function hasPackage(): bool
    {
        return $this->user->settings->get(self::PACKAGE_GENERATED) > 0;
    }

    public function getModule(): Module
    {
        return Yii::$app->getModule('legal');
    }
}
