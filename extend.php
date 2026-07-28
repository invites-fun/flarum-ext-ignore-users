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

use Flarum\Api\Controller\ShowForumController;
use Flarum\Api\Serializer;
use Flarum\Extend;
use Flarum\Http\RequestUtil;
use Flarum\User\Event\Saving;
use Flarum\User\Search\UserSearcher;
use Flarum\User\User;
use FoF\IgnoreUsers\User\Search\Gambit\IgnoredGambit;

return [
    new Extend\Locales(__DIR__.'/resources/locale'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/resources/less/forum.less')
        ->route('/ignoredUsers', 'ignored.users.view'),

    (new Extend\Model(User::class))
        ->relationship('ignoredUsers', function (User $model) {
            return $model->belongsToMany(User::class, 'ignored_user', 'user_id', 'ignored_user_id')
            ->withPivot('ignored_at');
        })
        ->relationship('ignoredBy', function (User $model) {
            return $model->belongsToMany(User::class, 'ignored_user', 'ignored_user_id', 'user_id')
            ->withPivot('ignored_at');
        }),

    (new Extend\ApiSerializer(Serializer\BasicUserSerializer::class))
        ->attribute('ignored', function (Serializer\BasicUserSerializer $serializer, User $user) {
            $actor = $serializer->getActor();

            /** @phpstan-ignore-next-line */
            return !$user->can('notBeIgnored') && IgnoreState::isIgnored($actor->id, $user->id);
        })
        ->attribute('canBeIgnored', function (Serializer\BasicUserSerializer $serializer, User $user) {
            return (bool) $serializer->getActor()->can('ignore', $user);
        }),

    (new Extend\Policy())
        ->modelPolicy(User::class, Access\UserPolicy::class)
        ->modelPolicy(User::class, Access\ByobuPolicy::class),

    (new Extend\Event())
        ->listen(Saving::class, Listener\SaveIgnoredToDatabase::class),

    (new Extend\SimpleFlarumSearch(UserSearcher::class))
        ->addGambit(IgnoredGambit::class),

    (new Extend\ApiController(ShowForumController::class))
        ->addInclude('actor.ignoredUsers')
        ->prepareDataForSerialization(function ($controller, $data, $request) {
            $actor = RequestUtil::getActor($request);
            if (!$actor->isGuest()) {
                IgnoreState::preload($actor->id);
            }
        }),

    (new Extend\Settings())
        ->serializeToForum('fof-ignore-users.ignored_discussion_default_behavior', 'fof-ignore-users.ignored_discussion_default_behavior')
        ->serializeToForum('fof-ignore-users.ignored_post_default_behavior', 'fof-ignore-users.ignored_post_default_behavior'),

        (new Extend\User())
        ->registerPreference('fof-ignore-users.ignored_discussion_behavior', function ($value) {
            return $value;
        },  resolve('flarum.settings')->get('fof-ignore-users.ignored_discussion_default_behavior', 'hide'))
        ->registerPreference('fof-ignore-users.ignored_post_behavior', function ($value) {
            return $value;
        }, resolve('flarum.settings')->get('fof-ignore-users.ignored_post_default_behavior', 'hide')),

];
