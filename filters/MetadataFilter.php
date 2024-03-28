<?php

namespace humhub\modules\legal\filters;

use Yii;

/**
 * Class MetadataFilter
 * Provides methods to filter out unneeded metadata from the provided content.
 *
 * @package humhub\modules\legal\filters
 */
class MetadataFilter
{
    /**
     * Filter out unneeded metadata from the provided content.
     *
     * This method filters out unnecessary metadata from each content item, such as comments not belonging to the current user.
     *
     * @param array $content The array of content items to be filtered.
     * @return array The filtered content array with unneeded metadata removed.
     */
    public static function filterMetadata($content)
    {
        return array_map(function ($item) {
            if (isset($item['content']['comments']['latest'])) {
                $currentUser = Yii::$app->user->getIdentity();
                $latestComments = $item['content']['comments']['latest'];
                
                // Filter out comments that don't belong to the current user
                $filteredComments = array_filter($latestComments, function ($comment) use ($currentUser) {
                    return $comment['createdBy']['id'] === $currentUser->id;
                });
                
                // Update the latest comments with the filtered comments
                $item['content']['comments']['latest'] = array_values($filteredComments);
            }
            return $item;
        }, $content);
    }
}
