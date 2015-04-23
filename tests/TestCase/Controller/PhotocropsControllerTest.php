<?php
namespace PhotoCrop\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use PhotoCrop\Controller\PhotocropsController;
use Cake\ORM\TableRegistry;

/**
 * PhotoCrop\Controller\PhotocropsController Test Case
 */
class PhotocropsControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'Photocrops' => 'plugin.photo_crop.photocrops',
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
     **/
    public function setUp() {
        parent::setUp();
        $config = TableRegistry::exists('Photocrops') ? [] : ['className' => 'PhotoCrop\Model\Table\PhotocropsTable'];
        $this->Photocrops = TableRegistry::get('Photocrops', $config);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown() {
        unset($this->Users);

        parent::tearDown();
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testremovePhotoCrop()
    {
        $this->session([
            'Auth.User.id' => 2,
        ]);

        if($this->debug) debug('PHOTOCROPS PLUGIN CONTROLLER - testRemovePhotoCrop: not an ajax call'); 
        $this->get('/photo_crop/photocrops/removePhotoCrop/1');
        $this->assertResponseCode(405);

        if($this->debug) debug('PHOTOCROPS PLUGIN CONTROLLER - testRemovePhotoCrop: all OK'); 
        // to simulate an ajax request
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $query = $this->Photocrops->find()
                ->where(['name' => 'profile_55156r43jf779.png'])
                ->select('id')
                ->first();
        $this->assertEquals(1, $query['id']);

        $this->get('/photo_crop/photocrops/removePhotoCrop/1');
        $query = $this->Photocrops->find()
                ->where(['name' => 'profile_55156r43jf779.png'])
                ->select('id')
                ->first();
        $this->assertResponseOk();
        $this->assertEquals(null, $query['id']);
    }
}
