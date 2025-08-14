<?php
// Configuration file for XXE CTF Challenge

// Domain and server settings
define('DOMAIN', 'abphz.tech');
define('SERVER_IP', '91.228.186.44');

// File upload settings
define('UPLOAD_DIR', '../uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB

// Flag for the CTF challenge
define('FLAG', 'flag{xxe_in_xlsx_files_is_dangerous_2024}');

// Create flag file for XXE extraction
if (!file_exists('/tmp/flag.txt')) {
    @file_put_contents('/tmp/flag.txt', FLAG);
}
if (!file_exists('../flag.txt')) {
    @file_put_contents('../flag.txt', FLAG);
}

// Ensure upload directory exists
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}
?>
