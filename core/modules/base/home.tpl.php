<h1>Welcome to the <a href="http://github.com/gintsmurans/staticphpc" target="_blank">StaticPHP framework</a> start page</h1>

<div class="content">

  <strong>Some variables:</strong>
  <ol>
    <li>Base uri: <strong><?php echo BASE_URI; ?></strong></li>
    <li>Site uri: <strong><?php echo router::site_uri(); ?></strong></li>
  </ol>

  <strong>This page files (edit them to change this page):</strong>
  <ol>
    <li>Controller: <strong><?php echo BASE_PATH . 'modules/base/base.php'; ?></strong></li>
    <li>View: <strong><?php echo __FILE__; ?></strong></li>
    <li>CSS: <strong><?php echo BASE_PATH . 'modules/base/style.css'; ?></strong></li>
  </ol>

  <div>&nbsp;</div>

  <strong>All included files:</strong>
  <ol>
  <?php foreach (get_included_files() as $file): ?>
    <li><?php echo $file; ?></li>
  <?php endforeach; ?>
  </ol>

</div>