<?php

namespace humhub\modules\legal\jobs;

use humhub\modules\legal\services\ExportService;
use humhub\modules\queue\LongRunningActiveJob;
use humhub\modules\user\models\User;
use Yii;

class ExportJob extends LongRunningActiveJob
{
    public $userId;

    public function run()
    {
        // Set the cache component explicitly to FileCache
        Yii::$app->set('cache', [
            'class' => 'yii\caching\FileCache',
        ]);

        // Access the export service
        $exportService = Yii::$container->get(ExportService::class);

        // Get the current user identity using the provided user ID
        $currentUser = User::findOne(['id' => $this->userId]);
        if (!$currentUser) {
            Yii::error('User is not authenticated.');
            return false;
        }

        // Perform data extraction
        $userData = $exportService->getUserData($currentUser);
        $postData = $exportService->getPostData($currentUser);
        $commentData = $exportService->getCommentData($currentUser);
        $fileData = $exportService->getFileData($currentUser);
        $likeData = $exportService->getLikeData($currentUser);

        // Combine data
        $data = [
            'user' => $userData,
            'post' => $postData,
            'comment' => $commentData,
            'file' => $fileData,
            'like' => $likeData,
        ];

        // Convert data to JSON
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);

        if ($jsonData === false) {
            Yii::error('Failed to encode data to JSON.');
            return false;
        }

        // Generate unique directory path based on user ID or username
        $uniquePath = Yii::getAlias('@runtime/exportedData/user_' . $currentUser->id . '/');
        if (!file_exists($uniquePath)) {
            mkdir($uniquePath, 0777, true);
        }

        // Save JSON data to a file
        $filePath = $uniquePath . 'userdata.json';
        if (file_put_contents($filePath, $jsonData) === false) {
            Yii::error('Failed to save JSON data to file: ' . $filePath);
            return false;
        }

        // Set expiration date (1 month from now)
        $expirationDate = strtotime('+1 month');
        if (file_put_contents($filePath . '.expire', $expirationDate) === false) {
            Yii::error('Failed to save expiration date to file.');
            return false;
        }

        // Log export completion
        Yii::info('User data exported successfully.');

        return $filePath;
    }

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        // Run the job
        $filePath = $this->run();

        if ($filePath === false) {
            Yii::error('Export job failed.');
            return;
        }

        // Send the file for download
        if (file_exists($filePath)) {
            $fileName = pathinfo($filePath, PATHINFO_BASENAME);
            $fileSize = filesize($filePath);

            // Set headers for JSON file download
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Length: ' . $fileSize);

            // Send the file content
            readfile($filePath);
        } else {
            Yii::error('File does not exist: ' . $filePath);
        }

        // Delete expired files
        $this->cleanupExpiredFiles($filePath);
    }

    private function cleanupExpiredFiles($filePath)
    {
        $exportedDataDir = dirname($filePath);
        $currentTimestamp = time();

        foreach (glob("$exportedDataDir/*.json.expire") as $expireFile) {
            $expirationTimestamp = intval(file_get_contents($expireFile));
            if ($expirationTimestamp <= $currentTimestamp) {
                $dataFile = str_replace('.expire', '', $expireFile);
                if (file_exists($dataFile)) {
                    unlink($dataFile);
                }
                unlink($expireFile);
            }
        }
    }
}
