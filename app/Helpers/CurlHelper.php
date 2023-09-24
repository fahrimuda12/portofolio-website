<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class CurlHelper
{
    public $url;

    public $buildQuery = true;

    public $withSession = false;

    public $cookieFile = null;

    public $redirectURL = null;

    public $followLocation = false;

    public $isDelete = false;

    public $isPut = false;

    public $removeResponseHeader = false;

    public $isPatch = false;

    public $statusCode;

    public function __construct($url)
    {
        $this->url = $url;
        $this->buildQuery = true;
    }

    private function setCookieFile($ch)
    {
        if ($this->withSession) {
            if (is_null($this->cookieFile)) {
                $cookieName = temporary_file_path(random_new_filename('txt'));
                file_put_contents($cookieName, '');
                $this->cookieFile = $cookieName;
            }

            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        }
    }

    public function destroy()
    {
        if (!is_null($this->cookieFile) && is_file($this->cookieFile)) {
            unlink($this->cookieFile);
            $this->cookieFile = null;
        }
    }

    public function post($data = [], $headers = [], $secure = true)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 2);

            if ($this->isPut) {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            }

            if ($this->followLocation) {
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            }

            self::setCookieFile($ch);

            if (is_array($data)) {
                curl_setopt($ch, CURLOPT_POST, count($data));

                if ($this->buildQuery) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                } else {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                }
            } else {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }

            if ($this->isPatch) {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            }

            if (!empty($headers)) {
                $curlHeader = [];
                foreach ($headers as $key => $value) {
                    $curlHeader[] = "$key: $value";
                }
                curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeader);
            }
            if (strpos($this->url, 'https://') !== false) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            }

            $response = curl_exec($ch);
            if (curl_error($ch)) {
                $error = curl_error($ch);
                Log::error('error', ['curl_error' => $error]);
                if (strpos($error, 'SSL certificate problem') !== false && $secure == true) {
                    return self::post($data, $headers, false);
                }

                curl_close($ch);
                return ['code' => 500, 'output' => $error];
            }

            if (preg_match('~Location: (.*)~i', $response, $match)) {
                $this->redirectURL = trim($match[1]);
            }

            if ($this->removeResponseHeader) {
                $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $response = substr($response, $headerSize);
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $this->statusCode = $httpCode;

            curl_close($ch);

            return ['code' => 200, 'output' => $response];
        } catch (\Exception $exception) {
            Log::error('error', ['curl_exception' => $exception->getMessage()]);
            return ['code' => 500, 'output' => $exception->getMessage()];
        }
    }

    public function get($data = [], $headers = [], $secure = true)
    {
        try {
            $params = http_build_query($data);
            $ch = curl_init();

            if (count($data) == 0) {
                curl_setopt($ch, CURLOPT_URL, $this->url);
            } else {
                curl_setopt($ch, CURLOPT_URL, $this->url . "?$params");
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 2);

            if ($this->isDelete) {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            }

            if ($this->followLocation) {
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            }

            self::setCookieFile($ch);

            if (!empty($headers)) {
                $curlHeader = [];
                foreach ($headers as $key => $value) {
                    $curlHeader[] = "$key: $value";
                }
                curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeader);
            }

            if (strpos($this->url, 'https://') !== false) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            }

            $response = curl_exec($ch);
            if (curl_error($ch)) {
                $error = curl_error($ch);
                Log::error('error', ['curl_error' => $error]);
                if (strpos($error, 'SSL certificate problem') !== false && $secure == true) {
                    return self::get($data, $headers, false);
                }

                curl_close($ch);
                return ['code' => 500, 'output' => $error];
            }

            if (preg_match('~Location: (.*)~i', $response, $match)) {
                $this->redirectURL = trim($match[1]);
            }

            if ($this->removeResponseHeader) {
                $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                $response = substr($response, $headerSize);
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $this->statusCode = $httpCode;

            curl_close($ch);

            return ['code' => 200, 'output' => $response];
        } catch (\Exception $exception) {
            Log::error('error', ['curl_exception' => $exception->getMessage()]);
            return ['code' => 500, 'output' => $exception->getMessage()];
        }
    }
}
