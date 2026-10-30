<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
// use RuntimeException;

class DragonPayServices
{
    private $keysFile;

    private $productionKeysEndpoint =
        'https://gw.dragonpay.ph/api/collect/v1/keys/callback';

    private $testKeysEndpoint =
        'https://test.dragonpay.ph/api/collect/v1/keys/callback';


public function __construct()
{
    $this->keysFile = storage_path(env('DRAGONPAY_KEY_PATH'));
}

    /**
     * Verify Dragonpay callback signature.
     *
     * First attempts verification using locally cached public keys.
     * If verification fails, fetches the latest keys from Dragonpay
     * and retries verification once.
     */
    public function verifySignature(array $requestData): bool
    {
        $requiredFields = [
            'txnid',
            'refno',
            'status',
            'message',
            'merchantid',
            'amount',
            'signatures',
        ];

        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $requestData)) {
                throw new RuntimeException(
                    "Missing required Dragonpay field: {$field}"
                );
            }
        }

        /*
         * Build the exact message required by Dragonpay.
         */
        $message = implode(':', [
            $requestData['txnid'],
            $requestData['refno'],
            $requestData['status'],
            $requestData['message'],
            env('DRAGONPAY_MERCHANT'),
            $requestData['param1'] ?? '',
            $requestData['param2'] ?? '',
            number_format((float) $requestData['amount'], 2, '.', ''),
        ]);

        $signature = base64_decode(
            $requestData['signatures'],
            true
        );

        if ($signature === false) {
            Log::warning('Dragonpay callback contains invalid Base64 signature.');

            return false;
        }

        $messageBytes = $message;

        /*
         * ---------------------------------------------------------
         * ATTEMPT 1
         * ---------------------------------------------------------
         * Verify using locally cached active keys.
         */
        $knownKeys = $this->getLocalActiveKeys();

        if ($this->verifyWithKeys(
            $messageBytes,
            $signature,
            $knownKeys
        )) {
            return true;
        }

        /*
         * ---------------------------------------------------------
         * RETRY
         * ---------------------------------------------------------
         * Signature failed. Dragonpay may have rotated its keys.
         */
        Log::warning(
            'Dragonpay signature verification failed. Fetching latest keys.'
        );

        try {
            $latestKeys = $this->fetchPublicKeys();

            if (empty($latestKeys)) {
                Log::error(
                    'Dragonpay returned no active public keys.'
                );

                throw new RuntimeException(
                    'Dragonpay returned no active public keys.'
                );
            }

            /*
             * Save the latest keys.
             */
            $this->updateCollectionKeys($latestKeys);

            /*
             * -----------------------------------------------------
             * ATTEMPT 2
             * -----------------------------------------------------
             * Retry verification using fresh Dragonpay keys.
             */
            return $this->verifyWithKeys(
                $messageBytes,
                $signature,
                $latestKeys->where('Status', 'Active')->pluck('Value')->values()->toArray()
            );

        } catch (\Throwable $e) {
            Log::error(
                'Failed to generate new signatures key.',
                [
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json(
                ['message' => 'Failed to generate new signatures key.'],
                400
            );
        }
    }

    /**
     * Verify RSA-SHA256 signature using available public keys.
     */
    private function verifyWithKeys(
        string $message,
        string $signature,
        array $publicKeys
    ): bool {
        if (empty($publicKeys)) {
            return false;
        }

        $activeKeys = collect($publicKeys)
            ->where('Status', 'Active')
            ->pluck('Value')
            ->values()
            ->toArray();

        foreach ($activeKeys as $publicKey) {

            try {

                /*
                 * Dragonpay returns the public key as Base64.
                 */
                $publicKeyBytes = base64_decode(
                    $publicKey,
                    true
                );

                if ($publicKeyBytes === false) {
                    continue;
                }

                $key = openssl_pkey_get_public(
                    $publicKeyBytes
                );

                if ($key === false) {
                    continue;
                }

                $result = openssl_verify(
                    $message,
                    $signature,
                    $key,
                    OPENSSL_ALGO_SHA256
                );

                if (is_resource($key)) {
                    openssl_free_key($key);
                }

                if ($result === 1) {
                    return true;
                }

            } catch (\Throwable $e) {

                Log::warning(
                    'Dragonpay public key verification failed.',
                    [
                        'error' => $e->getMessage(),
                    ]
                );
            }
        }

        return false;
    }

    private function getLocalKeys(): array
    {
        if (!file_exists($this->keysFile)) {
            return [];
        }

        try {

            $json = file_get_contents($this->keysFile);

            $keys = json_decode(
                $json,
                true
            );

            if (!is_array($keys)) {
                return [];
            }

            return collect($keys)->toArray();

        } catch (\Throwable $e) {

            Log::error(
                'Unable to read Dragonpay key cache.',
                [
                    'error' => $e->getMessage(),
                ]
            );

            return [];
        }
    }

    private function updateCollectionKeys(array $keys): void
    {
        try {
            $incomingByValue = [];

            foreach ($keys as $key) {
                $incomingByValue[$key['Value']] = $key['Status'];
            }

            $updatedByValue = [];

            foreach ($collectedKeys as $key) {
                $value = $key['Value'];

                $key['Status'] = isset($incomingByValue[$value])
                    ? $incomingByValue[$value]
                    : 'Inactive';

                $updatedByValue[$value] = $key;
            }

            foreach ($keys as $key) {
                $value = $key['Value'];

                if (!isset($updatedByValue[$value])) {
                    $updatedByValue[$value] = [
                        'Value' => $value,
                        'Status' => $key['Status'],
                        'Timestamp' => now()->toDateTimeString(),
                    ];
                }
            }

            $updatedKeys = array_values($updatedByValue);
        } catch (\Throwable $e) {
            Log::error(
                'Unable to update Dragonpay key cache.',
                [
                    'error' => $e->getMessage(),
                ]
            );
        }
    }

    /**
     * Get active public keys from local cache.
     */
    private function getLocalActiveKeys(): array
    {
        if (!file_exists($this->keysFile)) {
            return [];
        }

        try {

            $json = file_get_contents($this->keysFile);

            $keys = json_decode(
                $json,
                true
            );

            if (!is_array($keys)) {
                return [];
            }

            return collect($keys)
                ->where('Status', 'Active')
                ->pluck('Value')
                ->values()
                ->toArray();

        } catch (\Throwable $e) {

            Log::error(
                'Unable to read Dragonpay key cache.',
                [
                    'error' => $e->getMessage(),
                ]
            );

            return [];
        }
    }

    /**
     * Fetch latest public keys from Dragonpay.
     */
    private function fetchPublicKeys(): array
    {
        $merchantId = env('DRAGONPAY_MERCHANT');

        $apiKey = env('TEST_DRAGONPAY_KEY');

        $isTestMode = env('DRAGONPAY_TEST_MODE', true);

        $endpoint = $isTestMode
            ? $this->testKeysEndpoint
            : $this->productionKeysEndpoint;

        // $response = Http::withBasicAuth(
        //     $merchantId,
        //     $apiKey
        // )
        // ->timeout(15)
        // ->get($endpoint);

        // $response->throw();

        // $keys = $response->json();

        $client = new \GuzzleHttp\Client([
            'timeout' => 15,
        ]);

        $response = $client->get($endpoint, [
            'auth' => [
                $merchantId,
                $apiKey,
            ],
        ]);

        $keys = json_decode(
            $response->getBody()->getContents(),
            true
        );

        if (!is_array($keys)) {
            return [];
        }

        return collect($keys);
    }

    /**
     * Save latest Dragonpay public keys locally.
     */
    private function saveKeys(array $keys): void
    {
        $data = collect($keys)
            ->map(function ($key) {
                return [
                    'Value' => $key,
                    'Status' => 'Active',
                    'Timestamp' => now()->toDateTimeString(),
                ];
            })
            ->values()
            ->toArray();

        file_put_contents(
            $this->keysFile,
            json_encode(
                $data,
                JSON_PRETTY_PRINT
            ),
            LOCK_EX
        );
    }

    public function getKeysFilePath(): array
    {
        $latestKeys = $this->fetchPublicKeys();

        $this->updateCollectionKeys($latestKeys);

    }
}

/*
Dependencies
- GuzzleHttp/guzzle
- storage/keys/dragonpay.json // []
*/