<?php

namespace Codewiser\Githook\Services;

use Codewiser\Githook\Concerns\GitRepository;
use Codewiser\Githook\Concerns\Payload;
use Codewiser\Githook\Exceptions\GitHookException;
use Symfony\Component\HttpFoundation\Request;

class Unknown implements GitRepository
{
    public function validate(Request $request): void
    {
        throw new GitHookException("Unresolved request");
    }

    public function payload(Request $request): Payload
    {
        throw new GitHookException("Unresolved request");
    }
}