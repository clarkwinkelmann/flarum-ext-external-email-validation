import app from 'flarum/admin/app';

app.initializers.add('clarkwinkelmann-external-email-validation', () => {
    app.extensionData
        .for('clarkwinkelmann-external-email-validation')
        .registerSetting({
            setting: 'external-email-validation.method',
            type: 'select',
            label: app.translator.trans('clarkwinkelmann-external-email-validation.admin.settings.method'),
            options: {
                POST: 'POST',
                GET: 'GET',
            },
            default: 'POST',
        })
        .registerSetting({
            setting: 'external-email-validation.uri',
            type: 'text',
            label: app.translator.trans('clarkwinkelmann-external-email-validation.admin.settings.uri'),
            help: app.translator.trans('clarkwinkelmann-external-email-validation.admin.settings.uri-help'),
            placeholder: 'https://external-service.tld/api/check-email',
        })
        .registerSetting(function () {
            return m('.Form-group', [
                m('label', app.translator.trans('clarkwinkelmann-external-email-validation.admin.settings.body')),
                m('.helpText', app.translator.trans('clarkwinkelmann-external-email-validation.admin.settings.body-help')),
                m('textarea.FormControl', {
                    bidi: this.setting('external-email-validation.body'),
                })
            ]);
        })
        .registerSetting(function () {
            return m('.Form-group', [
                m('label', app.translator.trans('clarkwinkelmann-external-email-validation.admin.settings.headers')),
                m('.helpText', app.translator.trans('clarkwinkelmann-external-email-validation.admin.settings.headers-help')),
                m('textarea.FormControl', {
                    bidi: this.setting('external-email-validation.headers'),
                })
            ]);
        })
        .registerSetting({
            setting: 'external-email-validation.use-response-status',
            type: 'switch',
            label: app.translator.trans('clarkwinkelmann-external-email-validation.admin.settings.use-response-status'),
            help: app.translator.trans('clarkwinkelmann-external-email-validation.admin.settings.use-response-status-help'),
        })
        .registerSetting({
            setting: 'external-email-validation.response-success-key',
            type: 'text',
            label: app.translator.trans('clarkwinkelmann-external-email-validation.admin.settings.response-success-key'),
            help: app.translator.trans('clarkwinkelmann-external-email-validation.admin.settings.response-success-key-help'),
            placeholder: 'data.attributes.valid',
        })
        .registerSetting({
            setting: 'external-email-validation.default-error-message',
            type: 'text',
            label: app.translator.trans('clarkwinkelmann-external-email-validation.admin.settings.default-error-message'),
            help: app.translator.trans('clarkwinkelmann-external-email-validation.admin.settings.default-error-message-help'),
        })
        .registerSetting({
            setting: 'external-email-validation.response-error-key',
            type: 'text',
            label: app.translator.trans('clarkwinkelmann-external-email-validation.admin.settings.response-error-key'),
            help: app.translator.trans('clarkwinkelmann-external-email-validation.admin.settings.response-error-key-help'),
            placeholder: 'data.attributes.error',
        });
});
