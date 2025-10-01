<?php

namespace Codewiser\Githook\Services;

use Codewiser\Githook\Concerns\Carrier;
use Codewiser\Githook\Concerns\Payload;
use Codewiser\Githook\Concerns\Validator;
use Codewiser\Githook\Exceptions\GitHookException;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerAwareTrait;

class GitLab implements Validator, Carrier
{
    use LoggerAwareTrait;

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
            $this->logger?->warning('Missing "X-Gitlab-Token" header');
            throw new GitHookException('Missing "X-Gitlab-Token" header');
        }

        if ($header != $this->secret) {
            $this->logger?->warning('Signature does not match');
            throw new GitHookException('Signature does not match');
        }
    }

    public function payload(Request $request): Payload
    {
        return Payload::gromGitlab($request);
    }
}