<?php
declare(strict_types = 1);

namespace webtok;
use Exception;

class Error extends Exception
{
    public $kind;

    function __construct(string $kind, string $message = "")
    {
        parent::__construct($message);
        $this->kind = $kind;
    }
}

function sign(string $secret, array $data): array // [?string, ?Error]
{
    if (!isset($data["eat"]) || $data["eat"] <= time())
    {
        return [null, new Error("eat", "invalid eat")];
    }

    $data = json_encode($data);

    if (json_last_error() !== JSON_ERROR_NONE)
    {
        return [null, new Error("data", "invalid json")];
    }

    $data = base64_encode($data);
    $header = base64_encode(hash_hmac("sha256", $data, $secret));

    return ["$header.$data", null];
}

function verify(string $secret, string $tok): array // [?array, ?Error]
{
    $parts = explode(".", $tok);

    if (count($parts) !== 2)
    {
        return [null, new Error("token", "invalid token")];
    }

    $header = base64_decode($parts[0]);
    $data = $parts[1];

    if (!hash_equals(hash_hmac("sha256", $data, $secret), $header))
    {
        return [null, new Error("header", "headers do not match")];
    }

    $data = json_decode(base64_decode($data), true);

    if (json_last_error() !== JSON_ERROR_NONE)
    {
        return [null, new Error("data", "invalid json")];
    }

    if ($data["eat"] <= time())
    {
        return [null, new Error("eat", "expired token")];
    }

    return [$data, null];
}
