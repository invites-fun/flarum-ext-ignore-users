import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import DiscussionListItem from 'flarum/forum/components/DiscussionListItem';

export default function () {
  extend(DiscussionListItem.prototype, 'view', function (vdom) {
    if (!vdom || !vdom.attrs || !this.attrs.discussion) return;

    const user = this.attrs.discussion.user();
    if (!user || !user.ignored()) return;

    const preference =
      app.session.user?.preferences?.()?.['fof-ignore-users.ignored_discussion_behavior'] ||
      app.forum.attribute('fof-ignore-users.ignored_discussion_default_behavior') ||
      'hide';

    if (preference === 'collapse') {
      vdom.children = [
        <div className="DiscussionListItem-content">
          <a href={app.route.discussion(this.attrs.discussion)} className="DiscussionListItem-main" config={m.route}>
            <h3 className="DiscussionListItem-title">{app.translator.trans('fof-ignore-users.forum.preview.ignored_discussion_preview')}</h3>
          </a>
        </div>
      ];
    } else if (preference === 'hide') {
      if (!vdom.attrs.style) vdom.attrs.style = {};
      vdom.attrs.style.display = 'none';
    }
  });
}
