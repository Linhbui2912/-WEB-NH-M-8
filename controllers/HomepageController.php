<?php

declare(strict_types=1);

require_once __DIR__ . '/../modules/db_module.php';
require_once __DIR__ . '/../models/FeedModel.php';

class HomepageController
{
    /** @return list<array<string,mixed>> */
    public static function getFeed(string $viewerId): array
    {
        $link = null;
        taoKetNoi($link);

        try {
            return FeedModel::fetchPublicFeed($link, $viewerId);
        } finally {
            giaiPhongBoNho($link, true);
        }
    }
}
