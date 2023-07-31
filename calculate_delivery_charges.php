<?php
// Retrieve user inputs from the HTTP POST request
$originPincode = $_POST['originPincode'];
$destinationPincode = $_POST['destinationPincode'];
$deliverySpeed = $_POST['deliverySpeed'];
$packageWeight = $_POST['packageWeight'];
$paymentMode = $_POST['paymentMode'];

// Perform any additional server-side validation if needed
// ...

// Load the data from the JSON file
$data = json_decode(file_get_contents('data.json'), true);

// Find the selected delivery speed and payment mode in the data
$deliverySpeedData = null;
foreach ($data['deliveryOptions'] as $option) {
    if ($option['type'] === $deliverySpeed) {
        $deliverySpeedData = $option;
        break;
    }
}

$paymentModeData = null;
foreach ($data['paymentModes'] as $option) {
    if ($option['type'] === $paymentMode) {
        $paymentModeData = $option;
        break;
    }
}

// Prepare the response data
$response = array();

if (!$deliverySpeedData || !$paymentModeData) {
    // Return an error response if the delivery speed or payment mode is not found
    http_response_code(400); // Bad Request
    $response['error'] = 'Invalid delivery speed or payment mode.';
} else {
    // Calculate the delivery charge based on weight
    $deliveryCharge = 0;

    // Define the weight tiers and their respective charges
    $weightTiers = array(
        array('weight' => 1, 'charge' => 35),
        array('weight' => 3, 'charge' => 85),
        // Add more tiers as needed
    );

    foreach ($weightTiers as $tier) {
        if ($packageWeight <= $tier['weight']) {
            $deliveryCharge = $tier['charge'];
            break;
        }
    }

    // Apply additional charge based on delivery speed
    $deliveryCharge += $deliverySpeedData['additional_charge'];

    // Apply discount if the payment mode is "prepaid"
    if ($paymentMode === 'prepaid') {
        $discountPercentage = $paymentModeData['discount_percentage'];
        $deliveryCharge *= (1 - ($discountPercentage / 100));
    }

    // Add the calculated delivery charge to the response
    $response['deliveryCharge'] = round($deliveryCharge, 2);
}

// Send the JSON response back to the client
header('Content-Type: application/json');
echo json_encode($response);
?>
