<?php

/**
 * Trait TraitSingletone
 */
trait TraitSingletone {

  private static $instance;

  public static function getInstance($folder) {
    if (self::$instance === NULL) {
      self::$instance = new static($folder);
    }
    return self::$instance;
  }
}

class FileManager {

  use TraitSingletone;

  /**
   * @var array
   */
  public $files;

  /**
   * @var array contains criteries for sorting data.
   */
  public static $sort_criteria = [
    'namedesc' => [
      'type' => [SORT_ASC],
      'name' => [SORT_DESC, SORT_REGULAR],
    ],
    'nameasc' => [
      'type' => [SORT_ASC],
      'name' => [SORT_ASC, SORT_REGULAR],
    ],
    'sizedesc' => [
      'type' => [SORT_ASC],
      'size' => [SORT_DESC, SORT_NATURAL],
    ],
    'sizeasc' => [
      'type' => [SORT_ASC],
      'size' => [SORT_ASC, SORT_NATURAL],
    ],
    'typedesc' => [
      'type' => [SORT_DESC],
    ],
    'typeasc' => [
      'type' => [SORT_ASC],
    ],
    'extensiondesc' => [
      'extension' => [SORT_DESC,SORT_NATURAL],
    ],
    'extensionasc' => [
      'extension' => [SORT_ASC,SORT_NATURAL],
    ],
  ];

  private function __construct($folder) {
    $this->files = $this->getFiles($folder);
  }

  /**
   * Create array with data of files and dirs contained in $directory.
   *
   * @param string $directory
   *
   * @return array $files array of files and dir's in input
   * directory
   */
  public function getFiles($directory) {
    $files = [];

    if ($directory[strlen($directory) - 1] !== '/') {
      $directory .= '/';
    }

    if ($catalog = opendir($directory)) {
      while (FALSE !== ($item = readdir($catalog))) {
        if (!in_array($item, [".", "..", ".git"])) {
          $file = $directory . $item;
          $file_name = pathinfo($item);
          $file_size = filesize($file) / 8;
          $file_type = filetype($file);
          if ($file_type === 'dir') {
            $path = $directory . $file_name['filename'];
          }
          $file_data = [
            'name' => $file_name['filename'],
            'extension' => $file_name['extension'] && $file_type == 'file' ? $file_name['extension'] : '',
            'size' => $file_size,
            'type' => $file_type,
            'path' => $path,
          ];
          $files[] = $file_data;
        }
      }
    }
    closedir($catalog);
    return $files;
  }

  /**
   * Method sort $files array
   *
   * @param  array $data input array for sorting
   * @param  array $sort_criteria sorting data criteries
   * @param  bool $case_in_sensitive sort criteria - sensitive or not to
   *   data register
   *
   * @return array  $files sorted array
   */
  public function sort($data, $sort_criteria, $case_in_sensitive = TRUE) {
    if (!is_array($data) || !is_array($sort_criteria)) {
      return FALSE;
    }
    $args = [];
    $i = 0;

    foreach($sort_criteria as $sort_column => $sort_attributes) {
      $col_lists = [];
      foreach ($data as $key => $row) {
        $convert_to_lower = $case_in_sensitive && (in_array(SORT_STRING, $sort_attributes) || in_array(SORT_REGULAR, $sort_attributes));
        $row_data = $convert_to_lower ? strtolower($row[$sort_column]) : $row[$sort_column];
        var_dump($row_data);
        $col_lists[$sort_column][$key] = $row_data;
      }
      $args[] = &$col_lists[$sort_column];

      foreach ($sort_attributes as $sort_attribute) {
        $tmp[$i] = $sort_attribute;
        $args[] = &$tmp[$i];
        $i++;
      }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return end($args);
  }
}