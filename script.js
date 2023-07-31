document.addEventListener("DOMContentLoaded", function () {
  const calculateButton = document.getElementById("calculateButton");
  calculateButton.addEventListener("click", calculateDeliveryCharges);
});

function restrictToSixDigits(input) {
  const maxLength = 6;
  const inputValue = input.value;
  if (inputValue.length > maxLength) {
    input.value = inputValue.slice(0, maxLength);
  }
}

function calculateDeliveryCharges() {
  const originPincode = document.getElementById("originPincode").value;
  const destinationPincode = document.getElementById("destinationPincode").value;
  const deliverySpeed = document.getElementById("deliverySpeed").value;
  const packageWeight = parseFloat(document.getElementById("packageWeight").value);
  const paymentMode = document.getElementById("paymentMode").value;

  // Validate pincode length
  if (originPincode.length !== 6 || destinationPincode.length !== 6) {
    alert("Pincode should be exactly 6 digits.");
    return;
  }

  // Prepare the data to be sent in the AJAX request
  const data = {
    originPincode: originPincode,
    destinationPincode: destinationPincode,
    deliverySpeed: deliverySpeed,
    packageWeight: packageWeight,
    paymentMode: paymentMode
  };

  // Send AJAX POST request to the PHP backend
  fetch("calculate_delivery_charges.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(data)
  })
  .then(response => response.json())
  .then(result => {
    // Display the result
    const resultElement = document.getElementById("result");
    if (result.error) {
      resultElement.textContent = `Error: ${result.error}`;
    } else {
      console.log(result);
      const deliveryCharge = parseFloat(result.deliveryCharge);
      resultElement.textContent = `Delivery Charges: ₹${deliveryCharge.toFixed(2)}`;
    }
  })
  .catch(error => {
    console.error("Error fetching data:", error);
    // Display error message if there's an issue with the backend
    const resultElement = document.getElementById("result");
    resultElement.textContent = "Error: Failed to fetch data from the server.";
  });
}
