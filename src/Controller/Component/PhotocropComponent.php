<?php

namespace PhotoCrop\Controller\Component;

use Cake\Controller\Component;

class PhotocropComponent extends Component
{

    protected $_photocropDefaults = [
        'default' => [
            'type' => 'default',
            'maxNumImagesAllowed' => 5,
            'maxFileSizeAllowed' => 10, //MBs
            'qualityJpeg' => 0.85, //from 0.0 to 1 (normal default is 0.92)
            'allowResize' => true,
            'selMinSize' => [ 50, 50 ], //pxs
            'maxWidthPreview' => 500, //pxs
            'maxHeightPreview' => false,
            'aspectRatio' => 1, //1 square (16/9, 4/3, etc)
            'bgOpacity' => 0.5,
            'bgColor' => 'black',
            'cropImageWidth' => 600, //if ommited and added cropImageHeight width is calculated according to aspectRatio
        ]
    ];

    protected $_defaultConfig = [];

    /**
     * Initialize properties.
     *
     * @param array $config The config data.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->Controller = $this->_registry->getController();
        $this->Model = $this->Controller->{$this->Controller->modelClass};
        $this->Model->hasMany('PhotoCrop.Photocrops', ['dependent' => true]);
        if(!empty($this->_config)) {
            foreach($this->_config as $type => $configs) {
                $this->_config[$type]['type'] = $type;
                $this->_config[$type] = array_merge($this->_photocropDefaults['default'], $this->_config[$type]);
            }
        }
        $jsPhotoCropVars = (!empty($this->_config)) ? $this->_config : $this->_photocropDefaults;
        $this->Controller->set(compact('jsPhotoCropVars'));
    }
    
    /**
     * Prepares Photocrops information on request->data using the model method preparePhotoCropImages.
     * Patches the entity to be ready for the save associated
     *
     * @param obj $entity
     * @return obj $entity
     */
    public function preparePhotocropsAndPatchEntity($entity) 
    {
        $this->request->data = $this->Model->Photocrops->preparePhotoCropImages($this->request->data);
        // add photocrops to the list of fields accessible for mass asign
        $entity->accessible('photocrops', true);
        $entity = $this->Model->patchEntity($entity, $this->request->data, ['associated' => ['Photocrops']]);

        return $entity;
    }

}
