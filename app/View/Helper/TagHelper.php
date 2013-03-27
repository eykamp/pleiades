<?php
// enum class of tags and names
final class Tags {
  public $novelty = 1;
  public $dungeon = 2;
  public $workInProgress = 4;
}

App::uses('AppHelper', 'View/Helper');
class TagHelper extends AppHelper {
  public $helpers = array('Form', 'Html');
  public function tags($int) {
    $result = array();
    foreach(new Tags() as $name => $bit) {
      if ($int & $bit) {
        array_push($result, $name);
      }
    }
    return $result;
  }

  public function int($data) {
    $result = 0;
    foreach(new Tags() as $name => $bit) {
      if (isset($data[$name])) {
        $result |= $bit;
      }
    }
    return $result;
  }

  public function allTags() {
    $tags = array();
    foreach(new Tags() as $name => $bit) {
        array_push($tags, $name);
    }
    return $tags;
  }

  public function tagInput($int) {
    $result = "";
    foreach(new Tags() as $name => $bit) {
      $result .= $this->Form->input($name, array(
        'type' => 'checkbox',
        'checked' => ($int & $bit) > 0
      ));
    }
    $result = "<fieldset><legend>Tags</legend>$result</fieldset>";
    return $result;
  }
}
