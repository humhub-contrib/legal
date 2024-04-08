<?php

namespace humhub\modules\legal\filters;

use Yii;

/**
 * Provides methods to filter out unneeded metadata from the provided content.
 */
class MetadataFilter
{
    /**
     * Filter out unneeded metadata from the provided content.
     *
     * @param array $content The array of content items to be filtered.
     * @return array The filtered content array with unneeded metadata removed.
     */
    public static function filterMetadata($content)
    {
        return array_map(function ($item) {
            // Remove URL from user metadata if it exists
            if (isset($item['user']['url'])) {
                unset($item['url']);
            }

            // Remove comments, likes, and files from content metadata if they exist
            if (isset($item['content']['comments'])) {
                unset($item['content']['comments']);
                unset($item['content']['files']);
                unset($item['content']['likes']);
                unset($item['content']['metadata']['created_by']['url']);
                unset($item['content']['metadata']['updated_by']['url']);
            }

            // Remove URL from comment metadata if it exists
            if (isset($item['createdBy']['url'])) {
                unset($item['createdBy']['url']);
                unset($item['updatedBy']['url']);
                unset($item['likes']);
                unset($item['files']);
            }

            // Remove URL from file metadata if it exists
            if (isset($item['file'])) {
                foreach ($item['file'] as &$file) {
                    unset($file['url']);
                }
            }

            // Remove URL from like metadata if it exists
            if (isset($item['like'])) {
                foreach ($item['like'] as &$like) {
                    unset($like['createdBy']['url']);
                    unset($like['updatedBy']['url']);
                }
            }

            return $item;
        }, $content);
    }
}
