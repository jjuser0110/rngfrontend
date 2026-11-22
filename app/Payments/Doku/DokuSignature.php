<?php

namespace App\Payments\Doku;

/**
 * DOKU's request signature (non-SNAP), used both for our calls to DOKU and to check the notifications DOKU sends us.
 *
 *   Client-Id:{id}\nRequest-Id:{id}\nRequest-Timestamp:{time}\nRequest-Target:{path}[\nDigest:{digest}]
 *   Signature header = "HMACSHA256=" + base64(HMAC-SHA256(secret key, the lines above))
 *
 * Digest = base64(SHA-256 of the exact JSON body), only when there is a body (POST).
 */
class DokuSignature
{
    public static function digest(string $body): string
    {
        return base64_encode(hash('sha256', $body, true));
    }

    public static function make(string $clientId, string $requestId, string $timestamp, string $target, ?string $body, string $secretKey): string
    {
        $lines = [
            'Client-Id:' . $clientId,
            'Request-Id:' . $requestId,
            'Request-Timestamp:' . $timestamp,
            'Request-Target:' . $target,
        ];
        if ($body !== null) {
            $lines[] = 'Digest:' . self::digest($body);
        }

        return 'HMACSHA256=' . base64_encode(hash_hmac('sha256', implode("\n", $lines), $secretKey, true));
    }

    public static function matches(string $expected, ?string $given): bool
    {
        return $given !== null && hash_equals($expected, trim($given));
    }
}
