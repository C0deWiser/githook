<?php

namespace Codewiser\Githook\Services;

use Codewiser\Githook\Concerns\GitRepository;
use Codewiser\Githook\Concerns\Payload;
use Codewiser\Githook\Exceptions\GitHookException;
use Symfony\Component\HttpFoundation\Request;

class GitLab implements GitRepository
{
    public function __construct(protected string $secret)
    {
        //
    }

    /**
     * Secret should be equal to X-Gitlab-Token
     *
     * @throws GitHookException
     */
    public function validate(Request $request): void
    {
        $header = $request->headers->get('X-Gitlab-Token');

        if (! $header) {
            throw new GitHookException('Missing "X-Gitlab-Token" header');
        }

        if ($header != $this->secret) {
            throw new GitHookException('Signature does not match');
        }
    }

    public function payload(Request $request): Payload
    {
        return Payload::gromGitlab($request);
    }
}