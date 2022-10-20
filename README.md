# laravel-eurosms

## License
MIT © Anton Adamec

## Instalation
Add to /app/Providers/AppServiceProvider

```php
<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Tonci14\LaravelEuroSMS\EuroSmsService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EuroSmsService::class, function () {
            return new EuroSmsService(
                config('sms.eurosms.integrationKey'),
                config('sms.eurosms.integrationID'),
                config('sms.eurosms.senderName')
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
```


## Example Usage
### synchronous
```php
    $smsService->send("+421****", "Test sms content");
```
### asynchronous
```php
    $smsService->sendAsync("+421****", "Test sms content");
```
