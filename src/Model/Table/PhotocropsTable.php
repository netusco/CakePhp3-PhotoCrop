<?php
namespace PhotoCrop\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;
use Cake\Utility\Inflector;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

class PhotocropsTable extends Table 
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->primaryKey('id');
        $this->table('photocrops');
        $this->photocropsToRemove = [];
        $this->photocropsToUpload = [];
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator->add('name', [
            'unique' => [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Une image avec ce nom existe déjà.'
            ]
        ]);

        return $validator;
    }

    /**
    * beforeSave, starts a time before a save is initiated.
    *
    * @param array $option
    * @return boolean true or false if slug already used
    */
    public function beforeSave($event, $entity, $options) 
    {
        $uploaded = false;

        foreach($this->photocropsToUpload as $photocrop) {
            if($photocrop['name'] === $entity->name) {
                $uploaded = $this->uploadPhotocrop($photocrop); 

                break; // beforeSave it's called for each single element
            }
        }

        return $uploaded;
    }

    /**
     * Before delete logic
     * Get the name of the photocrop to delete on the server
     * 
     * @param Event $event, Entity $entity, ArrayObject $options
     * @return void
     */
    public function beforeDelete($event, $entity, $options) {
        $this->photocropsToRemove = $this->find('list', [
            'keyField' => 'type',
            'valueField' => 'name' 
            ])
            ->where(['id' => $entity->id])
            ->toArray();
    }
    
    /**
     * After delete logic
     * Knowing the name and type of the photocrop to remove we erase it from the server
     * 
     * @param Event $event, Entity $entity, ArrayObject $options
     * @return void
     */
    public function afterDelete($event, $entity, $options) {
        if (!empty($this->photocropsToRemove) && is_array($this->photocropsToRemove)) {
            $file = new File(WWW_ROOT . 'photocrops' . DS . key($this->photocropsToRemove) . DS . current($this->photocropsToRemove), false, 0777);
            $file->delete();       
        }
        $this->photocropsToRemove = [];
    }
    
    /**
     * gets data submited searches for Photos, prepares the data to save the photos
     * 
     * @param array $data
     * @return array $data
     */
    public function preparePhotoCropImages($data) {  
        if ( isset($data['photocrops']) ) {
            if (isset($data['photocrops']) && $data['photocrops']) {
                $photos = [];
                foreach ($data['photocrops'] as $key => $base64) {
                    preg_match('~image/(.*?);base64~', $base64, $imgType);            
                    $explode = explode('_', $key);
                    $type = ($explode[0]) ? $explode[0] : 'default';
                    $name = uniqid($type . '_') . '.' . $imgType[1]; // create a unique name for each photo

                    $photo_decoded = base64_decode(substr($base64, strpos($base64, "base64,") + 7));
                    // get photo info parameters
                    $photo_info = getimagesizefromstring($photo_decoded);

                    if (is_array($photo_info)) {
                        $photo = array(
                            'name' => $name,
                            'type' => $type,
                            'width' => $photo_info[0],
                            'height' => $photo_info[1],
                            'bits' => $photo_info['bits'],
                            'mime' => $photo_info['mime']
                        );
                        array_push($photos, $photo);
                        $this->photocropsToUpload[] = array_merge($photo, array('data' => $photo_decoded));
                    }
                }
                $data['photocrops'] = $photos;
            }
        }

        return $data;
    }

    /**
     * Given an array of data from a photocrop to be saved it creates a folder if needed and saves the photo on the server
     * 
     * @param array $data
     * @return bool
     */
    private function uploadPhotocrop($data) {
        // save photocrop to webroot/photocrops folder
        if (!empty($data)) {
            // create directory if it doesn't exist
            $dir = new Folder(WWW_ROOT . 'photocrops' . DS . $data['type'] . DS, true, 0755);
            // save cropped image as a file
            if(file_put_contents($dir->path . $data['name'], $data['data']) !== false) {
                return true;
            }
        }
        return false;
    }
}

