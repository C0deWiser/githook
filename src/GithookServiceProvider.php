<?php

namespace Codewiser\Githook;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class GithookServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GitHook::class, function () {
            $githook = new GitHook(config('services.githook.secret'));

            $githook->setDispatcher($this->app['events']);
            $githook->setRequest($this->app->refresh('request', $githook, 'setRequest'));

            if ($logger = config('services.githook.logger')) {
                $githook->setLogger(Log::channel($logger));
            }

            return $githook;
        });
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }
}