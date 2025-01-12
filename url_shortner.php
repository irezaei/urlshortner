<?php
// Database connection settings (Default values)
$dbHost = 'localhost'; 
$dbUser = 'root';  
$dbPass = ''; 
$dbName = 'url_shortener';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connect to the database
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate a random short code
function generateShortCode($length = 4) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}

// Handle form submission
$shortUrl = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = trim($_POST['password']);
    $originalUrl = trim($_POST['url']);
    $customCode = trim($_POST['custom_code'] ?? '');

    // Check password
    $correctPassword = 'ramz';
    if ($password !== $correctPassword) {
        $error = 'Invalid password. Access denied.';
    } elseif (!empty($originalUrl)) {
        $shortCode = !empty($customCode) ? $customCode : generateShortCode();

        // Check if custom code already exists
        $checkStmt = $conn->prepare("SELECT id FROM links WHERE short_code = ?");
        $checkStmt->bind_param('s', $shortCode);
        $checkStmt->execute();
        if ($checkStmt->fetch()) {
            if (!empty($customCode)) {
                $error = 'Custom code already exists. Please choose another.';
            } else {
                // If auto-generated code exists, try again
                do {
                    $shortCode = generateShortCode();
                    $checkStmt->bind_param('s', $shortCode);
                    $checkStmt->execute();
                } while ($checkStmt->fetch());
            }
        }
        $checkStmt->close();

        if (empty($error)) {
            // Insert into database
            $stmt = $conn->prepare("INSERT INTO links (original_url, short_code) VALUES (?, ?)");
            $stmt->bind_param('ss', $originalUrl, $shortCode);
            if ($stmt->execute()) {
                $shortUrl = "https://url.com/surl/?c=" . $shortCode;
            } else {
                $error = 'Error: Could not shorten the URL. Please try again.';
            }
            $stmt->close();
        }
    } else {
        $error = 'Please enter a valid URL.';
    }
}

// Handle redirect for short URLs
if (isset($_GET['c'])) {
    $code = $_GET['c'];
    $stmt = $conn->prepare("SELECT original_url FROM links WHERE short_code = ?");
    $stmt->bind_param('s', $code);
    $stmt->execute();
    $stmt->bind_result($originalUrl);
    if ($stmt->fetch()) {
        $stmt->close();
        // Update redirect count
        $conn->query("UPDATE links SET redirect_count = redirect_count + 1 WHERE short_code = '$code'");
        header("Location: $originalUrl");
        exit;
    } else {
        echo "Short URL not found.";
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>URL Shortener</title>
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <!-- QRCode.js -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
            margin: 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card-content {
            padding: 24px;
        }

        .card-title {
            font-size: 24px;
            font-weight: 500;
            text-align: center;
            margin-bottom: 24px;
            color: #ffffff;
        }

        .input-field label {
            color: #ffffff;
        }

        .input-field input {
            color: #ffffff;
            border-bottom: 1px solid #ffffff;
        }

        .input-field input:focus + label {
            color: #64b5f6 !important;
        }

        .input-field input:focus {
            border-bottom: 1px solid #64b5f6 !important;
            box-shadow: 0 1px 0 0 #64b5f6 !important;
        }

        .btn {
            width: 100%;
            margin-top: 20px;
            background-color: #64b5f6;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #42a5f5;
        }

        .result-card {
            margin-top: 20px;
            padding: 16px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .url-display {
            flex-grow: 1;
            word-break: break-all;
        }

        .url-display a {
            color: #64b5f6;
            text-decoration: none;
        }

        .copy-btn {
            background-color: #64b5f6;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .error-message {
            margin-top: 16px;
            padding: 12px;
            background-color: rgba(244, 67, 54, 0.1);
            color: #f44336;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .qr-section {
            margin-top: 20px;
            text-align: center;
        }

        .qr-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 16px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 16px;
        }

        .download-btn {
            background-color: #ff4081;
        }

        .success-message {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4CAF50;
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 8px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .success-message.show {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-content">
            <h4 class="card-title">URL Shortener</h4>
            <form method="POST" id="shortener-form">
                <div class="input-field">
                    <input type="password" id="password" name="password" required>
                    <label for="password">Password</label>
                </div>
                <div class="input-field">
                    <input type="url" id="url" name="url" required>
                    <label for="url">Enter URL to shorten</label>
                </div>
                <div class="input-field" id="custom-code-group" style="display: none;">
                    <input type="text" id="custom_code" name="custom_code" pattern="[a-zA-Z0-9-_]+">
                    <label for="custom_code">Custom Code (optional)</label>
                </div>
                <button type="submit" class="btn waves-effect waves-light">
                    <i class="material-icons left">link</i>
                    Shorten URL
                </button>
            </form>

            <?php if (!empty($shortUrl)): ?>
                <div class="result-card">
                    <div class="url-display">
                        <a href="<?php echo htmlspecialchars($shortUrl); ?>" target="_blank" id="short-url-link">
                            <?php echo htmlspecialchars($shortUrl); ?>
                        </a>
                    </div>
                    <button class="btn-flat copy-btn" id="copy-button">
                        <i class="material-icons">content_copy</i>
                    </button>
                </div>
                
                <div class="qr-section">
                    <div class="qr-container">
                        <div id="qrcode"></div>
                    </div>
                    <button class="btn download-btn waves-effect waves-light" id="download-qr">
                        <i class="material-icons left">download</i>
                        Download QR Code
                    </button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="material-icons">error</i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="success-message" id="success-message">
        <i class="material-icons">check_circle</i>
        URL copied to clipboard
    </div>

    <!-- Materialize JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password persistence
            const passwordInput = document.getElementById('password');
            const savedPassword = localStorage.getItem('savedPassword');
            if (savedPassword) {
                passwordInput.value = savedPassword;
            }

            const form = document.querySelector('#shortener-form');
            form.addEventListener('submit', () => {
                localStorage.setItem('savedPassword', passwordInput.value);
            });

            // Copy functionality
            const copyButton = document.getElementById('copy-button');
            const shortUrlLink = document.getElementById('short-url-link');
            const successMessage = document.getElementById('success-message');

            if (copyButton && shortUrlLink) {
                copyButton.addEventListener('click', async () => {
                    try {
                        await navigator.clipboard.writeText(shortUrlLink.innerText.trim());
                        
                        copyButton.innerHTML = `
                            <i class="material-icons">check</i>
                        `;
                        copyButton.classList.add('green');
                        
                        successMessage.classList.add('show');
                        
                        setTimeout(() => {
                            copyButton.innerHTML = `
                                <i class="material-icons">content_copy</i>
                            `;
                            copyButton.classList.remove('green');
                            successMessage.classList.remove('show');
                        }, 2000);
                    } catch (err) {
                        console.error('Failed to copy text: ', err);
                    }
                });
            }

            // Generate QR Code
            const qrContainer = document.getElementById('qrcode');
            if (qrContainer && shortUrlLink) {
                const qrCode = new QRCode(qrContainer, {
                    text: shortUrlLink.innerText.trim(),
                    width: 200,
                    height: 200
                });

                // Download QR Code
                const downloadQrBtn = document.getElementById('download-qr');
                if (downloadQrBtn) {
                    downloadQrBtn.addEventListener('click', () => {
                        const canvas = qrContainer.querySelector('canvas');
                        const url = canvas.toDataURL('image/png');
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'qr-code.png';
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                    });
                }
            }
        });
    </script>
</body>
</html>