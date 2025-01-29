#include <WiFi.h>
#include <MFRC522.h>
#include <HTTPClient.h>
#include "HX711.h"

// Wi-Fi credentials
const char* ssid = "Ae Vedya";
const char* password = "12345678";

// RFID Pin Configuration
#define RST_PIN 22
#define SS_PIN 5
MFRC522 rfid(SS_PIN, RST_PIN);

// Load cell setup
#define DOUT 14
#define SCK 27
HX711 scale;

const char* productServer = "http://192.168.236.163/done/product.php";
const char* cartServer = "http://192.168.236.163/done/backend.php";

String productName = "";
float lastWeight = 0;  // Stores the last measured weight
bool isProcessing = false; // Prevents multiple triggers for the same event
float weightThreshold = 5.0; // Threshold for significant weight change

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  Serial.println("Connecting to WiFi...");
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");

  SPI.begin(18, 19, 23);
  rfid.PCD_Init();
  Serial.println("RFID initialized.");

  scale.begin(DOUT, SCK);
  scale.set_scale(2280.f); // Adjust to your load cell calibration
  scale.tare();
  Serial.println("Load cell initialized and tared.");
}

void loop() {
  // Scan for RFID
  if (rfid.PICC_IsNewCardPresent() && rfid.PICC_ReadCardSerial()) {
    String uid = "";
    for (byte i = 0; i < rfid.uid.size; i++) {
      uid += String(rfid.uid.uidByte[i], HEX);
    }
    Serial.println("RFID Scanned: " + uid);
    recognizeProduct(uid);
    rfid.PICC_HaltA();
  }

  // Check if load cell is ready and product is recognized
  if (scale.is_ready()) {
    Serial.println("Load cell is ready.");
    if (!productName.isEmpty()) {
      float currentWeight = scale.get_units(10);
      Serial.println("Current weight detected: " + String(currentWeight) + " grams");

      float weightChange = currentWeight - lastWeight;

      // Ensure significant weight change and process accordingly
      if (abs(weightChange) > weightThreshold) {
        if (!isProcessing) {
          isProcessing = true;
          if (weightChange > 0) {
            Serial.println("Item dropped.");
            sendToCart(productName, "drop");
          } else {
            Serial.println("Item picked up.");
            sendToCart(productName, "pickup");
          }
          delay(1000); // Delay to stabilize readings
        }
      } else {
        isProcessing = false; // Reset for next significant change
        Serial.println("Weight change is negligible, no action taken.");
      }
      lastWeight = currentWeight; // Update the last weight
    } else {
      Serial.println("No product recognized yet. Waiting for RFID scan.");
    }
  }

  delay(500); // General loop delay to stabilize readings
}

void recognizeProduct(String uid) {
  HTTPClient http;
  http.begin(productServer);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  String postData = "uid=" + uid;
  int httpResponseCode = http.POST(postData);

  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.println("Server Response: " + response);

    // Parse JSON response
    int productIndex = response.indexOf("\"product\":\"");
    if (productIndex != -1) {
      productIndex += 11; // Move past the key to value
      int productEnd = response.indexOf("\"", productIndex);
      if (productEnd != -1) {
        productName = response.substring(productIndex, productEnd);
        Serial.println("Product recognized: " + productName);
      }
    }
  } else {
    Serial.println("Failed to communicate with server for product recognition. HTTP Response Code: " + String(httpResponseCode));
  }
  http.end();
}

void sendToCart(String productName, String action) {
  HTTPClient http;
  http.begin(cartServer);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  String postData = "product_name=" + productName + "&action=" + action;
  int httpResponseCode = http.POST(postData);

  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.println("Cart Server Response: " + response);
  } else {
    Serial.println("Failed to communicate with server for cart update. HTTP Response Code: " + String(httpResponseCode));
  }
  http.end();
}
