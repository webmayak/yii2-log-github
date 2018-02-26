# yii2-log-github

### Install

```composer require webmayak/yii2-log-github```

### Usage

```php
'components' => [
     'log' => [
          'targets' => [
              [
                  'class' => 'webmayak\yii2LogGithub\GithubTarget',
                  'levels' => ['error', 'warning'],
                  'accessToken' => '...',
                  'owner' => '...',
                  'repository' => '...',
              ],
          ],
     ],
],
```
