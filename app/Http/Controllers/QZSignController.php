<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * QZ Tray Message Signing Controller
 * 
 * This controller handles signing requests from QZ Tray for silent printing.
 * The signature allows QZ Tray to trust print requests without user confirmation.
 */
class QZSignController extends Controller
{
    /**
     * Path to the private key file
     * Store this file in a secure location outside the public directory
     */
    private $privateKeyPath;

    public function __construct()
    {
        // Private key should be stored securely outside public folder
        $this->privateKeyPath = storage_path('app/qz-tray/private-key.pem');
    }

    /**
     * Sign a message for QZ Tray
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function sign(Request $request)
    {
        $message = $request->input('request') ?? $request->query('request');

        if (empty($message)) {
            return response('No message to sign', 400);
        }

        // Check if private key exists
        if (!file_exists($this->privateKeyPath)) {
            Log::warning('QZ Tray private key not found at: ' . $this->privateKeyPath);
            return response('Private key not configured', 500);
        }

        try {
            $privateKeyContent = file_get_contents($this->privateKeyPath);
            $privateKey = openssl_get_privatekey($privateKeyContent);

            if (!$privateKey) {
                Log::error('Failed to load QZ Tray private key');
                return response('Invalid private key', 500);
            }

            $signature = null;
            // Use SHA512 for QZ Tray 2.1+ (use SHA1 for older versions)
            $success = openssl_sign($message, $signature, $privateKey, OPENSSL_ALGO_SHA512);

            if ($success && $signature) {
                return response(base64_encode($signature))
                    ->header('Content-Type', 'text/plain');
            }

            Log::error('Failed to sign message for QZ Tray');
            return response('Error signing message', 500);

        } catch (\Exception $e) {
            Log::error('QZ Tray signing error: ' . $e->getMessage());
            return response('Signing error: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Serve the digital certificate
     * 
     * @return \Illuminate\Http\Response
     */
    public function certificate()
    {
        $certPath = public_path('assets/qz-tray/digital-certificate.txt');

        if (!file_exists($certPath)) {
            Log::warning('QZ Tray certificate not found at: ' . $certPath);
            return response('Certificate not configured', 404);
        }

        return response(file_get_contents($certPath))
            ->header('Content-Type', 'text/plain')
            ->header('Cache-Control', 'no-store');
    }
}
