<?php

namespace FoF\IgnoreUsers\Scope;

use Flarum\User\User;
use FoF\IgnoreUsers\IgnoreState;
use Illuminate\Database\Eloquent\Builder;

class HideIgnoredDiscussionsScope
{
    public function __invoke(User $actor, Builder $query)
    {
        if ($actor->isGuest()) {
            return;
        }

        if ($actor->getPreference('fof-ignore-users.ignored_discussion_behavior', 'hide') !== 'block') {
            return;
        }

        $ignoredUserIds = IgnoreState::getIds($actor->id);

        if (empty($ignoredUserIds)) {
            return;
        }

        $query->whereNotIn('discussions.user_id', $ignoredUserIds);
    }
}
