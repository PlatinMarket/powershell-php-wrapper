<?php

// Starting Response Stream
ob_start();

// printr
function pr($data)
{
  echo "<pre>\r\n";
  echo print_r($data, true);
  echo "<pre>\r\n";
}

// Check Request From Local
function fromLocal(){
  if (!isset($_SERVER['LOCAL_ADDR'])) return $_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'];
  if (!isset($_SERVER['SERVER_ADDR'])) return $_SERVER['LOCAL_ADDR'] == $_SERVER['REMOTE_ADDR'];
  return ($_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'] || $_SERVER['LOCAL_ADDR'] == $_SERVER['REMOTE_ADDR']);
}

// Get ClientIP Address
function clientIp(){
  return isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
}

// Check Request From Trusted Zone
function isTrustedUser(){
  if (fromLocal() === true) return true;
  return strpos(clientIp(), ALLOW_IP) === 0;
}

// Exception Renderer
function error_handler($num, $str, $file, $line, $context = null)
{
    header('Content-Type: application/json');
    $errArr = array(
      'success' => 'error',
      'message' => $str . ($line ? ' at line ' . $line : '')
    );
    ob_clean();
    echo json_encode($errArr);
    exit();
}

// Exception Renderer
function exception_handler($e)
{
    error_handler($e->getCode(), $e->getMessage(), null, null, null);
}
set_error_handler("error_handler");
set_exception_handler("exception_handler");
