<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include configuration
require_once '../includes/config.php';
require_once '../includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XXE CTF Challenge - XLSX Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .ctf-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 2rem;
            margin-top: 5rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }
        .upload-area {
            border: 3px dashed #007bff;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            margin: 1.5rem 0;
            background: #f8f9fa;
        }
        .flag-display {
            background: #000;
            color: #00ff00;
            font-family: 'Courier New', monospace;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
        }
        .logo {
            font-size: 2.5rem;
            font-weight: bold;
            color: #007bff;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="ctf-container">
                    <div class="text-center mb-4">
                        <div class="logo">🎯 XXE CTF Challenge</div>
                        <p class="lead text-muted">Upload XLSX files for processing</p>
                        <small class="text-info">Hint: Look for XML entities in your Excel files...</small>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="upload.php" method="POST" enctype="multipart/form-data">
                        <div class="upload-area">
                            <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                            <h5>Choose your XLSX file</h5>
                            <input type="file" class="form-control mt-3" name="xlsx_file" accept=".xlsx,.xls" required>
                            <small class="text-muted mt-2 d-block">
                                Only Excel files (.xlsx, .xls) are accepted<br>
                                Maximum file size: 10MB
                            </small>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-upload"></i> Process File
                            </button>
                        </div>
                    </form>

                    <div class="mt-4">
                        <h6>Challenge Information:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-info-circle text-info"></i> Upload an Excel file to extract data</li>
                            <li><i class="fas fa-shield-alt text-warning"></i> Server processes XML content within XLSX files</li>
                            <li><i class="fas fa-flag text-success"></i> Find the hidden flag in the system</li>
                        </ul>
                    </div>

                    <div class="mt-4 text-center">
                        <small class="text-muted">
                            CTF Target: <?php echo htmlspecialchars(DOMAIN); ?> | IP: <?php echo htmlspecialchars(SERVER_IP); ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
