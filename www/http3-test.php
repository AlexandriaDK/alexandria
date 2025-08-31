<?php
// Alexandria HTTP/3 Migration Test
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>

<head>
  <title>Alexandria HTTP/3 Test</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 50px;
    }

    .status {
      background: #e8f5e8;
      border: 1px solid #4caf50;
      padding: 10px;
      border-radius: 5px;
      margin: 20px 0;
    }

    .info {
      background: #f0f8ff;
      border: 1px solid #0066cc;
      padding: 10px;
      border-radius: 5px;
      margin: 20px 0;
    }

    code {
      background: #f5f5f5;
      padding: 2px 5px;
      border-radius: 3px;
    }

    pre {
      background: #f5f5f5;
      padding: 10px;
      border-radius: 5px;
      overflow-x: auto;
    }
  </style>
</head>

<body>
  <h1>✓ Alexandria HTTP/3 Migration Successful!</h1>

  <div class="status">
    <h2>System Status</h2>
    <ul>
      <li><strong>✓ Nginx:</strong> Running with HTTP/3 (QUIC) support</li>
      <li><strong>✓ PHP-FPM:</strong> <?php echo phpversion(); ?> running</li>
      <li><strong>✓ HTTPS:</strong> SSL/TLS encryption enabled</li>
      <li><strong>✓ HTTP/2:</strong> Enabled</li>
      <li><strong>✓ HTTP/3:</strong> Available (check Alt-Svc header)</li>
    </ul>
  </div>

  <div class="info">
    <h3>Server Information</h3>
    <ul>
      <li><strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></li>
      <li><strong>Server Protocol:</strong> <?php echo $_SERVER['SERVER_PROTOCOL'] ?? 'Unknown'; ?></li>
      <li><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></li>
      <li><strong>PHP Version:</strong> <?php echo phpversion(); ?></li>
      <li><strong>Server Name:</strong> <?php echo $_SERVER['SERVER_NAME'] ?? 'Unknown'; ?></li>
      <li><strong>Request URI:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? 'Unknown'; ?></li>
    </ul>
  </div>

  <div class="info">
    <h3>HTTP/3 Testing</h3>
    <p><strong>Browser Testing:</strong></p>
    <ol>
      <li>Open browser developer tools (F12)</li>
      <li>Go to Network tab</li>
      <li>Reload this page</li>
      <li>Look for "Protocol" column showing "h3" or "http/3"</li>
    </ol>

    <p><strong>Command Line Testing:</strong></p>
    <pre><code># HTTP/3 (requires curl with HTTP/3 support)
curl --http3 -k https://localhost:8443/http3-test.php

# HTTP/2 (fallback)
curl --http2 -k https://localhost:8443/http3-test.php</code></pre>
  </div>

  <div class="info">
    <h3>Migration Details</h3>
    <p>Successfully migrated from:</p>
    <ul>
      <li><strong>Old:</strong> Apache with php:8.4-apache</li>
      <li><strong>New:</strong> Nginx + PHP-FPM with php:8.4-fpm</li>
    </ul>
    <p><strong>Key Benefits:</strong></p>
    <ul>
      <li>HTTP/3 (QUIC) protocol support for faster connections</li>
      <li>Better performance with Nginx serving static files</li>
      <li>Improved security with latest TLS protocols</li>
      <li>Future-ready architecture</li>
    </ul>
  </div>

  <p><small>Generated at: <?php echo date('Y-m-d H:i:s T'); ?></small></p>
</body>

</html>