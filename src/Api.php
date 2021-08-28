<?php

declare(strict_types=1);

namespace Astaroth\LongPoll;

final class Api
{
    public const API_ENDPOINT = "https://api.vk.com/method/";

    private string $access_token;
    private string $v;

    public function __construct(string $access_token, string $v)
    {
        $this->access_token = $access_token;
        $this->v = $v;
    }

    /**
     * @param string $url
     * @param array $params
     * @return array
     */
    public function call(string $url, array $params = []): array
    {
        $query_params = http_build_query($params);
        $raw = file_get_contents($url . "?" . $query_params);

        return @json_decode($raw, true) ?: [];
    }

    public function apiCall(string $method, array $params = [])
    {
        $params["access_token"] = $this->access_token;
        $params["v"] = $this->v;

        $data = $this->call(static::API_ENDPOINT . $method, $params);

        return $data["response"] ?? $data;
    }
}