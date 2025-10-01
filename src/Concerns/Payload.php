<?php

namespace Codewiser\Githook\Concerns;

use Symfony\Component\HttpFoundation\Request;

class Payload
{
    public static function gromGitlab(Request $request): static
    {
        return new static(
            $request->headers->get('X-Gitlab-Webhook-UUID'),
            $request->headers->get('X-Gitlab-Event'),
            json_decode($request->getContent(), true)
        );
    }

    public static function gromGithub(Request $request): static
    {
        return new static(
            $request->headers->get('X-GitHub-Delivery'),
            $request->headers->get('X-GitHub-Event'),
            json_decode($request->getContent(), true)
        );
    }

    /**
     * @param  string  $id  Unique webhook identifier.
     * @param  string  $event  Event name.
     * @param  array  $data  Webhook payload.
     */
    public function __construct(
        public string $id,
        public string $event,
        public array $data
    ) {
        //
    }
}