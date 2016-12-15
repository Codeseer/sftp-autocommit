<div style="text-align:center;">
<form style="display:inline-block; padding-top:40px;" id="site_form" method="post" class="pure-form pure-form-stacked">
  <input name="host" type="text" placeholder="Host" value="<?= $host ?>">
  <input name="port" type="text" placeholder="Port" value="<?= $port ?>">
  <input name="user" type="text" placeholder="User" value="<?= $user ?>">
  <input name="password" type="password" placeholder="Password" value="<?= $password ?>">
  <input name="remote_path" type="text" placeholder="Remote Path" value="<?= $remote_path ?>">
  <input name="id" type="hidden" value="<?= isset($id) ? $id : 'create' ?>">
  <button type="submit" class="pure-button pure-button-primary"><?= isset($id) ? 'UPDATE' : 'CREATE' ?></button>
</form>
</div>
