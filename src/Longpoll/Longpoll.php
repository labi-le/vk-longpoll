<?php

declare(strict_types=1);

namespace Astaroth\Longpoll;

use Astaroth\Contracts\HandlerInterface;
use Exception;
use Throwable;

/**
 * @author labile
 * @see https://vk.com/dev/bots_longpoll
 */
final class Longpoll implements HandlerInterface
{
    private int $wait = 25;
    private string $key;
    private string $server;

    private string $ts;
    private int $group_id;
    private Api $api;


    public function __construct(string $access_token, string $v, int $group_id = null)
    {
        $this->api = new Api($access_token, $v);

        if ($group_id === null) {
            $request = $this->api->apiCall("groups.getById")[0];
            $this->group_id = $request["id"];
        } else {
            $this->group_id = $group_id;
        }
    }

    /**
     * @param int $second
     * @return static
     */
    public function setWait(int $second)
    {
        $this->wait = $second;
        return $this;
    }

    /**
     * Get data from longpoll server
     * @throws Throwable
     */
    private function getLongPollServer(): void
    {
        foreach ($this->api->apiCall("groups.getLongPollServer",
            [
                "group_id" => $this->group_id
            ]
        ) as $key => $value) {
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
        return $this->api->call($this->server,
            [
                "act" => "a_check",
                "key" => $this->key,
                "ts" => $this->ts,
                "wait" => $this->wait,
            ]
        );
    }

    /**
     * User-callable handler
     * @throws Throwable
     */
    public function listen(callable $func): void
    {
        $this->getLongPollServer();
        try {
            while ($data = $this->fetchData()) {
                $this->failedHandler($data) ?: $this->parseResponse($data, $func);
            }
        } catch (Exception $e) {
            throw new VkLongpollException($e->getMessage(), $e->getCode());
        }
    }

    private function failedHandler(array $data): bool
    {
        if (isset($data["failed"])) {

            if ($data["failed"] === 1) {
                $this->ts = $data["ts"];
            }

            if ($data["failed"] === 2 || $data["failed"] === 3) {
                $this->getLongPollServer();
            }

            return true;
        }

        return false;
    }

    /**
     * Parse the response from the server and create a child process so as not to wait for the execution of the user-callable
     * @param array $response
     * @param callable $callable
     */
    private function parseResponse(array $response, callable $callable): void
    {
        $this->ts = $response["ts"];

        $this->fork(function () use ($callable, $response) {
            foreach ($response["updates"] as $event) {
                $callable($event);
            }
        });
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

