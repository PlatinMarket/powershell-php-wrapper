<?php
header('Content-Type: text/plain;');

$descriptorspec = array(
   0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
   1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
   2 => array("pipe", "w") // stderr is a file to write to
);

$repos = array(
  'C:\\Users\\Burak\\Documents\\GitHub\\hook-test',
  'C:\\inetpub\\Sites\\powershell-php-wrapper'
);

$tmp = array_merge($_SERVER, $_ENV);
$env = array();
foreach ($tmp as $key => $value) {
  if (!is_array($value)) $env[$key] = $value;
}

$result = array();
foreach ($repos as $repo)
{
  $result[$repo] = array('std_out' => '', 'std_err' => '', 'exit_code' => -1);
  $process = proc_open('git pull', $descriptorspec, $pipes, $repo, $env, array('bypass_shell' => true, 'suppress_errors' => true));

  if (is_resource($process))
  {
      fclose($pipes[0]);

      while($s = fgets($pipes[1], 1024)) {
        $result[$repo]['std_out'] .= $s;
      }

      while($s = fgets($pipes[2], 1024)) {
        $result[$repo]['std_err'] .= $s;
      }

      fclose($pipes[1]);

      $result[$repo]['exit_code'] = proc_close($process);
      $result[$repo]['std_out'] = trim($result[$repo]['std_out']);
      $result[$repo]['std_err'] = trim($result[$repo]['std_err']);
  }
}
echo serialize($result);
?>
