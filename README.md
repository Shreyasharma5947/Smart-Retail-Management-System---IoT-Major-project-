# Smart-Retail-Management-System---IoT-Major-project-

# **Smart Retail Management System**  
🚀 **RFID & Load Cell-Based Automated Inventory & Billing** 🚀  

This project is an **RFID and Load Cell Integrated Smart Retail System** that automates inventory tracking, cart management, and billing processes. It detects products using **RFID-based product recognition** and updates the cart in **real-time** using **load cell weight tracking**.  

📌 **Key Features**:  
✅ **RFID-Based Product Recognition** – Identifies products via predefined RFID UIDs.  
✅ **Load Cell Integration** – Tracks product pickup & drop actions dynamically.  
✅ **Automated Cart Updates** – Adds or removes products based on real-time weight changes.  
✅ **Accurate Billing** – Ensures error-free pricing and quantity calculations.  
✅ **Cloud Storage Option** – Supports MySQL or Firebase for real-time data storage.  

---

## **🛠️ Tech Stack & Components**  

### **🔹 Backend:**  
- **PHP** – Handles product recognition, cart updates, and billing logic.  
- **MySQL / Firebase** – Stores user, product, and transaction data.  
- **ESP32 (Wi-Fi + HTTP Client)** – Sends real-time weight & RFID data to the server.  

### **🔹 Frontend:**  
- **HTML, CSS, JavaScript (AJAX & Fetch API)** – Ensures smooth user interaction.  

### **🔹 Hardware Used:**  
- **ESP32 Microcontroller** – Connects RFID Reader & Load Cell to the backend.  
- **RFID Reader (MFRC522)** – Recognizes tagged products.  
- **Load Cell (HX711 Module)** – Measures weight variations for cart updates.  

---

## **📌 How It Works**  

### **1️⃣ User Authentication (RFID Login)**  
- The user taps an **RFID card** to log in.  
- If the card is **registered**, they are redirected to the **cart page**.  
- If the card is **new**, the user is prompted to **register** before proceeding.  

### **2️⃣ Product Recognition (RFID)**  
- When an RFID-tagged product is scanned, its details (e.g., "Water Bottle") are **fetched from the database** and displayed on the UI.  
- If the product is **not recognized**, it is marked as "Unknown Product" until added manually.  

### **3️⃣ Smart Cart System (Load Cell)**  
- **When an item is picked up**, it gets **added to the cart** dynamically.  
- **When an item is placed back**, it gets **removed from the cart** automatically.  

### **4️⃣ Automated Billing**  
- The **price is predefined** in the system:  
  - **Water Bottle** → ₹200  
  - **Lunch Box** → ₹300  
- The total amount is **calculated automatically**, ensuring a **hassle-free checkout experience**.  

---

## **🚀 Installation & Setup**  

### **1️⃣ Clone the Repository**  
```bash
git clone https://github.com/your-username/smart-retail-management.git
cd smart-retail-management
```

### **2️⃣ Setup the Backend (PHP & MySQL / Firebase)**  
#### **🔹 Using MySQL (Local Database)**
1. Install **XAMPP** or **WAMP** for local hosting.  
2. Move project files to `htdocs` (XAMPP) or `www` (WAMP).  
3. Create a **database** `rfid_product_db` and **import** `database.sql`.  

#### **🔹 Using Firebase (Cloud Database)**
1. Create a **Firebase Realtime Database**.  
2. Update `config.js` with Firebase API keys.  
3. Modify **PHP scripts** to interact with Firebase instead of MySQL.  

---

## **📌 Project Architecture**  

1️⃣ **User taps RFID card** – Authenticates & loads product info.  
2️⃣ **RFID verifies the product** – Predefined UIDs match product names.  
3️⃣ **Load cell detects weight change** – Updates cart when items are picked or dropped.  
4️⃣ **Billing system calculates total** – Prices are predefined, and bill generation is automated.  
5️⃣ **Checkout completes the transaction** – Data is stored in **MySQL or Firebase**.  

---


## **🔗 Future Enhancements**  
✔️ **Mobile App Integration** – Android/iOS app for better shopping experience.  
✔️ **AI-Based Demand Prediction** – Predict stock requirements using machine learning.  
✔️ **Voice-Enabled Checkout** – Integrate voice-based shopping for seamless checkout.  

---

## **💡 Contributors**  
👨‍💻 **Your Name** – Shreya sharma
👨‍💻 **Team Members** - Rithik Kumar MB, Srishti Saundarya

---

## **📜 License**  
This project is licensed under the **MIT License** – feel free to use and modify it.  

---
