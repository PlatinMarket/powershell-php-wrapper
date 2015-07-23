<?php

  // Loading Library
  require_once "config.php";
  require_once "utils.php";
  require_once "powershell.php";

  // Setting Headers
  header('Content-Type: application/json');

  // If Not Trusted
  if (!isTrustedUser()) throw new Exception('Unauthorized');

  $returnArray = array('success' => 'ok');
  if (!isset($_POST['command']))
  {
    $returnArray['success'] = 'ok';
    $returnArray['message'] = 'Ready Comrad!';
  }
  else
  {
    $command = $_POST['command'];
    $args = isset($_POST['args']) ? $_POST['args'] : array();
    $ps = new PowerShell();
    $returnArray['data'] = $ps->execute($command, $args);
  }

  // Parse Request
  echo json_encode($returnArray);

?>
