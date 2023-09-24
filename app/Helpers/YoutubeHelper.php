<?php

namespace App\Helpers;

class YoutubeHelper
{
    CONST YOUTUBE_BASE_EMBED_VIDEO_URL = 'https://www.youtube.com/embed/';
    CONST YOUTUBE_BASE_EMBED_THUMBNAIL_URL = 'http://img.youtube.com/vi/';
    CONST YOUTUBE_BASE_EMBED_THUMBNAIL_QUALITY_URL = '/hqdefault.jpg';

    public static function fetchVideoId($link)
    {
        if (strpos($link, '/embed/') > -1) {
            $fetch = explode('/embed/', $link);
            return count($fetch) > 1 ? $fetch[1] : null;
        }

        $fetchVideoId = (strpos($link, 'youtu.be') > -1) ? explode('be/', $link) : explode('=', $link);
        $videoId = count($fetchVideoId) > 1 ? substr($fetchVideoId[1], 0, 11) : null;
        return $videoId;

    }

    public static function generateEmbededVideoUrl($link)
    {
        $videoId = self::fetchVideoId($link);
        if (is_null($videoId)) {
            return null;
        }

        return self::YOUTUBE_BASE_EMBED_VIDEO_URL.$videoId;
    }

    public static function generateEmbededThumbnailUrl($link)
    {
        $videoId = self::fetchVideoId($link);
        if (is_null($videoId)) {
            return null;
        }

        return self::YOUTUBE_BASE_EMBED_THUMBNAIL_URL.$videoId.self::YOUTUBE_BASE_EMBED_THUMBNAIL_QUALITY_URL;
    }
}
