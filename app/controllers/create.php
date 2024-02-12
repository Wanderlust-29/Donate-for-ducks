<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;

// Chargement des variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../config/');
$dotenv->load();

// Utilisation des variables d'environnement
$stripeSecretKey = $_ENV['API_KEY'];
$stripe = new StripeClient($stripeSecretKey);


function calculateOrderAmount(int $amount): int {
    return $amount * 100;
}

try {
    // retrieve JSON from POST body
    $jsonStr = file_get_contents('php://input');
    $jsonObj = json_decode($jsonStr);
    
    $amount = isset($jsonObj->amount) ? (int)$jsonObj->amount : 0;


    $paymentIntent = $stripe->paymentIntents->create([
        'amount' => calculateOrderAmount($amount),
        'currency' => 'eur',
    ]);
    $output = [
        'clientSecret' => $paymentIntent->client_secret,
    ];

    echo json_encode($output);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

