<?php

namespace Webflorist\FormFactory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Webflorist\FormFactory\Components\Form\AntiBotProtection\CaptchaValidator;
use Webflorist\FormFactory\Components\Form\AntiBotProtection\HoneypotProtection;
use Webflorist\FormFactory\Components\Form\AntiBotProtection\TimeLimitProtection;
use Webflorist\FormFactory\Controllers\FormFactoryController;
use Webflorist\FormFactory\Utilities\FormFactoryTools;
use Webflorist\HtmlFactory\HtmlFactory;
use Route;
use Validator;

class FormFactoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     * @throws \Webflorist\HtmlFactory\Exceptions\DecoratorNotFoundException
     */
    public function boot()
    {

        // Publish the config.
        $this->publishes([
            __DIR__ . '/config/formfactory.php' => config_path('formfactory.php'),
        ]);

        // Load translations.
        $this->loadTranslationsFrom(__DIR__ . "/resources/lang", "webflorist-formfactory");

        // Load views.
        $this->loadViewsFrom(__DIR__.'/resources/views/', 'webflorist-formfactory');

        // Register included decorators.
        $this->registerHtmlFactoryDecorators();

        // Every time a FormRequest is resolved, we store the last used FormRequest-class in the session.
        // This is used by the CaptchaValidator and TimeLimitValidator to retrieve the corresponding FormRequest.
        $this->registerFormRequestResolverCallback();

        // Register the captcha-validator, if captcha-protection is enabled in the config.
        $this->registerCaptchaValidator();

        // Register the timeLimit-validator, if timeLimit-protection is enabled in the config.
        $this->registerTimeLimitValidator();

        // Register the honeypot-validator, if honeypot-protection is enabled in the config.
        $this->registerHoneypotValidator();

        $this->registerGetCsrfTokenRoute();

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FormFactory::class, function () {
            return new FormFactory();
        });

        // Merge the config.
        $this->mergeConfigFrom(__DIR__ . '/config/formfactory.php', 'formfactory');

    }

    /**
     * Register included decorators with HtmlFactory.
     *
     * @throws \Webflorist\HtmlFactory\Exceptions\DecoratorNotFoundException
     */
    private function registerHtmlFactoryDecorators()
    {
        /** @var HtmlFactory $htmlFactory */
        $htmlFactory = app(HtmlFactory::class);
        $htmlFactory->decorators->registerFromFolder(
            'Webflorist\FormFactory\Decorators\Bootstrap\v3',
            __DIR__ . '/Decorators/Bootstrap/v3'
        );
        $htmlFactory->decorators->registerFromFolder(
            'Webflorist\FormFactory\Decorators\Bootstrap\v4',
            __DIR__ . '/Decorators/Bootstrap/v4'
        );
    }

    /**
     * Registers a resolver-callback to store the last used FormRequest-class in the session.
     * This is used by the CaptchaValidator and TimeLimitValidator to retrieve the corresponding FormRequest.
     */
    private function registerFormRequestResolverCallback()
    {
        app()->resolving(FormRequest::class, function ($object) {
            FormFactoryTools::saveLastFormRequestObject(get_class($object));
        });
    }

    /**
     * Register the captcha-validator, if captcha-protection is enabled in the config.
     */
    private function registerCaptchaValidator()
    {
        if (config('formfactory.captcha.enabled')) {

            Validator::extendImplicit('captcha', CaptchaValidator::class . '@validate');

            // We deliver the error configured in the htmlfactory-language-file.
            Validator::replacer('captcha', function ($message, $attribute, $rule, $parameters) {
                return trans('webflorist-formfactory::formfactory.captcha_error');
            });
        }
    }

    /**
     * Register the timeLimit-validator, if timeLimit-protection is enabled in the config.
     */
    private function registerTimeLimitValidator()
    {
        if (config('formfactory.time_limit.enabled')) {

            Validator::extendImplicit('timeLimit', TimeLimitProtection::class . '@validate');

            // We deliver the error configured in the htmlfactory-language-file and replace the time-limit.
            Validator::replacer('timeLimit', function ($message, $attribute, $rule, $parameters) {

                return trans('webflorist-formfactory::formfactory.time_limit_error', [
                    'timeLimit' => TimeLimitProtection::getTimeLimitFromRuleParams($parameters)
                ]);
            });
        }
    }

    /**
     * Register the honeypot-validator, if honeypot-protection is enabled in the config.
     */
    private function registerHoneypotValidator()
    {
        if (config('formfactory.honeypot.enabled')) {

            Validator::extendImplicit('honeypot', HoneypotProtection::class . '@validate');

            // We deliver the error configured in the htmlfactory-language-file.
            Validator::replacer('honeypot', function ($message, $attribute, $rule, $parameters) {
                return trans('webflorist-formfactory::formfactory.honeypot_error');
            });
        }
    }


    /**
     * Register route to fetch new CSRF-token.
     */
    private function registerGetCsrfTokenRoute()
    {
        if (config('formfactory.vue.enabled')) {
            /** @var Router $router */
            $router = $this->app[Router::class];
            $router->get('api/csrf-token', FormFactoryController::class.'@getCsrfToken')->middleware(['web','throttle:60,1']);
        }
    }
}
