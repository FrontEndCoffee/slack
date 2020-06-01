<a href="https://uptimeproject.io" target="_blank"><img src="https://uptimeproject.io/img/logo.png" height="50px" /></a>

A minimal but flexible integration for slack webhooks.

## How to use

```php
<?php
use UptimeProject\Slack\Workspace;

$workspace = new Workspace('https://hooks.slack.com/services/blablabla');
$workspace->from('John')->send('Hello!');
```

### Adding profile icons

If no icon is given, the default icon for the integration is used. 

```php
<?php
use UptimeProject\Slack\Workspace;

$workspace = new Workspace('https://hooks.slack.com/services/blablabla');
// You can use an icon as avatar
$workspace->from('John', ':tophat:')->send('Hello!');

// Or an image from the web
$imgUrl = 'https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png';
$workspace->from('John', $imgUrl)->send('Hello!');
```

<img src="https://i.imgur.com/Qrv3Byk.png" width="250px" />

### Specifying a channel

If no channel name is given, the default channel for the webhook is used.

```php
<?php
use UptimeProject\Slack\Workspace;

$workspace = new Workspace('https://hooks.slack.com/services/blablabla');

// Send message to a specific channel
$workspace->from('John')->send('Hello!', '#general');
```

## How to develop

Feel free to create a PR if you have any ideas for improvements. Or create an issue.

* When adding code, make sure to add tests for it (phpunit).
* Make sure the code adheres to our coding standards (use php-cs-fixer to check/fix). 
* Also make sure PHPStan does not find any bugs.

```bash

vendor/bin/php-cs-fixer fix

vendor/bin/phpstan analyze

vendor/bin/phpunit --coverage-text

```

These tools will also run in Github Actions on PR's and pushes on master.
