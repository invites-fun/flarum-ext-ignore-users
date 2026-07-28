<?php

namespace FoF\IgnoreUsers\Listener;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\User\User;
use FoF\IgnoreUsers\IgnoreState;

class FilterIgnoredNotifications
{
    /**
     * @param BlueprintInterface $blueprint
     * @param User[] $users
     * @return User[]
     */
    public function __invoke(BlueprintInterface $blueprint, array $users): array
    {
        if (! method_exists($blueprint, 'getFromUser')) {
            return $users;
        }

        $fromUser = $blueprint->getFromUser();
        if (! $fromUser) {
            return $users;
        }

        $fromUserId = $fromUser->id;

        $userIdsToCheck = [];
        foreach ($users as $user) {
            if ($user->id !== $fromUserId && $user->getPreference('fof-ignore-users.ignored_notification_behavior', 'block') === 'block') {
                $userIdsToCheck[] = $user->id;
            }
        }

        if (empty($userIdsToCheck)) {
            return $users;
        }

        $ignoringUserIds = IgnoreState::where('ignored_user_id', $fromUserId)
            ->whereIn('user_id', $userIdsToCheck)
            ->pluck('user_id')
            ->toArray();

        if (empty($ignoringUserIds)) {
            return $users;
        }

        foreach ($users as $key => $user) {
            if (in_array($user->id, $ignoringUserIds)) {
                unset($users[$key]);
            }
        }

        return array_values($users);
    }
}
