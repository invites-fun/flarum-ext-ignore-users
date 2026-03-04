<?php

/*
 * This file is part of fof/ignore-users.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\IgnoreUsers;

use Flarum\Database\AbstractModel;

/**
 * @property int    $user_id
 * @property int    $ignored_user_id
 * @property string $ignored_at
 */
class IgnoreState extends AbstractModel
{
    protected $table = 'ignored_user';

    protected $fillable = ['user_id', 'ignored_user_id', 'ignored_at'];

    /**
     * Cache ignored user IDs.
     *
     * @var array<int, int[]>
     */
    private static array $ignoredIdsCache = [];

    /**
     * Preload all ignored user IDs for the given actor in a single query.
     */
    public static function preload(int $actorId): void
    {
        if (isset(self::$ignoredIdsCache[$actorId])) {
            return;
        }

        self::$ignoredIdsCache[$actorId] = self::where('user_id', $actorId)
            ->pluck('ignored_user_id')
            ->map(fn($id) => (int) $id)
            ->all();
    }

    /**
     * Check whether $actorId ignores $userId.
     * Calls preload() lazily if not already populated.
     */
    public static function isIgnored(int $actorId, int $userId): bool
    {
        if (!isset(self::$ignoredIdsCache[$actorId])) {
            self::preload($actorId);
        }

        return in_array($userId, self::$ignoredIdsCache[$actorId], true);
    }

    /**
     * Return the cached array of ignored user IDs for the given actor.
     * Calls preload() lazily if not already populated.
     *
     * @return int[]
     */
    public static function getIds(int $actorId): array
    {
        if (!isset(self::$ignoredIdsCache[$actorId])) {
            self::preload($actorId);
        }

        return self::$ignoredIdsCache[$actorId];
    }

    /**
     * Invalidate the cache for an actor after an ignore/unignore write.
     */
    public static function invalidateCache(int $actorId): void
    {
        unset(self::$ignoredIdsCache[$actorId]);
    }
}
