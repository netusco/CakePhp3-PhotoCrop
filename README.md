# PhotoCrop plugin for CakePHP

## Requirements

* CakePHP 3.0.0 or greater
* PHP 5.4.16 or greater
* JCrop (jquery plugin v0.9.12) [Github](https://github.com/tapmodo/Jcrop/tree/master/js)

## Copyright and license

This software is registered under the MIT license. Copyright(c) 2015 - Ernest Conill

## Installation

* Clone this plugin from [Github](https://github.com/netusco/CakePhp-PhotoCrop.git)
You will need a **PhotoCrop** directory in your plugins folder so from your app root directory you can use:

```sh
git clone https://github.com/netusco/CakePhp-PhotoCrop.git plugins/PhotoCrop/
```

* This plugin uses JCrop jquery plugin. You can update it from it's [Github source](https://github.com/tapmodo/Jcrop/tree/master/js)

* Load the plugin

```php
Plugin::load('PhotoCrop', ['bootstrap' => false, 'routes' => true]);
```

### Reporting Issues

If you have a problem with this plugin please open an issue.

### Contributing

I'm not actively maintaining this plugin, but it's open for community contributions.

# Documentation

### Database
In your database you need to add the following sql schema found also in config/schema. 
Remember to add your foreign key to relate to models and index them. 

```sql
CREATE TABLE IF NOT EXISTS `photocrops` (
    `id` int(11) NOT NULL,
    `name` varchar(255) DEFAULT NULL,
    `type` varchar(45) DEFAULT NULL,
    `mime` varchar(25) DEFAULT NULL,
    `width` smallint(6) DEFAULT NULL,
    `height` smallint(6) DEFAULT NULL,
    `bits` mediumint(9) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `photocrops`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
```

### Controller
* In your controller you need to load the component with the configurations required for each type of photo crop.
If no configuration is given or for each missing configuration element the one used is the default.

```php
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('PhotoCrop.Photocrop', [
            'profile' => [
                'maxNumImagesAllowed' => 3,
                'maxWidthPreview' => 500, //pxs
            ],
            'cover' => [
                'maxNumImagesAllowed' => 1, 
                'aspectRatio' => 4/3,
                'maxWidthPreview' => 750, //pxs
            ]
        ]);
    }
```

default config:

```php
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
```

* Then within the action method add the contain when you get the entity. 
(Only if you would like to display the saved photocrops).

```php
    $entity = $this->Users->get($this->request->session()->read('Auth.User.id'), ['contain' => ['Photocrops']]);    
```

* To add the photocrop info and patch the resulting entity add this line right before the `save()` call.

```php
    // $entity is patched within the Component Photocrop
    $entity = $this->Photocrop->preparePhotocropsAndPatchEntity($entity);
```

* Now you are ready to save this entity (no need to Patch it as it's already done).


### Model
After adding the database you need to set up the relations with those models that use Photocrops. 
(Only needed if we want to add a dependent relation)

        $this->hasMany('PhotoCrop.Photocrops', ['dependent' => true]);


### View
* In your view you do a call to the plugin element giving the data needed to adjust the required specifications (see below).
It loads font-awesome v4.3, Jquery v1.11.2, JCrop v0.9.12 if not loaded already

```php
        echo $this->element('PhotoCrop.photocrop_input', [
            'data' => [
                'photocrop-type' => 'profile', //if there is only one type defined this can be ommited
                'entity' => $entity, //to be added only if there is the need to display stored photocrops of this entity 
                'inputPhotocropLabel' => 'Ajouter une photo', //if ommited there will be no label
                'inputPhotocropLabelClass' => 'photocrop_label', //only needed if there is label
                'inputPhotocropClass' => 'form__input--photocrop', //this is the class by default
            ]
        ]);
```

To display the images look for them within `webroot/photocrops/{$entity->type}/{$entity->name}` (remove 'webroot' using `$this->Html->image`).


### Tests
Adding PhotoCrops plugin might mean that you need to modify a bit your test cases. You can easily point to the Photocrops fixtures file within the plugin.

```php
public $fixtures = [
    'Photocrops' => 'plugin.PhotoCrop.photocrops'
];
```

To pass the tests within the PhotoCrop plugin use the following:

```sh
vendor/bin/phpunit plugins/PhotoCrop/tests/TestCase/Controller/PhotocropsControllerTest.php
```
