<?php

declare(strict_types=1);

namespace Astaroth\LongPoll;

use Astaroth\VkUtils\Client;
use Throwable;

class LongPoll extends Client
{

    public const WAIT = 25;

    private string $key;
    private string $server;
    private string $ts;


    public function __construct(public ?int $group_id = null, int|string $version = null)
    {
        parent::__construct($version);
    }

    /**
     * Get data from longpoll server
     * @throws Throwable
     */
    private function getLongPollServer(): void
    {
        if ($this->group_id === null) {
            $request = current($this->request('groups.getById'));
            $this->group_id = $request['id'];
        }

        $longpollData = $this->request('groups.getLongPollServer', ['group_id' => $this->group_id]);
        foreach ($longpollData as $key => $value) {
            if ($key === "key") {
                $this->key = $value;
            }
            if ($key === "server") {
                $this->server = $value;
            }
            if ($key === "ts") {
                $this->ts = $value;
            }
        }
    }

    /**
     * @throws Throwable
     */
    private function fetchData(): array
    {
        $parameters =
            [
                'act' => 'a_check',
                'key' => $this->key,
                'ts' => $this->ts,
                'wait' => self::WAIT,
            ];

        return $this->base_request($this->server . '?' . http_build_query($parameters));
    }

    /**
     * @throws Throwable
     */
    public function listen(callable $callable): void
    {
        $this->getLongPollServer();
        try {
            while ($data = $this->fetchData()) {
                $this->failedHandler($data) ?: $this->parseResponse($data, $callable);
            }
        } catch (Throwable $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }

    private function failedHandler(array $data): bool
    {
        if (isset($data['failed'])) {

            if ($data['failed'] === 1) {
                $this->ts = $data['ts'];
            }

            if ($data['failed'] === 2 || $data['failed'] === 3) {
                $this->getLongPollServer();
            }

            return true;
        }

        return false;
    }

    /**
     * Loop
     * @param array $response
     * @param callable $callable
     */
    private function parseResponse(array $response, callable $callable): void
    {
        $this->ts = $response['ts'];
        $this->fork(fn() => array_walk($response['updates'], static function ($event) use ($callable) {
            $callable($event);
        }));
    }

    /**
     * Fork a process
     * @param callable $callable
     */
    private function fork(callable $callable): void
    {
        /** @noinspection LoopWhichDoesNotLoopInspection */
        /** @noinspection MissingOrEmptyGroupStatementInspection */
        /** @noinspection PhpStatementHasEmptyBodyInspection */
        while (pcntl_wait($status, WNOHANG | WUNTRACED) > 0) {
        }

        if (pcntl_fork() === 0) {
            $callable();
            posix_kill(posix_getpid(), SIGTERM);
        }
    }
}

