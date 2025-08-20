# url-dcrypt
PHP function for dynamic URL encryption and decryption using AES, SHA3, and Base64.
This library helps protect your web application from SQL Injection attacks by obfuscating URL parameters in a secure, irreversible way.

<br>
<br>

## 📁 Features

🔒 AES-256-CBC encryption

🛡️ HMAC-based integrity verification (SHA3-256)

🌐 URL-safe Base64 encoding

⚡ Lightweight functional approach (no classes or dependencies)

✅ Easy to integrate into existing PHP projects

<br>
<br>

## 📦 Installation
Install via Composer:

```
composer require alvar27/url-dcrypt
```
<br>
<br>
<br>

## 🔑 Configuration
Make sure to define a constant SECRET_KEY before using the functions:

```
define('SECRET_KEY', 'your-32-byte-secret-key-goes-here');
```
<br>
<br>
The secret key must be exactly 32 bytes for AES-256-CBC. You can use a key generator or hash a passphrase with SHA-256 to ensure proper length.

<br>
<br>

## 💡 Usage
Using this library is as simple as calling the provided functions for encryption and decryption.

encryption:
```
encrypt_url("your_plaintext")
```
decryption:
```
decrypt_url("your_ciphertext")
```
<br>

##### Making the URL More Dynamic
To ensure that the encrypted URL is refreshed dynamically (for example, when the user clicks the refresh icon in the browser), include the following function at the end of your HTML page, just like adding JavaScript:
<br>
```
<?php update_url() ?>
```
<br>

##### Ciphertext Validation (Optional)
To prevent errors or decryption failures, you can first verify whether a given value is indeed ciphertext by using: 
<br>
```
is_ciphertext()
```
<br>
Example :
<br>

```
if (is_ciphertext($data)) {
    $plaintext = decrypt_url($data);
}
```

<br>
<br>

## 👨‍💻 Author
Created by [@alvar27](https://github.com/alvar27)
<br>
Feel free to fork, improve, or contribute. Happy coding!
