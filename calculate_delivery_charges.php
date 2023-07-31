<?php

// // Retrieve user inputs from the HTTP POST request
// $originPincode = $_POST['originPincode'];
// $destinationPincode = $_POST['destinationPincode'];
// $deliverySpeed = $_POST['deliverySpeed'];
// $packageWeight = floatval($_POST['packageWeight']);
// $paymentMode = $_POST['paymentMode'];



// // Perform any additional server-side validation if needed
// // ...

// // Load the data from the JSON file
// $data = json_decode(file_get_contents('data.json'), true);

// // Find the selected delivery speed and payment mode in the data
// $deliverySpeedData = null;
// foreach ($data['deliveryOptions'] as $option) {
//     if ($option['type'] === $deliverySpeed) {
//         $deliverySpeedData = $option;
//         break;
//     }
// }

// $paymentModeData = null;
// foreach ($data['paymentModes'] as $option) {
//     if ($option['type'] === $paymentMode) {
//         $paymentModeData = $option;
//         break;
//     }
// }

// // Prepare the response data
// $response = array();

// if (!$deliverySpeedData || !$paymentModeData) {
//     // Return an error response if the delivery speed or payment mode is not found
//     http_response_code(200); // Bad Request
//     $response['deliveryCharge'] = 0;
//     $response['error'] = 'Invalid delivery speed or payment mode.';
// } else {
//     // Calculate the delivery charge based on weight
//     $deliveryCharge = 0;

//     // Define the weight tiers and their respective charges
//     $weightTiers = array(
//         array('weight' => 1, 'charge' => 35),
//         array('weight' => 3, 'charge' => 85),
//         // Add more tiers as needed
//     );

//     foreach ($weightTiers as $tier) {
//         if ($packageWeight <= $tier['weight']) {
//             $deliveryCharge = $tier['charge'];
//             break;
//         }
//     }

//     // Apply additional charge based on delivery speed
//     $deliveryCharge += $deliverySpeedData['additional_charge'];

//     // Apply discount if the payment mode is "prepaid"
//     if ($paymentMode === 'prepaid') {
//         $discountPercentage = $paymentModeData['discount_percentage'];
//         $deliveryCharge *= (1 - ($discountPercentage / 100));
//     }

//     // Add the calculated delivery charge to the response
//     $response['deliveryCharge'] = round($deliveryCharge, 2);
// }

// // Send the JSON response back to the client
// header('Content-Type: application/json');
// echo json_encode($response);

header('Content-Type: application/json');

// Validate the form submission
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array('error' => 'Method not allowed.'));
    exit;
}

// Retrieve the form data
$originPincode = $_POST['originPincode'];
$destinationPincode = $_POST['destinationPincode'];
$deliverySpeed = $_POST['deliverySpeed'];
$packageWeight = floatval($_POST['packageWeight']);
$paymentMode = $_POST['paymentMode'];

// Validate the required fields
if (
    empty($originPincode) ||
    empty($destinationPincode) ||
    empty($deliverySpeed) ||
    !isset($packageWeight) ||
    empty($paymentMode)
) {
    http_response_code(400); // Bad Request
    echo json_encode(array('error' => 'Missing or invalid input data'));
    exit;
}

// Calculate the delivery charges based on the input data
$deliveryCharge = calculateDeliveryCharge($originPincode, $destinationPincode, $deliverySpeed, $packageWeight, $paymentMode);

// Prepare the response
$response = array(
    'deliveryCharge' => $deliveryCharge
);

// Convert the response data to JSON format and send the response
echo json_encode($response);

// Function to calculate the delivery charge based on input data
// Function to calculate the delivery charge based on input data
function calculateDeliveryCharge($originPincode, $destinationPincode, $deliverySpeed, $packageWeight, $paymentMode)
{
    // Define the delivery charge rates based on weight
    $weightChargeRates = array(
        1000 => 35.0,  // ₹35 for up to 1 kg
        3000 => 85.0   // ₹85 for up to 3 kg
        // Add more weight ranges and charges as needed
    );

    // Define additional charges for delivery speed and payment mode
    $speedCharge = ($deliverySpeed === 'fastest') ? 50.0 : 0.0;  // ₹50 extra for fastest delivery
    $paymentModeCharge = ($paymentMode === 'cashOnDelivery') ? 30.0 : 0.0;  // ₹30 extra for cash on delivery

    // Calculate the delivery charge based on package weight
    $deliveryCharge = 0.0;
    foreach ($weightChargeRates as $weightLimit => $charge) {
        if ($packageWeight <= $weightLimit) {
            $deliveryCharge = $charge;
            break;
        }
    }

    // Apply additional charges for speed and payment mode
    $deliveryCharge += $speedCharge + $paymentModeCharge;

    return $deliveryCharge;
}

?>
