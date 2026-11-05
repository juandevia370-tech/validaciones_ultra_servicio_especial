<?php

class SupabaseAuth
{
    private $url;
    private $publishableKey;

    public function __construct(array $config)
    {
        $this->url = rtrim($config['url'], '/');
        $this->publishableKey = $config['publishable_key'];
    }

    public function verify($accessToken)
    {
        $response = $this->request(
            '/auth/v1/user',
            'GET',
            $accessToken
        );

        if ($response['status'] !== 200 || !is_array($response['data'])) {
            return $response;
        }

        return [
            'status' => 200,
            'data' => $response['data'],
            'error' => null,
        ];
    }

    public function refresh($refreshToken)
    {
        return $this->request(
            '/auth/v1/token?grant_type=refresh_token',
            'POST',
            null,
            ['refresh_token' => $refreshToken]
        );
    }

    private function request($path, $method, $accessToken = null, ?array $body = null)
    {
        if (!function_exists('curl_init')) {
            error_log('The PHP cURL extension is required for Supabase authentication.');

            return ['status' => 503, 'data' => null, 'error' => 'cURL is unavailable.'];
        }

        $headers = [
            'apikey: ' . $this->publishableKey,
            'Accept: application/json',
        ];

        if ($accessToken !== null) {
            $headers[] = 'Authorization: Bearer ' . $accessToken;
        }

        $options = [
            CURLOPT_URL => $this->url . $path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CUSTOMREQUEST => $method,
        ];

        if ($body !== null) {
            $options[CURLOPT_POSTFIELDS] = json_encode($body);
            $headers[] = 'Content-Type: application/json';
            $options[CURLOPT_HTTPHEADER] = $headers;
        }

        $request = curl_init();
        curl_setopt_array($request, $options);
        $content = curl_exec($request);
        $status = (int) curl_getinfo($request, CURLINFO_HTTP_CODE);
        $error = curl_error($request);
        curl_close($request);

        if ($content === false) {
            error_log('Supabase authentication request failed: ' . $error);

            return ['status' => 503, 'data' => null, 'error' => 'Supabase is unavailable.'];
        }

        $data = json_decode($content, true);

        return [
            'status' => $status,
            'data' => $data,
            'error' => $status >= 400 ? 'Supabase rejected the request.' : null,
        ];
    }
}
