#include <WiFi.h>
#include <HTTPClient.h>
#include "HX711.h"

// Wi-Fi credentials
const char* ssid = "Ae Vedya";
const char* password = "12345678";

// Backend server URL
const char* serverName = "http://192.168.236.163/inv1/cart.php"; 

// Load cell setup
#define DOUT 14
#define SCK 27
HX711 scale;

float previousWeight = 0.0;
float threshold = 5.0; // Minimum detectable weight change in grams

void setup() {
  Serial.begin(115200);

  // Connect to Wi-Fi
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());

  // Initialize load cell
  scale.begin(DOUT, SCK);
  delay(3000); // Allow load cell to stabilize
  scale.set_scale(2280.f); // Set calibration factor
  scale.tare(); // Zero the load cell
}

void loop() {
  if (scale.is_ready()) {
    float currentWeight = scale.get_units(10); // Take the average of 10 readings
    float weightDifference = currentWeight - previousWeight;

    if (abs(weightDifference) > threshold) { // If the weight difference exceeds the threshold
      String action = (weightDifference < 0) ? "pickup" : "drop";
      String product = getProductFromWeight(currentWeight);  // Get the product name based on the weight
      sendToServer(currentWeight, action, product); 
      previousWeight = currentWeight; 
    }

    Serial.print("Current Weight: ");
    Serial.print(currentWeight);
    Serial.println(" g"); 
  } else {
    Serial.println("Load cell not ready");
  }

  delay(2000); 
}

// Function to map the weight to the corresponding product
String getProductFromWeight(float weight) {
  if (weight >= 38 && weight <= 55) {
    return "Water Bottle";
  } 
  else if (weight >= 70 && weight <= 90) {  
    return "Toy Car";
  } 
  else {
    return "Unknown Product";
  }
}

void sendToServer(float weight, String action, String product) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverName);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    String postData = "weight=" + String(weight) + "&action=" + action + "&product=" + product;
    int httpResponseCode = http.POST(postData);

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println("Server Response: " + response);
    } else {
      Serial.println("Error sending data to server.");
    }
    http.end();
  } else {
    Serial.println("WiFi not connected.");
  }
}
