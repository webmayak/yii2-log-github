# yii2-log-github

Provides GithubTarget component for Yii2 logging system. Creates issues from application exceptions. You should specify your GitHub repository name, owner name and your PAT (personal access token) with `repo` permissions enabled.

### Install

```composer require webmayak/yii2-log-github```

### Usage

```php
'components' => [
     'log' => [
          'targets' => [
              [
                  'class' => 'webmayak\yii2LogGithub\GithubTarget',
                  'enabled' => true,
                  'except' => ['yii\web\HttpException:404', 'yii\web\HttpException:403'],
                  'levels' => ['error', 'warning'],
                  'accessToken' => '...',
                  'owner' => '...',
                  'repository' => '...',
              ],
          ],
     ],
],
```
