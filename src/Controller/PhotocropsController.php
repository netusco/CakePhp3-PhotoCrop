<?php
namespace PhotoCrop\Controller;

use PhotoCrop\Controller\AppController;

/**
 * Photocrops Controller
 *
 * @property \PhotoCrop\Model\Table\PhotocropsTable $Photocrops
 */
class PhotocropsController extends AppController
{
    
    # Global Javascript Vars
    public $_jsPhotoCropVars = array();

    public function initialize()
    {
        parent::initialize();
        // $this->loadComponent('PhotoCrop.Photocrop');
    }

    /**
     * Remove photo crop method
     * TODO: allow access to owner or admin only
     * 
     * @return void 
     */
    public function removePhotoCrop($id)
    {
        $this->layout = false;
        $this->autoRender = false;
        $this->request->allowMethod('ajax');

        $entity = $this->Photocrops->get($id);
        $this->Photocrops->delete($entity);
    }

}
