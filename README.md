# GitHub/GitLab webhooks

This package provides unified service for handling both GitHub and GitLab 
webhooks.

Add webhook secret to `/config/services.php` file:

```php
return [
    'githook' => [
        // required
        'secret' => env('GITHOOK_SECRET'),
        // optional
        'logger' => env('GITHOOK_LOG_CHANNEL'),
    ]
]
```

Register webhook:

Payload URL: `{your-host}/githook`

Content type: `application/json`

Secret: `GITHOOK_SECRET` value

Observe `\Codewiser\Githook\Events\GithookArrived` event:

```php
use Codewiser\Githook\Events\GithookArrived;

class GitHookListener
{
    public function handle(GithookArrived $githook): void 
    {
        // $githook->provider;
        // $githook->payload;
    }
}
```