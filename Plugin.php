<?php namespace Winter\DriverMailgun;

use App;
use Event;
use System\Classes\PluginBase;
use System\Models\MailSetting;

/**
 * DriverMailgun Plugin Information File
 */
class Plugin extends PluginBase
{
    const MODE_MAILGUN = 'mailgun';

    public function pluginDetails()
    {
        return [
            'name'        => 'winter.drivermailgun::lang.plugin_name',
            'description' => 'winter.drivermailgun::lang.plugin_description',
            'homepage'    => 'https://github.com/wintercms/wn-drivermailgun-plugin',
            'author'      => 'Winter CMS',
            'icon'        => 'icon-leaf',
        ];
    }

    public function register()
    {
        Event::listen('mailer.beforeRegister', function ($mailManager) {
            $settings = MailSetting::instance();
            if ($settings->send_mode === self::MODE_MAILGUN) {
                $config = App::make('config');
                $config->set('mail.mailers.mailgun.transport', self::MODE_MAILGUN);
                $config->set('services.mailgun.domain', $settings->mailgun_domain);
                $config->set('services.mailgun.secret', $settings->mailgun_secret);
            }
        });
    }

    public function boot()
    {
        MailSetting::extend(function ($model) {
            $model->bindEvent('model.beforeValidate', function () use ($model) {
                $model->rules['mailgun_domain'] = 'required_if:send_mode,' . self::MODE_MAILGUN;
                $model->rules['mailgun_secret'] = 'required_if:send_mode,' . self::MODE_MAILGUN;
            });
        });

        Event::listen('backend.form.extendFields', function ($widget) {
            if (!$widget->getController() instanceof \System\Controllers\Settings) {
                return;
            }
            if (!$widget->model instanceof MailSetting) {
                return;
            }

            $field = $widget->getField('send_mode');
            $field->options(array_merge($field->options(), [self::MODE_MAILGUN => 'Mailgun']));

            $widget->addTabFields([
                'mailgun_domain' => [
                    'tab'     => 'system::lang.mail.general',
                    'label'   => 'winter.drivermailgun::lang.mailgun_domain',
                    'commentAbove' => 'winter.drivermailgun::lang.mailgun_domain_comment',
                    'span'    => 'left',
                    'trigger' => [
                        'action'    => 'show',
                        'field'     => 'send_mode',
                        'condition' => 'value[mailgun]',
                    ],
                ],
                'mailgun_secret' => [
                    'tab'     => 'system::lang.mail.general',
                    'label'   => 'winter.drivermailgun::lang.mailgun_secret',
                    'commentAbove' => 'winter.drivermailgun::lang.mailgun_secret_comment',
                    'span'    => 'right',
                    'type'    => 'sensitive',
                    'trigger' => [
                        'action'    => 'show',
                        'field'     => 'send_mode',
                        'condition' => 'value[mailgun]',
                    ],
                ],
            ]);
        });
    }
}
