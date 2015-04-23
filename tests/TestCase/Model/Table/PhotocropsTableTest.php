<?php
namespace PhotoCrop\Test\TestCase\Model\Table;

use PhotoCrop\Model\Table\PhotocropsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * PhotoCrop\Model\Table\PhotocropsTable Test Case
 */
class PhotocropsTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'Photocrops' => 'plugin.photo_crop.photocrops'
    ];

    /**
     * Debug to display cases titles
     * 
     * @var bool
     */
    public $debug = true;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        
        $config = TableRegistry::exists('Photocrops') ? [] : ['className' => 'PhotoCrop\Model\Table\PhotocropsTable'];
        $this->PhotocropsTable = TableRegistry::get('Photocrops', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PhotocropsTable);

        parent::tearDown();
    }
    
    /**
     * preparePhotoCropImages method
     *
     * @return void
     */
    public function testpreparePhotoCropImages()
    {   
        $data = [
            'nom' => 'Françoi',
            'prenom' => 'Couchemar',
        ];

        // no photocrops added
        if($this->debug) debug('PHOTOCROPS MODEL TABLE - testpreparePhotoCropImages: no photocrops added');
        $this->assertNotContains('photocrops', $this->PhotocropsTable->preparePhotoCropImages($data));
        $this->assertContains('Françoi', $this->PhotocropsTable->preparePhotoCropImages($data));

        // all ok
        if($this->debug) debug('PHOTOCROPS MODEL TABLE - testpreparePhotoCropImages: all ok');
        $data = array_merge($data, ['photocrops' => [
                'profile_0' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAlgAAAJYCAYAAAC+ZpjcAAAgAElEQVR4Xuy9WY+lW5IltI/Ps3tE3HnIoYo/xAtvPCEhnlpCAiEeEDwgEKip7s6iuqq66a4qgRDzIARi+D2dd4yIG+HzjNZatmzb/s53POLezKysUtfN8nIP93PO9317MFt72TKzxb/6b/zbjw+PD+3x8bE9Pjw2/s//xu8eH1vD/z3oNW3R2tbmZtvd3m7Hh4ft808+bn/wi5/za+3hoV2cn7Xz84t2d3vX9PJF29jYbHt7B21/f6ft7Gy3ra2Ntrm50TY2Fg0fzks8tnZ399Du7h/a/f1Du727579v7u7a1dV1u7y8bpfX1+3m9q7d3Ny0u7s7vg7vw3+8f98vP1PPgf9wD/y+0BcfqPxXX8v7ic/DD7e3t/y6urpqb9++bW/evOG1d3Z22/HxcXv+/FlbX1/n19raWltbrOmTeSE9W9xh/L5feLFYNHxN76X+m3+dvGZ4Q/6jXmv' 
            ]]);
        $this->assertContains('image/png', $this->PhotocropsTable->preparePhotoCropImages($data)['photocrops'][0]);
    }
}
