import app from 'flarum/admin/app';

app.initializers.add('fof-ignore-users', () => {
  app.extensionData
    .for('fof-ignore-users')
    .registerPermission(
      {
        icon: 'fas fa-comment-slash',
        label: app.translator.trans('fof-ignore-users.admin.permissions.can_not_be_ignored_label'),
        permission: 'notBeIgnored',
      },
      'reply'
    )
    .registerSetting({
      setting: 'fof-ignore-users.ignored_discussion_default_behavior',
      label: app.translator.trans('fof-ignore-users.admin.settings.ignored_discussion_default_behavior_label'),
      type: 'select',
      options: {
        label: app.translator.trans('fof-ignore-users.admin.settings.default_behavior_options.label'),
        hide: app.translator.trans('fof-ignore-users.admin.settings.default_behavior_options.hide'),
        block: app.translator.trans('fof-ignore-users.admin.settings.default_behavior_options.block'),
      },
      default: 'hide',
    })
    .registerSetting({
      setting: 'fof-ignore-users.ignored_post_default_behavior',
      label: app.translator.trans('fof-ignore-users.admin.settings.ignored_post_default_behavior_label'),
      type: 'select',
      options: {
        label: app.translator.trans('fof-ignore-users.admin.settings.default_behavior_options.label'),
        hide: app.translator.trans('fof-ignore-users.admin.settings.default_behavior_options.hide'),
        block: app.translator.trans('fof-ignore-users.admin.settings.default_behavior_options.block'),
      },
      default: 'hide',
    });
});
