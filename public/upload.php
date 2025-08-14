<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!isset($_FILES['xlsx_file']) || $_FILES['xlsx_file']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = 'Please select a valid file to upload.';
    header('Location: index.php');
    exit;
}

$file = $_FILES['xlsx_file'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$fileType = $file['type'];

// File validation
$allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
                 'application/vnd.ms-excel'];
$allowedExtensions = ['xlsx', 'xls'];
$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if (!in_array($fileExtension, $allowedExtensions)) {
    $_SESSION['error'] = 'Only Excel files (.xlsx, .xls) are allowed.';
    header('Location: index.php');
    exit;
}

if ($fileSize > MAX_FILE_SIZE) {
    $_SESSION['error'] = 'File size exceeds the maximum limit of ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB.';
    header('Location: index.php');
    exit;
}

// Generate unique filename
$uploadFileName = uniqid('xlsx_', true) . '.' . $fileExtension;
$uploadPath = UPLOAD_DIR . $uploadFileName;

// Move uploaded file
if (!move_uploaded_file($fileTmpName, $uploadPath)) {
    $_SESSION['error'] = 'Failed to upload file. Please try again.';
    header('Location: index.php');
    exit;
}

// Process the XLSX file (VULNERABLE TO XXE)
$result = processXLSXFile($uploadPath);

// Clean up uploaded file
unlink($uploadPath);

if ($result === false) {
    $_SESSION['error'] = 'Failed to process the Excel file. Please ensure it\'s a valid XLSX file.';
    header('Location: index.php');
    exit;
}

$_SESSION['success'] = 'File processed successfully!';
$_SESSION['file_data'] = $result;
header('Location: results.php');
exit;
?>
