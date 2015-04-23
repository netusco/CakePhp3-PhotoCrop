<?php
namespace PhotoCrop\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;

/**
 * Photocrop Entity.
 */
class Photocrop extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'id' => true,
        'name' => true,
        'type' => true,
        'mime' => true,
        'width' => true,
        'height' => true,
        'bits' => true
    ];
    
}
