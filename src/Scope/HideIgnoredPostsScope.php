<?php

namespace FoF\IgnoreUsers\Scope;

use Flarum\User\User;
use FoF\IgnoreUsers\IgnoreState;
use Illuminate\Database\Eloquent\Builder;

class HideIgnoredPostsScope
{
    public function __invoke(User $actor, Builder $query)
    {
        if ($actor->isGuest()) {
            return;
        }

        if ($actor->getPreference('fof-ignore-users.ignored_post_behavior', 'hide') !== 'block') {
            return;
        }

        $ignoredUserIds = IgnoreState::getIds($actor->id);

        if (empty($ignoredUserIds)) {
            return;
        }

        $query->whereNotIn('posts.user_id', $ignoredUserIds);
    }
}
