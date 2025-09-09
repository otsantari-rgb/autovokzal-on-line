<?php

namespace App\Providers;

use App\Clients\BiletAvtoApiClient;
use App\Services\Atol\AtolService;
use App\Services\PaymentEventHandler;
use App\Services\PaymentService;
use App\Services\RefundService;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Vite;
use YooKassa\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerBiletAvtoApiClient();
        $this->registerYooKassaClient();
        $this->registerAtolService();
        $this->registerPaymentEventHandler();
        $this->registerPaymentService();
        $this->registerRefundService();
    }

    /**
     * Регистрация клиента BiletAvtoApiClient.
     */
    private function registerBiletAvtoApiClient(): void
    {
        $this->app->singleton(BiletAvtoApiClient::class, function () {
            return new BiletAvtoApiClient(
                config('services.biletavto.url'),
                config('services.biletavto.username'),
                config('services.biletavto.password')
            );
        });
    }

    /**
     * Регистрация клиента YooKassa.
     */
    private function registerYooKassaClient(): void
    {
        $this->app->singleton(Client::class, function () {
            $client = new Client();
            $client->setAuth(
                config('services.yookassa.shopId'),
                config('services.yookassa.secretKey')
            );

            return $client;
        });
    }

    /**
     * Регистрация обработчика событий платежей.
     */
    private function registerPaymentEventHandler(): void
    {
        $this->app->singleton(PaymentEventHandler::class, function ($app) {
            return new PaymentEventHandler(
                $app->make(BiletAvtoApiClient::class),
                $app->make(Client::class),
                $app->make(AtolService::class),
            );
        });
    }

    /**
     * Регистрация PaymentService.
     */
    private function registerPaymentService(): void
    {
        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService($app->make(Client::class));
        });
    }

    /**
     * Регистрация RefundService.
     */
    private function registerRefundService(): void
    {
        $this->app->singleton(RefundService::class, function ($app) {
            return new RefundService(
                $app->make(BiletAvtoApiClient::class),
                $app->make(Client::class)
            );
        });
    }

    private function registerAtolService(): void
    {
        $this->app->singleton(AtolService::class, function () {
            return new AtolService(
                config('services.atol.api_version'),
                config('services.atol.group_code'),
                config('services.atol.api_url'),
                config('services.atol.login'),
                config('services.atol.password')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        Vite::macro('style', fn (string $asset) => $this->content("resources/css/{$asset}"));
        // Vite::macro('image', fn (string $asset) => $this->asset("resources/img/{$asset}"));
    }
}
