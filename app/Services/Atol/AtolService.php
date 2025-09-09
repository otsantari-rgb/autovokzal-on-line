<?php

namespace App\Services\Atol;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class AtolService
{
    protected string $token;
    protected string $apiVersion;
    protected string $groupCode;
    protected string $apiUrl;
    protected string $login;
    protected string $password;

    public function __construct(string $apiVersion, string $groupCode, string $apiUrl, string $login, string $password) {
        $this->apiVersion = $apiVersion;
        $this->groupCode = $groupCode;
        $this->apiUrl = $apiUrl;
        $this->login = $login;
        $this->password = $password;
        $response = Http::withHeaders([
            'Content-Type' => 'application/json; charset=utf-8',
        ])->post("$this->apiUrl/$this->apiVersion/getToken", [
            'login' => $this->login,
            'pass' => $this->password,
        ]);

        $data = $response->json();

        if (isset($data['token'])) {
            $this->token = $data['token'];
        }
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function registerDocument(string $operation, $document)
    {
        if (empty($this->token)) {
            throw new Exception('Unauthorized. Please get token first.');
        }

        $allowedOperations = ['sell', 'sell_refund', 'sell_correction', 'buy', 'buy_refund', 'buy_correction'];

        if (!in_array($operation, $allowedOperations)) {
            throw new InvalidArgumentException('Invalid operation type.');
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json; charset=utf-8',
            'Token' => $this->token,
        ])->post("$this->apiUrl/$this->apiVersion/$this->groupCode/$operation", $document);

        return $response->json();
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function getDocumentReport($uuid)
    {
        if (empty($this->token)) {
            throw new Exception('Unauthorized. Please get token first.');
        }

        $response = Http::withHeaders([
            'Token' => $this->token,
        ])->get("$this->apiUrl/$this->apiVersion/$this->groupCode/report/$uuid");

        return $response->json();
    }
}
