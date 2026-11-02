<?php

namespace App\Security;

use Google_Client;

readonly class GoogleIdTokenVerifier
{
    private Google_Client $client;
    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setClientId($_ENV['OAUTH_GOOGLE_CLIENT_ID']);
        $this->client->setClientSecret($_ENV['OAUTH_GOOGLE_CLIENT_SECRET']);
        $this->client->setRedirectUri($_ENV['OAUTH_GOOGLE_REDIRECT_URI']);
        $this->client->addScope("email");
        $this->client->addScope("profile");
    }

    public function verifyIdToken(string $token) : false | array
    {
        $payload = $this->client->verifyIdToken($token);

        if (!$payload) return false;
        if (isset($payload['error'])) return false;
        if (!$payload['email_verified']) return false;

        return $this->dataExtraction($payload);
    }

    public function verifyCode(string $code) : false | array
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        dump($token);

        if (isset($token['error'])) return false;

        $payload = $this->client->verifyIdToken($token['id_token']);
        dump($payload);

        if (!$payload) return false;
        if (!$payload['email_verified']) return false;

        return $this->dataExtraction($payload);

    }

    private function dataExtraction(array $payload) :array
    {
        return [
            'sub'     => $payload['sub'],
            'email'   => $payload['email'],
            'name'    => $payload['name'] ?? null,
            'picture' => $payload['picture'] ?? null,
        ];
    }
}
