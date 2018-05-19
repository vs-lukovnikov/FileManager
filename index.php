<?php error_reporting(0); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <?php
  require('FileManager.php');

  isset($_GET['cat']) ? $cat = $_GET['cat'] : $cat = getcwd();
  isset($_GET['sort']) ? $sort = $_GET['sort'] : $sort = 'name';
  isset($_GET['order']) ? $order = $_GET['order'] : $order = 'asc';

  $sort = $sort . $order;

  $file_manager = FileManager::getInstance($cat);
  $sort_criteria = $file_manager::$sort_criteria;
  $files = $file_manager->sort($file_manager->files, $sort_criteria[$sort], TRUE);

  isset($_GET['cat']) ? $path = $_GET['cat'] : $path = dirname(__FILE__);

  $order = [
    'name' => 'desc',
    'type' => 'desc',
    'size' => 'desc',
    'extension' => 'desc',
  ];
  ?>
  <title>Test File Manager</title>
</head>
<body>
<p>
  <?php
  echo "<p>Directory: " . $path . "</p>";

  if ($path !== dirname(__FILE__)) {
    $back = $_SERVER['PHP_SELF'] . "?cat=" . realpath($_GET['cat'] . '/../');
    echo "<a href=" . $back . ">Exit</a>";
  }
  if (isset($_GET['sort'])) {
    ($_GET['order'] === 'desc') ? $order[$_GET['sort']] = 'asc' : $order_name = 'desc';
  }
  ?>
</p>
<table>
  <tr>
    <th>
      <a href="<?php echo $_SERVER['PHP_SELF'] . '?sort=name&order='
        . $order['name']
        . '&cat='
        . realpath($cat); ?>">Name</a>
    </th>
    <th>
      <a href="<?php echo $_SERVER['PHP_SELF'] . '?sort=type&order='
        . $order['type']
        . '&cat='
        . realpath($cat); ?>">Type</a>
    </th>
    <th>
      <a href="<?php echo $_SERVER['PHP_SELF'] . '?sort=extension&order='
        . $order['extension']
        . '&cat='
        . realpath($cat); ?>">Extension</a>
    </th>
    <th>
      <a href="<?php echo $_SERVER['PHP_SELF'] . '?sort=size&order='
        . $order['size']
        . '&cat='
        . realpath($cat); ?>">Size</a>
    </th>
  </tr>
  <?php if ($files == NULL): ?>
    <tr>
      <td>No files</td>
    </tr>
  <?php else: ?>
    <?php foreach ($files as $file): ?>
      <tr>
        <td>
          <?php
          if ($file['type'] === 'dir') {
            $link = $_SERVER['PHP_SELF'] . "?cat=" . $path . '/' . $file['name'];
            echo "<a href=" . $link . ">" . $file['name'] . "</a>";
          }
          else {
            echo $file['name'] . '.' . $file['extension'];
          }
          ?>
        </td>
        <td><?php echo $file['type']; ?></td>
        <td><?php echo $file['extension']; ?></td>
        <td><?php echo $file['size'] . ' kB'; ?></td>
      </tr>
    <?php endforeach; ?>
  <?php endif; ?>
</table>
</body>
</html>
