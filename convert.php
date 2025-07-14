<?php
// convert.php : receives uploaded PDF, triggers Python conversion, and streams back DOCX

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
    // Ensure necessary folders exist
    $uploadDir = __DIR__ . '/uploads/';
    $convertedDir = __DIR__ . '/converted/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    if (!is_dir($convertedDir)) mkdir($convertedDir, 0777, true);

    $fileName = basename($_FILES['pdf_file']['name']);
    $fileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $fileName); // sanitize
    $uploadFile = $uploadDir . $fileName;
    $outputFile = $convertedDir . pathinfo($fileName, PATHINFO_FILENAME) . '.docx';

    if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $uploadFile)) {
        // Call Python conversion
        $cmd = escapeshellcmd('python3 convert.py ' . escapeshellarg($uploadFile) . ' ' . escapeshellarg($outputFile));
        exec($cmd, $outputLines, $returnVar);

        if ($returnVar === 0 && file_exists($outputFile)) {
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="' . basename($outputFile) . '"');
            header('Content-Length: ' . filesize($outputFile));
            readfile($outputFile);
            // Optionally, cleanup files here
            exit;
        } else {
            http_response_code(500);
            echo 'Conversion failed. ' . implode("\n", $outputLines);
        }
    } else {
        http_response_code(400);
        echo 'File upload failed.';
    }
} else {
    http_response_code(405);
    echo 'Method Not Allowed';
}
?>