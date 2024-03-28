<?php

namespace humhub\modules\legal\controllers;

use Yii;
use humhub\modules\user\components\BaseAccountController;
use humhub\modules\legal\services\ExportService;
use yii\web\Response;

/**
 * ExportController handles exporting user data as JSON.
 */
class ExportController extends BaseAccountController
{
    private $exportService;

    public function __construct($id, $module, ExportService $exportService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->exportService = $exportService;
    }

    /**
     * Renders the view displaying the exported user data as JSON.
     *
     * @return string The rendered view.
     */
    public function actionIndex()
    {
        return $this->renderAjax('index');
    }

    /**
     * Downloads the exported user data as a JSON file.
     *
     * @return Response The file response.
     * @throws \yii\base\Exception If the REST module is not enabled.
     */
    public function actionDownload()
    {
        // Check if the REST module is enabled
        if (!Yii::$app->hasModule('rest')) {
            throw new \yii\base\Exception('REST module is not enabled.');
        }

        $currentUser = Yii::$app->user->getIdentity();

        $userData = $this->exportService->getUserData($currentUser);
        $postData = $this->exportService->getPostData($currentUser);
        $commentData = $this->exportService->getCommentData($currentUser);
        $fileData = $this->exportService->getFileData($currentUser);
        $likeData = $this->exportService->getLikeData($currentUser);

        // Combine user, post, comment, file, and like data
        $data = [
            'user' => $userData,
            'post' => $postData,
            'comment' => $commentData,
            'file' => $fileData,
            'like' => $likeData,
        ];

        // Convert data to JSON
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);

        // Set headers for JSON file download
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->headers->add('Content-Type', 'application/json');
        Yii::$app->response->headers->add('Content-Disposition', 'attachment; filename="userdata.json"');

        // Output JSON data to response body
        Yii::$app->response->content = $jsonData;

        // Send the response
        return Yii::$app->response;
    }
}
