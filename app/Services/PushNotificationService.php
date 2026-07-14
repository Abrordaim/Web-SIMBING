<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    /**
     * Send a push notification via Expo API.
     *
     * @param string|null $to Expo push token
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Optional data payload
     * @return bool
     */
    public static function send(?string $to, string $title, string $body, array $data = []): bool
    {
        if (!$to) {
            return false;
        }

        try {
            $response = Http::post('https://exp.host/--/api/v2/push/send', [
                'to' => $to,
                'sound' => 'default',
                'title' => $title,
                'body' => $body,
                'data' => $data,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Expo Push Notification Failed: ' . $e->getMessage());
            return false;
        }
    }
}
