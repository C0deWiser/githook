<?php

namespace Codewiser\Githook\Concerns;

use Codewiser\Githook\Exceptions\GitHookException;
use Symfony\Component\HttpFoundation\Request;

interface Validator
{
    /**
     * @throws GitHookException
     */
    public function validate(Request $request): void;
}