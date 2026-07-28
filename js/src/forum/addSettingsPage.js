import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';

import SettingsPage from 'flarum/forum/components/SettingsPage';
import FieldSet from 'flarum/common/components/FieldSet';
import Select from 'flarum/common/components/Select';

export default () => {
  extend(SettingsPage.prototype, 'settingsItems', function (items) {
    const preferences = this.user.preferences();

    const behaviorOptions = {
      label: app.translator.trans('fof-ignore-users.forum.settings.default_behavior_options.label'),
      collapse: app.translator.trans('fof-ignore-users.forum.settings.default_behavior_options.collapse'),
      hide: app.translator.trans('fof-ignore-users.forum.settings.default_behavior_options.hide'),
      block: app.translator.trans('fof-ignore-users.forum.settings.default_behavior_options.block'),
    };

    const notificationOptions = {
      allow: app.translator.trans('fof-ignore-users.forum.settings.notification_behavior_options.allow'),
      block: app.translator.trans('fof-ignore-users.forum.settings.notification_behavior_options.block'),
    };

    items.add(
      'fof-ignore-users',
      FieldSet.component(
        {
          label: app.translator.trans('fof-ignore-users.forum.settings.settings_heading'),
          className: 'Settings-ignoreUsers',
        },
        [
          <div className="Form-group">
            <p>{app.translator.trans('fof-ignore-users.forum.settings.ignored_discussion_default_behavior_label')}</p>

            {Select.component({
              options: behaviorOptions,
              value:
                preferences['fof-ignore-users.ignored_discussion_behavior'] ||
                app.forum.attribute('fof-ignore-users.ignored_discussion_default_behavior') ||
                'hide',
              onchange: (value) => {
                this.user.savePreferences({ 'fof-ignore-users.ignored_discussion_behavior': value }).then(() => m.redraw());
              },
            })}

            <p className="helpText">{app.translator.trans('fof-ignore-users.forum.settings.ignored_discussion_default_behavior_help')}</p>
          </div>,

          <div className="Form-group">
            <p>{app.translator.trans('fof-ignore-users.forum.settings.ignored_post_default_behavior_label')}</p>

            {Select.component({
              options: behaviorOptions,
              value:
                preferences['fof-ignore-users.ignored_post_behavior'] ||
                app.forum.attribute('fof-ignore-users.ignored_post_default_behavior') ||
                'hide',
              onchange: (value) => {
                this.user.savePreferences({ 'fof-ignore-users.ignored_post_behavior': value }).then(() => m.redraw());
              },
            })}

            <p className="helpText">{app.translator.trans('fof-ignore-users.forum.settings.ignored_post_default_behavior_help')}</p>
          </div>,

          <div className="Form-group">
            <p>{app.translator.trans('fof-ignore-users.forum.settings.ignored_notification_default_behavior_label')}</p>

            {Select.component({
              options: notificationOptions,
              value:
                preferences['fof-ignore-users.ignored_notification_behavior'] ||
                app.forum.attribute('fof-ignore-users.ignored_notification_default_behavior') ||
                'block',
              onchange: (value) => {
                this.user.savePreferences({ 'fof-ignore-users.ignored_notification_behavior': value }).then(() => m.redraw());
              },
            })}

            <p className="helpText">{app.translator.trans('fof-ignore-users.forum.settings.ignored_notification_default_behavior_help')}</p>
          </div>,
        ]
      ),
      -11
    );
  });
};
