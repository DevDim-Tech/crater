<?php

namespace Crater\Providers;

use Illuminate\Support\ServiceProvider;
use Crater\AI\Interfaces\AIServiceInterface;
use Crater\AI\Services\OpenAIService;

class AIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AIServiceInterface::class, OpenAIService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
