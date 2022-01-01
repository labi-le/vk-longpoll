<?php

declare(strict_types=1);

namespace Astaroth\Longpoll;

use RuntimeException;

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

        $ch = curl_init("$url?$query_params");
        curl_setopt_array($ch,
            [
                CURLOPT_RETURNTRANSFER => true,
            ]
        );

        $stream = curl_exec($ch);

        if ($stream === false) {
            throw new RuntimeException("Failed to connect to VKontakte: " . curl_error($ch));
        }

        return @json_decode($stream, true) ?: [];
    }

    public function apiCall(string $method, array $params = [])
    {
        $params["access_token"] = $this->access_token;
        $params["v"] = $this->v;

        $data = $this->call(self::API_ENDPOINT . $method, $params);

        if (isset($data["error"])) {
            $error = $data["error"];
            throw new RuntimeException($error["error_msg"], $error["error_code"]);
        }

        return $data["response"] ?? $data;
    }
}