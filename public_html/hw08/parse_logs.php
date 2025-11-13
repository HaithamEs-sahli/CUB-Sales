<?php
/*
 * Databases Project 2025 - Assignment 8
 * Person A Work - Log Parsing Script
 *
 * This script reads Apache access and error logs and produces:
 *   - access_clean.csv
 *   - page_counts.csv
 *   - browser_counts.csv
 *   - error_clean.csv
 *
 * Make sure access.log and error.log are in the same folder as this script.
 */

// Paths
$accessLogPath = __DIR__ . "/access.log";
$errorLogPath  = __DIR__ . "/error.log";

/* -----------------------------------------------------------
   Helper: detect browser
------------------------------------------------------------ */
function detectBrowser($ua) {
    $ua = strtolower($ua);
    if (strpos($ua, 'edge') !== false) return 'Edge';
    if (strpos($ua, 'chrome') !== false && strpos($ua, 'chromium') === false) return 'Chrome';
    if (strpos($ua, 'firefox') !== false) return 'Firefox';
    if (strpos($ua, 'safari') !== false && strpos($ua, 'chrome') === false) return 'Safari';
    if (strpos($ua, 'msie') !== false || strpos($ua, 'trident') !== false) return 'Internet Explorer';
    return 'Other';
}

/* -----------------------------------------------------------
   Helper: convert Apache date to ISO YYYY-MM-DD HH:MM:SS
------------------------------------------------------------ */
function apacheDateToIso($dateStr) {
    $dt = DateTime::createFromFormat('d/M/Y:H:i:s O', $dateStr);
    if ($dt) return $dt->format('Y-m-d H:i:s');
    return $dateStr;
}

function apacheErrorDateToIso($dateStr) {
    // Try with microseconds
    $dt = DateTime::createFromFormat('D M d H:i:s.u Y', $dateStr);
    if ($dt) return $dt->format('Y-m-d H:i:s');

    // Try without microseconds
    $dt = DateTime::createFromFormat('D M d H:i:s Y', $dateStr);
    if ($dt) return $dt->format('Y-m-d H:i:s');

    return $dateStr;
}

/* -----------------------------------------------------------
   PROCESS access.log
------------------------------------------------------------ */

if (!file_exists($accessLogPath)) {
    echo "ERROR: access.log not found!\n";
} else {
    $fh = fopen($accessLogPath, 'r');

    $rows = [];
    $pageCounts = [];
    $browserCounts = [];

    // Common Apache combined log format
    $pattern = '/^(\S+) \S+ \S+ \[([^\]]+)\] "(\S+) (.*?) HTTP\/[0-9.]+" (\d{3}) \S+ "[^"]*" "([^"]*)"/';

    while ($line = fgets($fh)) {
        if (!preg_match($pattern, $line, $m)) continue;

        $ip        = $m[1];
        $rawDate   = $m[2];
        $method    = $m[3];
        $rawPath   = $m[4];
        $status    = $m[5];
        $userAgent = $m[6];

        $timestamp = apacheDateToIso($rawDate);

        // extract only path (remove query strings)
        $urlParts = parse_url($rawPath);
        $page     = $urlParts['path'] ?? $rawPath;

        $browser  = detectBrowser($userAgent);

        $rows[] = [
            $timestamp,
            $ip,
            $method,
            $page,
            $status,
            $browser,
            $userAgent
        ];

        // count page hits
        if (!isset($pageCounts[$page])) $pageCounts[$page] = 0;
        $pageCounts[$page]++;

        // count browsers
        if (!isset($browserCounts[$browser])) $browserCounts[$browser] = 0;
        $browserCounts[$browser]++;
    }

    fclose($fh);

    // Write access_clean.csv
    $out = fopen(__DIR__ . "/access_clean.csv", "w");
    fputcsv($out, ["timestamp", "ip", "method", "page", "status", "browser", "user_agent"]);
    foreach ($rows as $r) fputcsv($out, $r);
    fclose($out);

    // Write page_counts.csv
    $out = fopen(__DIR__ . "/page_counts.csv", "w");
    fputcsv($out, ["page", "hits"]);
    foreach ($pageCounts as $p => $c) fputcsv($out, [$p, $c]);
    fclose($out);

    // Write browser_counts.csv
    $out = fopen(__DIR__ . "/browser_counts.csv", "w");
    fputcsv($out, ["browser", "hits"]);
    foreach ($browserCounts as $b => $c) fputcsv($out, [$b, $c]);
    fclose($out);

    echo "✔ access.log processed.\n";
}

/* -----------------------------------------------------------
   PROCESS error.log
------------------------------------------------------------ */

if (!file_exists($errorLogPath)) {
    echo "ERROR: error.log not found!\n";
} else {
    $fh = fopen($errorLogPath, 'r');
    $errorRows = [];

    // Example Apache error log line:
    // [Wed Oct 11 14:32:52.123456 2025] [core:error] [client 1.2.3.4:5678] message
    $pattern = '/^\[([^\]]+)\] \[([^\]]+)\](?: \[pid [^\]]+\])?(?: \[client ([^\]]+)\])? (.*)$/';

    while ($line = fgets($fh)) {
        if (!preg_match($pattern, $line, $m)) continue;

        $rawDate  = $m[1];
        $moduleLv = $m[2];
        $client   = $m[3] ?? "";
        $message  = trim($m[4]);

        $timestamp = apacheErrorDateToIso($rawDate);

        // Extract IP only
        $ip = "";
        if ($client !== "") {
            $parts = explode(":", $client);
            $ip = $parts[0];
        }

        // Split "core:error"
        $module = "";
        $level  = "";
        if (strpos($moduleLv, ':') !== false) {
            [$module, $level] = explode(":", $moduleLv, 2);
        } else {
            $module = $moduleLv;
        }

        $errorRows[] = [
            $timestamp,
            $ip,
            $module,
            $level,
            $message
        ];
    }

    fclose($fh);

    // Write error_clean.csv
    $out = fopen(__DIR__ . "/error_clean.csv", "w");
    fputcsv($out, ["timestamp", "ip", "module", "level", "message"]);
    foreach ($errorRows as $r) fputcsv($out, $r);
    fclose($out);

    echo "✔ error.log processed.\n";
}

echo "✔ All tasks completed.\n";
?>
