<?php
App::uses('AppController', 'Controller');
/**
 * Products Controller

 * @property Product $Product
 * @property PaginatorComponent $Paginator
 */
class DocumentsController extends AppController {
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator','Search.Prg','EventRegister','InventoryManager','Csv.Csv','Shopfy', 'WooCommerce', 'Amazon', 'Access');
    public $helpers = array('Product');
    public $paginate = array();
    public $theme = 'Mtro';

    public function beforeRender() {
        parent::beforeRender();
        $this->response->disableCache();
    }

    public function isAuthorized($user) {

        /*if (in_array($this->action, array('edit', 'delete'))) {
            $productId = (int) $this->request->params['pass'][0];
            if ($this->Product->isOwnedBy($productId, $user['id'])) {
                return true;
            } else {
                //You have no access to '. $this->action .' this product.
                $this->Session->setFlash(__('Authorization failed. You have no access for this action.'), 'admin/danger');
                return false;
            }
        }

        if (in_array($this->action, array('view'))) {
            $productId = (int) $this->request->params['pass'][0];
            if (!$this->Access->hasProductAccess($productId, 'r')) {
                $this->Session->setFlash(__('Authorization failed. You have no access for this product.'), 'admin/danger');
                return $this->redirect('index');
            }
        }

        if (in_array($this->action, array('add'))) {
            if($user['is_limited']) {
                $this->Session->setFlash(__('Authorization failed. You have no access to add products.'), 'admin/danger');
                return $this->redirect('index');
            }
        }

        if (in_array($this->action, array('upImg', 'backImg'))) {
            if(isset($user['is_admin']) && $user['is_admin'] == 1) {
                return true;
            } else {
                $this->Session->setFlash(__('Authorization failed.'), 'admin/danger');
                return $this->redirect('index');
            }
        }*/

        return parent::isAuthorized($user);
    }

    public function index($model_type='order',$model_id=null) {
        $this->layout = false;

        $documents = $this->Document->find('all', array(
            'conditions' => array('model_type' => $model_type, 'model_id' => $model_id),
        ));

        echo json_encode($documents);
        exit;
    }

    public function view($model_type='order',$model_id=null) {
        $this->layout = false;
        $this->set(compact('model_type', 'model_id'));
    }

    public function delete($id) {
        $this->Document->id = $id;
        $this->layout = false;

        $document = $this->Document->find('first', array(
            'conditions' => array('id' => $id),
            'contain' => false
        ));

        $target_dir = WWW_ROOT."uploads/documents/";
        $ex_file = pathinfo($document['Document']['attachment_path']);

        if ($this->Document->delete()) {
            $target_dir = WWW_ROOT."uploads/documents/";
            $ex_file = pathinfo($document['Document']['attachment_path']);

            if(file_exists($target_dir . $ex_file['basename'])) {
                @unlink($target_dir . $ex_file['basename']);
            }
            $response['action'] = 'success';
            $response['message'] = __('Document has been deleted');
        } else {
            $response['action'] = 'success';
            $response['message'] = __('The Document could not be deleted. Please, try again.');
        }
        echo json_encode($response);
        exit;
    }

    public function upload() {
        //pdf, doc,docx,xls,xlsx, 'gif', 'jpeg', 'png', 'jpg'
        $error_messages = array(
            1 => 'The uploaded file must be less then 2Mb',
            2 => 'The uploaded file must be less then 2Mb',
            3 => 'The uploaded file was only partially uploaded',
            4 => 'No file was uploaded',
            6 => 'Missing a temporary folder',
            7 => 'Failed to write file to disk',
            8 => 'A PHP extension stopped the file upload',
            'post_max_size' => 'The uploaded file must be less then 2Mb',
            'max_file_size' => 'File is too big',
            'min_file_size' => 'File is too small',
            'accept_file_types' => 'Filetype not allowed',
            'max_number_of_files' => 'Maximum number of files exceeded',
            'max_width' => 'Image exceeds maximum width',
            'min_width' => 'Image requires a minimum width',
            'max_height' => 'Image exceeds maximum height',
            'min_height' => 'Image requires a minimum height',
            'abort' => 'File upload aborted',
            'image_resize' => 'Failed to resize image'
        );
        $extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'gif', 'jpeg', 'png', 'jpg'];

        if ($this->request->is('post')) {
            
            $target_dir = WWW_ROOT."uploads/documents/";
            $file = pathinfo($_FILES['file']['name']);
            $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            if(in_array($extension, $extensions)) {
                if($extension) {
                    $extension = '.'.$extension;
                }
                $target_file = md5(time()) . $extension;
                $target_path = $target_dir . $target_file;
                
                $this->request->data['Document']['attachment_name'] = $_FILES['file']['name'];
                $this->request->data['Document']['attachment_path'] = Router::url('/', true) .'uploads/documents/'. $target_file;
                $this->request->data['Document']['user_id'] = $this->Auth->user('id');
                $this->request->data['Document']['created'] = date('Y-m-d H:i:s');
                
                if(!$_FILES['file']['error']) {
                    if(move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
                        if($this->Document->save($this->request->data)) {
                            $response['action'] = 'success';
                            $response['Document'] = $this->request->data['Document'];
                            $response['Document']['id'] = $this->Document->id;
                        } else {
                            $response['action'] = 'error';
                            $response['msg'] = 'Can\'t add document.';
                        }
                    } else {
                        $response['action'] = 'error';
                        $response['msg'] = 'Can\'t upload document.';
                    }
                } else {
                    $response['action'] = 'error';
                    $response['msg'] = $error_messages[$_FILES['file']['error']];
                }
            } else {
                $response['action'] = 'error';
                $response['msg'] = 'Use only allowed document types: '. implode(', ', $extensions);
            }
        } else {
            $response['action'] = 'error';
            $response['msg'] = 'Request not found';
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}