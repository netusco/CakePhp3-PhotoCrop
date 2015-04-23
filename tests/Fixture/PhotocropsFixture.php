<?php
namespace PhotoCrop\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class PhotocropsFixture extends TestFixture
{
    
    /**
     * fields property
     *
     * @var array
     */
    
    /**
     * Import
     *
     * @var array
     */
    public $import = ['table' => 'photocrops'];
    
    /**
     * method init
     * 
     * @return void
     */
    public function init() {
        
        $this->records = [
        [
            'name' => 'profile_55156r43jf779.png',
            'type' => 'profile',
            'mime' => 'image/png',
            'width' => '600',
            'height' => '600',
            'bits' => '8',
        ], [
            'name' => 'profile_43233i51oj323.png',
            'type' => 'profile',
            'mime' => 'image/png',
            'width' => '600',
            'height' => '600',
            'bits' => '8',
        ], [
            'name' => 'profile_22134t22iu454.png',
            'type' => 'profile',
            'mime' => 'image/png',
            'width' => '600',
            'height' => '600',
            'bits' => '8',
        ]
    ];
        parent::init();
    }
    
 }
