<?php

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

if (!function_exists('getFcmAccessToken')) {

    function getFcmAccessToken(): string
    {
        $serviceAccountPath = config('services.fcm.service_account');

        if (!file_exists($serviceAccountPath)) {
            throw new \RuntimeException('Service account file not found: ' . $serviceAccountPath);
        }

        $credentials = new ServiceAccountCredentials(
            'https://www.googleapis.com/auth/firebase.messaging',
            $serviceAccountPath
        );

        $token = $credentials->fetchAuthToken();

        if (empty($token['access_token'])) {
            throw new \RuntimeException('Failed to get access token: ' . json_encode($token));
        }

        return $token['access_token'];
    }
}
