<?php

namespace App\Http\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Mailer
{
    /**
     * Function to dispatch message to configured receiver
     *
     * @return string message received from server
     */
    public function dispatchEmail($body, $to, $subject = null)
    {
        if (empty(env('URL_SENDY'))) {
            Log::info($body);
            return;
        }
        $token = $this->retrieveToken();

        $client = new Client;
        $response = $client->request('POST', env('URL_SENDY') . '/api/v1/emails',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
                'body' => json_encode(
                    [
                        'to' => $to,
                        'from' => env('EMAIL_FROM'),
                        'subject' => $subject,
                        'body' => $body

                    ]
                )
            ]);
        return json_decode((string) $response->getBody(), true)['message'];
    }

    /**
     * Function to retrieve authenticated token
     *
     * @return string token from oauth route
     */
    public function retrieveToken()
    {
        $client = new Client;

        $response = $client->post(env('URL_SENDY') . '/oauth/token', [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => env('CLIENT_ID_SENDY'),
                'client_secret' => env('CLIENT_SECRET_SENDY'),
                'scope' => '',
            ],
        ]);

        return json_decode((string) $response->getBody(), true)['access_token'];
    }
}
