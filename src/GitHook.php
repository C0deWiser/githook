<?php

namespace Codewiser\Githook;

use Codewiser\Githook\Concerns\Carrier;
use Codewiser\Githook\Concerns\Payload;
use Codewiser\Githook\Concerns\Validator;
use Codewiser\Githook\Events\GithookArrived;
use Codewiser\Githook\Exceptions\GitHookException;
use Codewiser\Githook\Services\GitHub;
use Codewiser\Githook\Services\GitLab;
use Codewiser\Githook\Services\Unknown;
use Illuminate\Contracts\Events\Dispatcher;
use Symfony\Component\HttpFoundation\Request;

class GitHook
{
    protected ?Request $request = null;
    protected ?Dispatcher $events = null;

    public function __construct(protected string $secret)
    {
        //
    }

    /**
     * @throws GitHookException
     */
    public function handle(Request $request): Payload
    {
        $this->setRequest($request);

        $this->validate();
        return $this->payload();
    }

    /**
     * Get the event dispatcher instance.
     */
    public function getDispatcher(): ?Dispatcher
    {
        return $this->events;
    }

    /**
     * Set the event dispatcher instance.
     */
    public function setDispatcher(Dispatcher $events): void
    {
        $this->events = $events;
    }

    /**
     * Get the current request instance.
     */
    public function getRequest(): Request
    {
        return $this->request ?: Request::createFromGlobals();
    }

    /**
     * Set the current request instance.
     */
    public function setRequest(Request $request): static
    {
        $this->request = $request;

        return $this;
    }

    public function service(): Validator&Carrier
    {
        if ($this->getRequest()->headers->has('X-Gitlab-Token')) {
            return new GitLab(config('services.githook.secret'));
        }

        if ($this->getRequest()->headers->has('X-Hub-Signature-256')) {
            return new GitHub(config('services.githook.secret'));
        }

        return new Unknown();
    }

    /**
     * @throws GitHookException
     */
    public function validate(): void
    {
        $this->service()->validate($this->getRequest());
    }

    public function payload(): Payload
    {
        $payload = $this->service()->payload($this->getRequest());

        $this->getDispatcher()?->dispatch(new GithookArrived($payload));

        return $payload;
    }
}