<?php

namespace Codewiser\Githook\Concerns;

use Codewiser\Githook\Exceptions\GitHookException;
use Symfony\Component\HttpFoundation\Request;

interface GitRepository
{
    public function payload(Request $request): Payload;

    /**
     * @throws GitHookException
     */
    public function validate(Request $request): void;
}