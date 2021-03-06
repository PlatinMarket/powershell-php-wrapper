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
    $ps = new PowerShell();
    $returnArray['data'] = $ps->execute($command);
  }

  // Parse Request
  echo json_encode($returnArray);

?>
