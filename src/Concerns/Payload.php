<?php

namespace Codewiser\Githook\Concerns;

use Codewiser\Githook\Services\GitHub;
use Codewiser\Githook\Services\GitLab;
use Symfony\Component\HttpFoundation\Request;

class Payload
{
    public static function gromGitlab(Request $request): static
    {
        return new static(
            GitLab::class,
            $request->headers->get('X-Gitlab-Webhook-UUID'),
            $request->headers->get('X-Gitlab-Event'),
            json_decode($request->getContent(), true)
        );
    }

    public static function gromGithub(Request $request): static
    {
        return new static(
            GitHub::class,
            $request->headers->get('X-GitHub-Delivery'),
            $request->headers->get('X-GitHub-Event'),
            json_decode($request->getContent(), true)
        );
    }

    public function __construct(
        public string $provider,
        public string $id,
        public string $event,
        public array $payload
    )
    {
        //
    }
}