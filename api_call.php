<?php

  // Loading Library
  require_once "vendor/autoload.php";
  require_once "config.php";
  require_once "utils.php";

  // ps04 Library
  use mikehaertl\shellcommand\Command;

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
    $command = new Command($command);
    if ($command->execute())
    {
      $returnArray['data'] = $command->getOutput();
      $returnArray['exit_code'] = $command->getExitCode();
    }
    else
    {
      $returnArray['success'] = 'error';
      $returnArray['exit_code'] = $command->getExitCode();
      $returnArray['message'] = $command->getError();
    }
  }

  // Parse Request
  echo json_encode($returnArray);

?>
