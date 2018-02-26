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
        $client = new Client();
        $request = $client->createRequest()
            ->setMethod('POST')
            ->setFormat(Client::FORMAT_JSON)
            ->setUrl("{$this->githubApiUrl}/repos/{$this->owner}/{$this->repository}/issues")
            ->setHeaders(['Authorization' => "token {$this->accessToken}", 'User-Agent' => "{$this->userAgent}"]);

        foreach ($this->messages as $message) {
            list($text, $level, $category, $timestamp) = $message;
            if (!is_string($text)) {
                // exceptions may not be serializable if in the call stack somewhere is a Closure
                if ($text instanceof \Throwable || $text instanceof \Exception) {
                    $text = (string) $text;
                } else {
                    $text = VarDumper::export($text);
                }
            }
            $prefix = $this->getMessagePrefix($message);

            $request->setData([
                'title' => "{$prefix}[$level][$category]",
                'body' => $text
            ]);
            $response = $request->send();
            if (!$response->isOk) {
                throw new LogRuntimeException('Unable to export log through GithubTarget!');
            }
        }
    }
}
