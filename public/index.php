<?php
require_once('../vendor/autoload.php');

function printJson(array $response)
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
}

function getTracking($trackingCode)
{
    $result = ['history' => null, 'error' => null];
    try {
        $client = new \Qandacc\AmazonTracking\Client();
        $result['history'] = $client->getTrackingHistory($trackingCode);
    } catch (\Exception $e) {
        $result['error'] = $e->getMessage();
    }
    return $result;
}

$response = getTracking(filter_input(\INPUT_GET, 'trackingCode'));
printJson($response);
