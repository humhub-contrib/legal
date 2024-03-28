<?php

namespace humhub\modules\legal\jobs;

use humhub\modules\queue\ActiveJob;
use Yii;

class ExportJob extends ActiveJob
{
    public $exportedData;

    public function run()
    {
        // Log export completion
        Yii::info('User data exported successfully.');
    }

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        return $this->run();
    }
}
