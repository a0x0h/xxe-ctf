<?php
// Functions for processing XLSX files - VULNERABLE TO XXE

/**
 * Process XLSX file and extract content
 * WARNING: This function is intentionally vulnerable to XXE attacks
 * for CTF purposes. DO NOT use in production!
 */
function processXLSXFile($filePath) {
    try {
        // Create a temporary directory to extract XLSX contents
        $tempDir = sys_get_temp_dir() . '/xlsx_' . uniqid();
        if (!mkdir($tempDir, 0777, true)) {
            return false;
        }

        // Extract XLSX file (it's just a ZIP archive)
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== TRUE) {
            return false;
        }
        
        $zip->extractTo($tempDir);
        $zip->close();

        $extractedData = '';

        // Process workbook.xml (VULNERABLE TO XXE)
        $workbookPath = $tempDir . '/xl/workbook.xml';
        if (file_exists($workbookPath)) {
            $extractedData .= "=== WORKBOOK XML CONTENT ===\n";
            $extractedData .= processXMLFile($workbookPath) . "\n\n";
        }

        // Process sharedStrings.xml (VULNERABLE TO XXE)
        $sharedStringsPath = $tempDir . '/xl/sharedStrings.xml';
        if (file_exists($sharedStringsPath)) {
            $extractedData .= "=== SHARED STRINGS XML CONTENT ===\n";
            $extractedData .= processXMLFile($sharedStringsPath) . "\n\n";
        }

        // Process worksheet files (VULNERABLE TO XXE)
        $worksheetDir = $tempDir . '/xl/worksheets/';
        if (is_dir($worksheetDir)) {
            $files = scandir($worksheetDir);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'xml') {
                    $extractedData .= "=== WORKSHEET: $file ===\n";
                    $extractedData .= processXMLFile($worksheetDir . $file) . "\n\n";
                }
            }
        }

        // Clean up temporary directory
        removeDirectory($tempDir);

        return $extractedData ?: 'No data extracted from the file.';
        
    } catch (Exception $e) {
        error_log("XLSX Processing Error: " . $e->getMessage());
        return "Error processing file: " . $e->getMessage();
    }
}

/**
 * Process XML file - INTENTIONALLY VULNERABLE TO XXE
 * This function deliberately enables external entity processing
 */
function processXMLFile($xmlPath) {
    if (!file_exists($xmlPath)) {
        return "File not found: $xmlPath";
    }

    try {
        // Read the raw XML content first
        $xmlContent = file_get_contents($xmlPath);
        
        // VULNERABLE: Create DOMDocument with external entities enabled
        $dom = new DOMDocument();
        
        // CRITICAL VULNERABILITY: Enable external entity loading
        $dom->resolveExternals = true;
        $dom->substituteEntities = true;
        
        // Disable entity loader warnings but keep functionality
        $previousValue = libxml_disable_entity_loader(false);
        
        // Load XML with external entities enabled (VULNERABLE)
        $loadResult = $dom->loadXML($xmlContent, LIBXML_NOENT | LIBXML_DTDLOAD);
        
        // Restore previous setting
        libxml_disable_entity_loader($previousValue);
        
        if (!$loadResult) {
            return "Failed to parse XML: $xmlPath\nRaw content:\n" . substr($xmlContent, 0, 1000);
        }

        // Extract and return the processed content
        $processedContent = $dom->saveXML();
        
        // Also try to extract text content
        $textContent = extractTextFromXML($dom);
        
        return "Raw XML:\n" . substr($xmlContent, 0, 500) . "\n\nProcessed XML:\n" . substr($processedContent, 0, 500) . "\n\nExtracted Text:\n" . $textContent;
        
    } catch (Exception $e) {
        return "XML Processing Error: " . $e->getMessage() . "\nFile: $xmlPath";
    }
}

/**
 * Extract text content from XML DOM
 */
function extractTextFromXML($dom) {
    $text = '';
    
    // Get all text nodes
    $xpath = new DOMXPath($dom);
    $textNodes = $xpath->query('//text()');
    
    foreach ($textNodes as $node) {
        $nodeText = trim($node->nodeValue);
        if (!empty($nodeText)) {
            $text .= $nodeText . "\n";
        }
    }
    
    return $text ?: 'No text content found.';
}

/**
 * Alternative XML processor using SimpleXML (also vulnerable)
 */
function processXMLWithSimpleXML($xmlPath) {
    if (!file_exists($xmlPath)) {
        return "File not found: $xmlPath";
    }

    try {
        // VULNERABLE: Load XML with external entities
        $xmlContent = file_get_contents($xmlPath);
        
        // Disable entity loader to allow external entities
        libxml_disable_entity_loader(false);
        
        // Load with LIBXML_NOENT to process entities
        $xml = simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_NOENT | LIBXML_DTDLOAD);
        
        if ($xml === false) {
            return "Failed to parse XML with SimpleXML: $xmlPath";
        }
        
        // Convert to string to see processed content
        return $xml->asXML();
        
    } catch (Exception $e) {
        return "SimpleXML Error: " . $e->getMessage();
    }
}

/**
 * Recursively remove directory
 */
function removeDirectory($dir) {
    if (!is_dir($dir)) {
        return;
    }
    
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            removeDirectory($path);
        } else {
            unlink($path);
        }
    }
    rmdir($dir);
}

/**
 * Log processing activity (for debugging)
 */
function logActivity($message) {
    $logFile = '../logs/activity.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message\n";
    
    if (!is_dir(dirname($logFile))) {
        mkdir(dirname($logFile), 0777, true);
    }
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
?>
