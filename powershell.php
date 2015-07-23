<?php

class PowerShell
{
  // PowerShell Executable
  private $_psExec = null;

  // PowerShell Module Dir
  private $_psModuleDir = null;

  // Get SERVER Vairable
  public static function ENV($key)
  {
    $serverVariables = $_SERVER;
    foreach ($serverVariables as $k => $v) $serverVariables[strtoupper($k)] = $v;

    if (!$key || !is_string($key) || !isset($serverVariables[strtoupper($key)])) return null;
    return $serverVariables[strtoupper($key)];
  }

  // Public Construct
  public function __construct($psPath = null)
  {
    // Set Ps Executable Path
    if ($psPath)
      $this->_psExec = $psPath;
    else
      $this->_psExec = self::ENV('SYSTEMROOT') . DIRECTORY_SEPARATOR . 'system32' . DIRECTORY_SEPARATOR . 'WindowsPowerShell' . DIRECTORY_SEPARATOR . 'v1.0' . DIRECTORY_SEPARATOR . 'powershell.exe';

    // Set Ps Module Dir
    $this->_psModuleDir = self::ENV('PSMODULEPATH');

    // Validation
    if (!file_exists($this->_psExec)) throw new Exception('PowerShell executable not found');
  }

  // Parse argument array. 'key => value' or 'numeric => value'
  private function parseArguments($args = array())
  {
    $parsedArguments = array();
    if (!is_array($args)) $args = array();
    foreach ($args as $key => $value)
    {
      if (!is_numeric($key))
        $parsedArguments[] = '-' . $key . ' ' . $value;
      else
        $parsedArguments[] = $value;
    }
    return implode(' ', $parsedArguments);
  }

  // Execute Command
  public function execute($command, $args = array())
  {
    if (!is_string($command) || (!is_array($args) && !is_string($args))) return null;
    if (is_array($args) && $args = $this->parseArguments($args)) if (!empty($args)) $command = $command . ' ' . $args;
    $command = "\"" . $this->_psExec . "\"" . " \"" . $command . "\"";

    exec($command, $stdOut, $error);
    if ($error) throw new Exception("Error occured when executing command.");

    if (is_array($stdOut))
    {
      $stdOut = implode("\r\n", $stdOut);
      if (is_array($stdOut) && trim($stdOut[count($stdOut) - 1]) == "True") $stdOut = true;
      if (is_array($stdOut) && trim($stdOut[count($stdOut) - 1]) == "False") $stdOut = false;
    }

    $jsonArr = array();
    $jsonArr = json_decode($stdOut, true);
    if (json_last_error() == JSON_ERROR_NONE) $stdOut = $jsonArr;

    return $stdOut;
  }
}
