<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['file_data'])) {
    header('Location: index.php');
    exit;
}

$fileData = $_SESSION['file_data'];
unset($_SESSION['file_data']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Results - XXE CTF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .results-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 2rem;
            margin-top: 3rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }
        .output-box {
            background: #000;
            color: #00ff00;
            font-family: 'Courier New', monospace;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1rem 0;
            white-space: pre-wrap;
            word-break: break-all;
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="results-container">
                    <h2 class="text-center mb-4">📊 File Processing Results</h2>
                    
                    <div class="alert alert-info">
                        <strong>File processed successfully!</strong> Here's what we extracted:
                    </div>

                    <h5>Extracted Data:</h5>
                    <div class="output-box"><?php echo htmlspecialchars($fileData); ?></div>

                    <?php if (strpos($fileData, 'flag{') !== false || strpos($fileData, 'FLAG{') !== false): ?>
                        <div class="alert alert-success mt-3">
                            <strong>🎉 Congratulations!</strong> You found something interesting!
                        </div>
                    <?php endif; ?>

                    <div class="text-center mt-4">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload Another File
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
