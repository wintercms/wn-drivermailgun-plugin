<?php namespace Winter\DriverMailgun;

use App;
use Event;
use System\Classes\PluginBase;
use System\Models\MailSetting;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Bridge\Mailgun\Transport\MailgunTransportFactory;

/**
 * DriverMailgun Plugin Information File
 */
class Plugin extends PluginBase
{
    public $elevated = true;
    
    const MODE_MAILGUN = 'mailgun';

    public function pluginDetails()
    {
        return [
            'name'        => 'winter.drivermailgun::lang.plugin.name',
            'description' => 'winter.drivermailgun::lang.plugin.description',
            'homepage'    => 'https://github.com/wintercms/wn-drivermailgun-plugin',
            'author'      => 'Winter CMS',
            'icon'        => 'icon-leaf',
        ];
    }

    public function register()
    {
        Event::listen('mailer.beforeRegister', function ($mailManager) {
            $mailManager->extend(self::MODE_MAILGUN, function ($config) {
                $factory = new MailgunTransportFactory();

                if (!isset($config['secret'])) {
                    $config = $this->app['config']->get('services.mailgun', []);
                }

                return $factory->create(new Dsn(
                    'mailgun+'.($config['scheme'] ?? 'https'),
                    $config['endpoint'] ?? 'default',
                    $config['secret'],
                    $config['domain']
                ));
            });

            $settings = MailSetting::instance();
            if ($settings->send_mode === self::MODE_MAILGUN) {
                $config = App::make('config');
                $config->set('mail.mailers.mailgun.transport', self::MODE_MAILGUN);
                $config->set('services.mailgun.domain', $settings->mailgun_domain);
                $config->set('services.mailgun.endpoint', $settings->mailgun_endpoint);
                $config->set('services.mailgun.secret', $settings->mailgun_secret);
            }
        });
    }

    public function boot()
    {
        MailSetting::extend(function ($model) {
            $model->bindEvent('model.beforeValidate', function () use ($model) {
                $model->rules['mailgun_endpoint'] = 'required_if:send_mode,' . self::MODE_MAILGUN;
                $model->rules['mailgun_domain'] = 'required_if:send_mode,' . self::MODE_MAILGUN;
                $model->rules['mailgun_secret'] = 'required_if:send_mode,' . self::MODE_MAILGUN;
            });
            $model->mailgun_endpoint = config('services.mailgun.endpoint', env('MAILGUN_ENDPOINT'));
            $model->mailgun_domain = config('services.mailgun.domain', env('MAILGUN_DOMAIN'));
            $model->mailgun_secret = config('services.mailgun.secret', env('MAILGUN_SECRET'));
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
                'mailgun_endpoint' => [
                    'tab'     => 'system::lang.mail.general',
                    'type'    => 'dropdown',
                    'label'   => 'winter.drivermailgun::lang.mailgun_endpoint',
                    'options' => [
                        'api.mailgun.net' => 'US - api.mailgun.net',
                        'api.eu.mailgun.net' => 'EU - api.eu.mailgun.net',
                    ],
                    'commentAbove' => 'winter.drivermailgun::lang.mailgun_endpoint_comment',
                    'span'    => 'full',
                    'trigger' => [
                        'action'    => 'show',
                        'field'     => 'send_mode',
                        'condition' => 'value[mailgun]',
                    ],
                    'default' => config('services.mailgun.endpoint'),
                ],
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
                    'default' => config('services.mailgun.domain'),
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
                    'default' => config('services.mailgun.secret'),
                ],
            ]);
        });
    }
}
