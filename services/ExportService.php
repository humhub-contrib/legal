<?php

namespace humhub\modules\legal\services;

use Yii;
use humhub\modules\file\models\File;
use humhub\modules\like\models\Like;
use humhub\modules\post\models\Post;
use humhub\modules\comment\models\Comment;
use humhub\modules\legal\filters\MetadataFilter;
use humhub\modules\rest\definitions\UserDefinitions;
use humhub\modules\rest\definitions\PostDefinitions;
use humhub\modules\rest\definitions\FileDefinitions;
use humhub\modules\rest\definitions\LikeDefinitions;
use humhub\modules\rest\definitions\CommentDefinitions;

class ExportService
{
    /**
     * Retrieves user data.
     *
     * @param $currentUser
     * @return array
     */
    public function getUserData($currentUser)
    {
        $currentUser = Yii::$app->user->getIdentity();
        if (\Yii::$app->hasModule('rest')) {
            return UserDefinitions::getUser($currentUser);
        } else {
            return $currentUser->attributes;
        }
    }

    /**
     * Retrieves current user's posts and returns as JSON.
     *
     * @param $currentUser
     * @return array
     */
    public function getPostData($currentUser)
    {
        $currentUser = Yii::$app->user->getIdentity();
        if (\Yii::$app->hasModule('rest')) {
            $currentUserId = $currentUser->id;
            $userPosts = Post::find()
                ->where(['created_by' => $currentUserId])
                ->all();

            // Convert posts to array and apply metadata filtering
            $postsArray = array_map(function ($post) {
                return PostDefinitions::getPost($post);
            }, $userPosts);

            // Apply metadata filtering
            $filteredPosts = MetadataFilter::filterMetadata($postsArray);

            return $filteredPosts;
        } else {
            return [];
        }
    }

    /**
     * Retrieves current user's files and returns as JSON.
     *
     * @param $currentUser
     * @return array
     */
    public function getFileData($currentUser)
    {
        $currentUser = Yii::$app->user->getIdentity();
        if (\Yii::$app->hasModule('rest')) {
            $userFiles = File::find()
                ->where(['created_by' => $currentUser->id])
                ->all();

            // Convert files to array and apply metadata filtering
            $filesArray = array_map(function ($file) {
                return FileDefinitions::getFile($file);
            }, $userFiles);

            // Apply metadata filtering
            $filteredFiles = MetadataFilter::filterMetadata($filesArray);

            return $filteredFiles;
        } else {
            return [];
        }
    }

    /**
     * Retrieves current user's likes and returns as JSON.
     *
     * @param $currentUser
     * @return array
     */
    public function getLikeData($currentUser)
    {
        $currentUser = Yii::$app->user->getIdentity();
        if (\Yii::$app->hasModule('rest')) {
            $userLikes = Like::find()
                ->where(['created_by' => $currentUser->id])
                ->all();

            // Convert likes to array and apply metadata filtering
            $likesArray = array_map(function ($like) {
                return LikeDefinitions::getLike($like);
            }, $userLikes);

            // Apply metadata filtering
            $filteredLikes = MetadataFilter::filterMetadata($likesArray);

            return $filteredLikes;
        } else {
            return [];
        }
    }

    /**
     * Retrieves current user's comments and returns as JSON.
     *
     * @param $currentUser
     * @return array
     */
    public function getCommentData($currentUser)
    {
        $currentUser = Yii::$app->user->getIdentity();
        if (\Yii::$app->hasModule('rest')) {
            $userComments = Comment::find()
                ->where(['created_by' => $currentUser->id])
                ->all();

            // Convert comments to array and apply metadata filtering
            $commentsArray = array_map(function ($comment) {
                return CommentDefinitions::getComment($comment);
            }, $userComments);

            // Apply metadata filtering
            $filteredComments = MetadataFilter::filterMetadata($commentsArray);

            return $filteredComments;
        } else {
            return [];
        }
    }
}
