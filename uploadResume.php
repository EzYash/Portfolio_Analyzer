<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

function respond($ok, $payload = []){
    http_response_code($ok ? 200 : 400);
    echo json_encode(array_merge(["ok" => $ok], $payload));
    exit;
}

if(!isset($_FILES["resume"])){
    respond(false, ["error" => "No file uploaded (resume)"]);
}

$file = $_FILES["resume"];
if($file["error"] !== UPLOAD_ERR_OK){
    respond(false, ["error" => "Upload failed (code ".$file["error"].")"]);
}

$name = $file["name"] ?? "resume.pdf";
$tmp = $file["tmp_name"];
$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
if($ext !== "pdf"){
    respond(false, ["error" => "Only PDF files are supported"]);
}

// Store uploads
$uploadDir = __DIR__ . DIRECTORY_SEPARATOR . "uploads";
if(!is_dir($uploadDir)){
    @mkdir($uploadDir, 0755, true);
}

$safeBase = preg_replace('/[^a-zA-Z0-9._-]+/', '_', basename($name));
$dest = $uploadDir . DIRECTORY_SEPARATOR . time() . "_" . $safeBase;
if(!move_uploaded_file($tmp, $dest)){
    respond(false, ["error" => "Could not save uploaded file"]);
}

// Extract text: prefer pdftotext if available
$text = "";
$pdftotext = trim((string)@shell_exec("command -v pdftotext 2>/dev/null"));
if($pdftotext !== ""){
    $outTxt = $dest . ".txt";
    $cmd = escapeshellcmd($pdftotext) . " -layout " . escapeshellarg($dest) . " " . escapeshellarg($outTxt) . " 2>/dev/null";
    @shell_exec($cmd);
    if(is_file($outTxt)){
        $text = (string)@file_get_contents($outTxt);
        @unlink($outTxt);
    }
}

// Fallback: no extractor available
if(trim($text) === ""){
    respond(true, [
        "skills" => [],
        "warning" => "PDF text extraction tool not available on server. Install poppler (pdftotext) to enable extraction."
    ]);
}

$textLower = strtolower($text);

// Lightweight skill dictionary (expand anytime)
$skillDict = [
    "html" => ["html", "html5"],
    "css" => ["css", "css3", "sass", "scss"],
    "javascript" => ["javascript", "js", "ecmascript"],
    "typescript" => ["typescript", "ts"],
    "react" => ["react", "reactjs", "react.js"],
    "nextjs" => ["next.js", "nextjs"],
    "vue" => ["vue", "vuejs", "vue.js"],
    "node" => ["node", "nodejs", "node.js"],
    "express" => ["express", "expressjs"],
    "php" => ["php", "laravel"],
    "mysql" => ["mysql"],
    "mongodb" => ["mongodb", "mongo"],
    "postgresql" => ["postgres", "postgresql"],
    "python" => ["python"],
    "pandas" => ["pandas"],
    "numpy" => ["numpy"],
    "machine learning" => ["machine learning", "ml"],
    "deep learning" => ["deep learning"],
    "tensorflow" => ["tensorflow"],
    "pytorch" => ["pytorch"],
    "docker" => ["docker"],
    "kubernetes" => ["kubernetes", "k8s"],
    "aws" => ["aws", "amazon web services"],
    "linux" => ["linux"],
    "git" => ["git", "github", "gitlab"],
    "ci/cd" => ["ci/cd", "cicd", "continuous integration", "continuous delivery"],
    "rest api" => ["rest api", "restful api", "rest"],
];

$found = [];
foreach($skillDict as $canonical => $variants){
    foreach($variants as $v){
        $v = strtolower($v);
        // whole-word match for short tokens like "js"
        if(strlen($v) <= 3){
            if(preg_match('/\b'.preg_quote($v, '/').'\b/', $textLower)){
                $found[] = $canonical;
                break;
            }
        }else{
            if(strpos($textLower, $v) !== false){
                $found[] = $canonical;
                break;
            }
        }
    }
}

$found = array_values(array_unique($found));
respond(true, ["skills" => $found]);

?>

