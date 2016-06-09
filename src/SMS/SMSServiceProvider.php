<?php namespace Selmonal\SMS;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Selmonal\SMS\Contracts\Message;
use Selmonal\SMS\Detector;
use Selmonal\SMS\Events\MessageWasFailed;
use Selmonal\SMS\Events\MessageWasSent;
use Selmonal\SMS\Transport\LogTransport;
use Selmonal\SMS\Transport\SkytelTransport;
use Selmonal\SMS\Transport\Transport;

class SMSServiceProvider extends ServiceProvider {

    protected $packageName = 'sms';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/../routes.php';

        // Register Views from your package
        $this->loadViewsFrom(__DIR__.'/../views', $this->packageName);

        // Register your migration's publisher
        $this->publishes([
            __DIR__.'/../database/migrations/' => base_path('/database/migrations')
        ], 'migrations');

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path($this->packageName.'.php'),
        ]);

        // Мессеж амжилттай явсан тохиодолд өгөгдлийн сан уруу хадгалах
        // арга хэмжээ авна.
        Event::listen('Selmonal\SMS\Events\MessageWasSent', function(MessageWasSent $event) {
            if($event->getMessage() instanceof \Selmonal\SMS\Message) {
                $event->getMessage()->setAsSent()->save();
            }
        });

        $this->app->singleton(Detector::class, function () {

            $detector = new Detector();

            foreach (Config::get('sms.formats') as $owner => $formats) {
                foreach ($formats as $format) {
                    $detector->addFormat($format, $owner);
                }
            }

            return $detector;

        });

        $this->app->singleton(Transport::class, function() {

            switch (Config::get('sms.driver')) {
                case 'log': return app(LogTransport::class); break;
                case 'skytel': return app(SkytelTransport::class); break;
            }

            throw new \InvalidArgumentException('Invalid sms driver.');
        });

        $this->app->bind(Message::class, \Selmonal\SMS\Message::class);
    }
}
