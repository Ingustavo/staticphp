
<style>@import '<?php echo site_url('css/users.css'); ?>';</style>
<script type="text/javascript" src="<?php echo site_url('js/users.js'); ?>"></script>

<form action="" method="post">
  <table class="user-list">
    <tr>
      <td>Username:</td>
      <td><input type="text" name="username" value="<?php echo $user->username; ?>" /></td>
    </tr>
  
    <tr>
      <td>Password:</td>
      <td><input type="password" name="password" /></td>
    </tr>
  
    <tr>
      <td>Access:</td>
      <td class="text-left">
        <ul>
          <li><input type="checkbox" name="c[*]" value="*"<?php if (!empty($user->access->{'*'})){ echo ' checked="checked"'; } ?> /> All</li>
          <?php foreach ($access as $tmp_item): ?>
            <li>
              <input type="checkbox" name="c[<?php echo $tmp_item['name']; ?>]" value="*"<?php if (!empty($user->access->{$tmp_item['name']}) && $user->access->{$tmp_item['name']} == '*'){ echo ' checked="checked"'; } ?> /><?php echo $tmp_item['name']; ?>
              <?php if (!empty($tmp_item['methods']) && is_array($tmp_item['methods'])): ?>
                <ul>
                <?php foreach ($tmp_item['methods'] as $method): ?>
                  <li><input type="checkbox" name="c[<?php echo $tmp_item['name']; ?>][<?php echo $method; ?>]" value="*"<?php if (!empty($user->access->{$tmp_item['name']}->{$method})){ echo ' checked="checked"'; } ?> /><?php echo $method; ?></li>
                <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </td>
    </tr>
    <tr>
      <td colspan="2" class="text-right">
        <input type="submit" value="Edit" />
      </td>
    </tr>
  </table>
</form>