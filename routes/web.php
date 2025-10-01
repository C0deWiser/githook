<?php

use Codewiser\Githook\Exceptions\GitHookException;
use Codewiser\Githook\GitHook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

Route::post('githook', function (Request $request, GitHook $hook) {
    try {
        $hook->handle($request);
    } catch (GitHookException $exception) {
        throw new AccessDeniedHttpException($exception->getMessage());
    }
});