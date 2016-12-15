<link rel="stylesheet" href="https://unpkg.com/purecss@0.6.0/build/pure-min.css">
<?php
  //GET show new site form.
  //POST create new site with SFTP details
  $db = new SQLite3('sites.db');
  $db->query('CREATE TABLE IF NOT EXISTS "sites" (
      "id" integer PRIMARY KEY AUTOINCREMENT NOT NULL,
      "host" varchar(1024),
      "port" varchar(1024),
      "user" varchar(1024),
      "password" varchar(1024),
      "remote_path" varchar(1024)
    );');
    $query = "SELECT * FROM sites";
    $results = $db->query($query);
    echo '<table style="margin:auto; text-align:center;" class="pure-table pure-table-bordered">';
    include 'list_head.php';
    echo '<tbody>';
    while($site = $results->fetchArray()) {
      include 'list_tr.php';
    }
    echo '</tbody>';
    echo '</table>';
 ?>
