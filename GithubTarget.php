<?php

namespace webmayak\yii2LogGithub;

use yii\log\Target;
use yii\log\LogRuntimeException;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\helpers\VarDumper;

class GithubTarget extends Target
{

    public $githubApiUrl = 'https://api.github.com';

    public $accessToken;

    public $owner;

    public $repository;

    public $userAgent = 'yii2 github issue collector @webmayak';

    public function init()
    {
        parent::init();
        if (empty($this->accessToken)) {
            throw new InvalidConfigException('The "accessToken" option must be specified.');
        }
        if (empty($this->owner)) {
            throw new InvalidConfigException('The "owner" option must be specified.');
        }
        if (empty($this->repository)) {
            throw new InvalidConfigException('The "repository" option must be specified.');
        }
    }

    public function export()
    {
        list($text, $level, $category, $timestamp) = $this->messages[0];
        $prefix = $this->getMessagePrefix($this->messages[0]);
        $title = "{$prefix}[$level][$category]";
        
        $messages = array_map([$this, 'formatMessage'], $this->messages);
        $body = "```\n" . wordwrap(implode("\n", $messages), 70) . "\n```";
        
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setFormat(Client::FORMAT_JSON)
            ->setUrl("{$this->githubApiUrl}/repos/{$this->owner}/{$this->repository}/issues")
            ->setHeaders(['Authorization' => "token {$this->accessToken}", 'User-Agent' => "{$this->userAgent}"])
            ->setData(['title' => $title, 'body' => $body])
            ->send();
        if (!$response->isOk) {
            throw new LogRuntimeException('Unable to export log through GithubTarget!');
        }
    }
}
