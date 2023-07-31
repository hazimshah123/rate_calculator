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

  const data = {
    originPincode: originPincode,
    destinationPincode: destinationPincode,
    deliverySpeed: deliverySpeed,
    packageWeight: packageWeight,
    paymentMode: paymentMode
  };

  console.log(data);
//   // Convert the data to JSON
//   const jsonData = JSON.stringify(data);

//   // Create a new XMLHttpRequest object
//   const xhr = new XMLHttpRequest();

//   // Configure the POST request
//   xhr.open("POST", "calculate_delivery_charges.php", true);
//   xhr.setRequestHeader("Content-Type", "application/json");

//   // Define the function to handle the response
//   xhr.onreadystatechange = function () {
//     if (xhr.readyState === XMLHttpRequest.DONE) {
//       console.log(xhr.responseText);
//       if (xhr.status === 200) {
//         // Response received successfully
//         const result = JSON.parse(xhr.responseText);
//         handleResponse(result);
//       } else {
//         // Error handling for failed requests
//         const resultElement = document.getElementById("result");
//         resultElement.textContent = "Error: Failed to fetch data from the server.";
//       }
//     }
//   };

//   // Send the POST request with JSON data
//   xhr.send(jsonData);
// }

// function handleResponse(result) {
//   const resultElement = document.getElementById("result");
//   if (result.error) {
//     resultElement.textContent = `Error: ${result.error}`;
//   } else {
//     const deliveryCharge = parseFloat(result.deliveryCharge);
//     resultElement.textContent = `Delivery Charges: ₹${deliveryCharge.toFixed(2)}`;
//   }
// }


  // Send AJAX POST request to the backend API using the fetch API
  fetch("https://your-backend-api-url.com/calculate_delivery_charges", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(data)
  })
    .then(response => {
      // Check if the response was successful (status 200-299)
      if (!response.ok) {
        throw new Error("Network response was not ok.");
      }
      // Parse the response JSON data
      return response.json();
    })
    .then(result => {
      // Handle the parsed JSON response
      const resultElement = document.getElementById("result");
      if (result.error) {
        resultElement.textContent = `Error: ${result.error}`;
      } else {
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