<?php

namespace Codewiser\Githook;

use Codewiser\Githook\Concerns\GitRepository;
use Codewiser\Githook\Concerns\Payload;
use Codewiser\Githook\Events\GithookArrived;
use Codewiser\Githook\Exceptions\GitHookException;
use Codewiser\Githook\Services\GitHub;
use Codewiser\Githook\Services\GitLab;
use Codewiser\Githook\Services\Unknown;
use Illuminate\Contracts\Events\Dispatcher;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

class GitHook
{
    use LoggerAwareTrait;

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
        return $this
            ->setRequest($request)
            ->validate()
            ->toPayload();
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

    public function service(): GitRepository
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
    public function validate(): static
    {
        $service = $this->service();

        try {
            $service->validate($this->getRequest());
        } catch (GitHookException $e) {

            $this->logger?->error(class_basename($service).' '.$e->getMessage());

            throw $e;
        }

        return $this;
    }

    public function toPayload(): Payload
    {
        $service = $this->service();

        $payload = $service->payload($this->getRequest());

        $this->logger?->info(class_basename($service).' '.$payload->event, $payload->data);

        $this->getDispatcher()?->dispatch(new GithookArrived($service::class, $payload));

        return $payload;
    }
}