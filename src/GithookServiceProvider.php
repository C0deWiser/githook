<?php

namespace Codewiser\Githook;

use Illuminate\Support\ServiceProvider;

class GithookServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GitHook::class, function () {
            $githook = new GitHook(config('services.githook.secret'));

            $githook->setDispatcher($this->app['events']);
            $githook->setRequest($this->app->refresh('request', $githook, 'setRequest'));

            return $githook;
        });
    }
}