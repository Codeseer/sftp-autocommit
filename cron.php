<?php
//no timeout since this can litterally take hours if the site is GB in size.
set_time_limit(0);

$connection = ssh2_connect('hostname', 22);
ssh2_auth_password($connection, 'username', 'password');

$remote_dir=".";
$local_dir=__DIR__.'/downloads/';

$sftp = ssh2_sftp($connection);

function downloadR($connection, $sftp, $local_dir, $remote_dir = '.', $depth = 0) {
  $entries = scandir("ssh2.sftp://$sftp/$remote_dir");
  foreach($entries as $entry) {
    if($entry !== '.' && $entry !== '..') {
      //file mode starts with 16 means it is a directory
      $remoteFile = "$remote_dir/$entry";
      $stat = ssh2_sftp_stat($sftp,$remoteFile);
      $is_dir = substr($stat['mode'], 0, 2) == '16';
      if($is_dir) {
        //echo("<span style='background-color: LightGreen;padding-left:${depth}0px;'>$remoteFile</span><br/>");
        mkdir($local_dir.$remoteFile);
        if($depth == 0) {
          downloadR($connection,$sftp,$local_dir,$remoteFile, $depth+1);
        }
      } else {
        //download the file.
        //echo( "<span style='padding-left:${depth}0px;'>$remoteFile</span><br/>");
        //don't download if the filesize has not changed.
        if($stat['size'] != stat($local_dir.$remoteFile)['size']) {
          $time_start = microtime(true);
          copy("ssh2.sftp://$sftp/$remoteFile", $local_dir.$remoteFile);
          $time_end = microtime(true);
          $deltaT = $time_end - $time_start;
          echo("Downloaded $remoteFile in $deltaT seconds.<br />");
        }
      }
    }
  }
}
$startAll = microtime(true);
downloadR($connection,$sftp,$local_dir);
$deltaT_all = microtime(true) - $startAll;
$deltaT_all = gmdate("H:i:s", $deltaT_all);

echo "<div style='background-color: LightGreen; text-align: center;'>Finished Sync in <b>$deltaT_all</b></div>";
?>
