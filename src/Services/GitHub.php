<?php

namespace Codewiser\Githook\Services;

use Codewiser\Githook\Concerns\Carrier;
use Codewiser\Githook\Concerns\Payload;
use Codewiser\Githook\Concerns\Validator;
use Codewiser\Githook\Exceptions\GitHookException;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerAwareTrait;

class GitHub implements Validator, Carrier
{
    use LoggerAwareTrait;

    public function __construct(protected string $secret)
    {
        //
    }

    /**
     * sha256 hash from request body, spiced with a secret,
     * should be compliant with X-Hub-Signature-256
     *
     * @throws GitHookException
     */
    public function validate(Request $request): void
    {
        $header = $request->headers->get('X-Hub-Signature-256');

        if (! $header) {
            $this->logger?->warning('Missing "X-Hub-Signature-256" header');
            throw new GitHookException('Missing "X-Hub-Signature-256" header');
        }

        $signature = str($header)->after('sha256=')->toString();

        if (! $signature) {
            $this->logger?->warning('Wrong "X-Hub-Signature-256" header');
            throw new GitHookException('Wrong "X-Hub-Signature-256" header');
        }

        $computed = hash_hmac('sha256', $request->getContent(), $this->secret);

        if (! hash_equals($signature, $computed)) {
            $this->logger?->warning('Signature does not match');
            throw new GitHookException('Signature does not match');
        }
    }

    public function payload(Request $request): Payload
    {
        return Payload::gromGithub($request);
    }
}