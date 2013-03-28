<?php
App::uses('AppController', 'Controller');
class LevelsController extends AppController {
  public $helpers = array('Html', 'Form');
  public $components = array('Auth');
  public $uses = array('Level', 'Rating');

  function levelFileName($level) {
    $levelName = $level['User']['username'] . "_" . $level['Level']['name'] . ".level";
    $levelName = strtr($levelName, array(
      ' ' => '_',
      '-' => '_'
    ));
    $levelName = ereg_replace('[^a-zA-Z0-9_.]', '', $levelName);
    return $levelName;
  }

  // gets a level by id and returns appropriate errors
  function getLevel($id) {
    if($id == null) {
      throw new BadRequestException('You must specify a level');
    }

    $level = $this->Level->findById($id);
    if(empty($level)) {
      throw new BadRequestException('Level not found');
    }

    return $level;
  }

  // returns the contents if it can or false otherwise
  public function getUploadedFile($arr) {
    if ((isset($arr['error']) && $arr['error'] == 0) ||
      (!empty( $arr['tmp_name']) && $arr['tmp_name'] != 'none')
    ) {
      if(is_uploaded_file($arr['tmp_name'])) {
        $handle = fopen($arr['tmp_name'], 'r');
        $content = fread($handle, $arr['size']);
        fclose($handle);
        return $content;
      }
    }
    return false;
  }

  public function beforeFilter() {
    parent::beforeFilter();
    $this->Auth->allow('download', 'raw', 'index', 'view', 'add');
    if($this->Auth->loggedIn()) {
      $this->Auth->allow('rate');
    }
  }

  public function index() {
    $this->set('levels', $this->Level->find('all'));
  }

  public function edit($id = null) {
    $level = $this->getLevel($id);

    if(!$this->Auth->loggedIn()) {
      throw new ForbiddenException('You must be logged in to edit a level');
    }

    if($level['User']['user_id'] != $this->Auth->user('user_id')) {
      throw new ForbiddenException('You can only edit a level you uploaded');
    }

    if($this->request->is('post') || $this->request->is('put')) {
      $this->Level->id = $id;
      if($this->Level->save($this->request->data)) {
        $this->Session->setFlash('Level updated');
        return $this->redirect(array('action' => 'view', $id));
      } else {
        $this->Session->setFlash('Unable to update level');
      }
    }

    if(empty($this->request->data)) {
      $this->request->data = $level;
    }
    $tags = $this->Level->Tag->find('list');
    $this->set(compact('tags'));
  }

  public function view($id) {
    $level = $this->Level->findById($id);
    $this->set('current_rating', $this->Rating->findByUserIdAndLevelId($this->Auth->user('user_id'), $id));
    $this->set('level', $level);
    $this->set('level_file_name', $this->levelFileName($level));
    $this->set('logged_in', $this->Auth->loggedIn());
    $this->set('is_owner', $level['User']['user_id'] == $this->Auth->user('user_id'));
  }

  public function rate($id, $value) {
    $this->Level->id = $id;
    if($this->Level->rate($this->Auth->user('user_id'), $value)) {
      $this->Session->setFlash('Rating updated');
    } else {
      $this->Session->setFlash('Could not set rating');
    }
    $this->redirect($this->referer());
  }

  public function add() {
    if($this->request->is('post')) {
      $this->Level->create();
      $this->Level->set('user_id', $this->Auth->user('user_id'));

      $contentFileContents = $this->getUploadedFile($this->request->data['Level']['contentFile']);
      if($contentFileContents !== false) {
        $this->request->data['Level']['content'] = $contentFileContents;
      }

      $levelgenFilelevelgens = $this->getUploadedFile($this->request->data['Level']['levelgenFile']);
      if($levelgenFilelevelgens !== false) {
        $this->request->data['Level']['levelgen'] = $levelgenFilelevelgens;
      }

      if($this->Level->save($this->request->data)) {
        $this->Session->setFlash('Your post has been saved.');
        $this->redirect(array('action' => 'index'));
      } else {
        $this->Session->setFlash('could not save level');
      }
    } else {
      $tags = $this->Level->Tag->find('list');
      $this->set(compact('tags'));
    }
  }

  public function raw($id = null, $type = 'content') {
    $level = $this->getLevel($id);

    if($type !== 'content' && $type !== 'levelgen') {
      throw new BadRequestException('Valid display modes are "level" and "levelgen"');
    }

    $this->response->type('text/text');
    $this->response->body($level['Level'][$type]);
    return $this->response;
  }

  public function download($id = null) {
    $level = $this->getLevel($id);

    $levelName = $this->levelFileName($level);

    $tmp = tempnam('/tmp', 'levelzip_');
    $zip = new ZipArchive();
    $zip->open($tmp, ZIPARCHIVE::OVERWRITE);
    $zip->addFromString($levelName, $level['Level']['content']);

    if(!empty($level['Level']['levelgen'])) {
      $zip->addFromString($level['Level']['levelgen_filename'], $level['Level']['levelgen']);
    }

    $zip->close();

    $this->response->file($tmp);
    $this->response->download($levelName . '.zip');
    return $this->response;
  }
}
?>
