<?php namespace Winter\MailgunDriver;

use App;
use Backend;
use Backend\Models\UserRole;
use Event;
use System\Classes\PluginBase;
use System\Models\MailSetting;

use Winter\Storm\Mail\MailManager;

use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Bridge\Mailgun\Transport\MailgunTransportFactory;

/**
 * SendgridDriver Plugin Information File
 */
class Plugin extends PluginBase
{
    const MODE_MAILGUN = 'mailgun';

    public function pluginDetails()
    {
        return [
            'name'        => 'Mailgun driver',
            'description' => 'winter.mailgundriver:lang.plugin_description',
            'author'      => 'Winter',
            'icon'        => 'icon-leaf'
        ];
    }

    public function register()
    {
        Event::listen('mailer.beforeRegister', function ($mailManager) {
            $settings = MailSetting::instance();
            if ($settings->send_mode === self::MODE_MAILGUN) {
                $config = App::make('config');
                $config->set('mail.mailers.mailgun.transport', self::MODE_MAILGUN);
                $config->set('services.mailgun.api_key', $settings->mailgun_api_key);
            }
        });
    }

    public function boot()
    {
        MailSetting::extend(function ($model) {
            $model->bindEvent('model.beforeValidate', function () use ($model) {
                $model->rules['mailgun_api_key'] = 'required_if:send_mode,' . self::MODE_MAILGUN;
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
            $field->options(array_merge($field->options(), [self::MODE_MAILGUN => "Mailgun"]));

            $widget->addTabFields([
                'mailgun_api_key' => [
                    "tab"     => "system::lang.mail.general",
                    'label'   => 'winter.mailgundriver::lang.fields.mailgun_api_key.label',
                    'commentAbove' => 'winter.mailgundriver::lang.fields.mailgun_api_key.comment',
                    'trigger' => [
                        'action'    => 'show',
                        'field'     => 'send_mode',
                        'condition' => 'value[mailgun]'
                    ]
                ],
            ]);
        });
    }
}
