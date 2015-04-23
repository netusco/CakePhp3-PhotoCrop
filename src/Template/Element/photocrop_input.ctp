<!-- PHOTOCROP GLOBAL CONFIG VARS -->
<?php
    if(isset($jsPhotoCropVars)):
        echo $this->Html->scriptBlock('var jsPhotoCropVars = ' . json_encode($jsPhotoCropVars) . ';');
    endif; 
?>

<!-- PHOTOCROP INPUT -->
<div class="input file">
    <?php if(isset ($data['inputPhotocropLabel'])): ?>
    <label <?php if(isset($data['inputPhotocropLabelClass'])) { echo 'class="'.$data['inputPhotocropLabelClass'].'"'; } ?>>
        <?= $data['inputPhotocropLabel']; ?>
    </label>
    <?php endif; ?>
    <?php
        echo $this->Form->file('Photocrop.name', [
            'type' => 'file',
            'class' => (isset($data['inputPhotocropClass'])) ? $data['inputPhotocropClass'] : 'form__input--photocrop',
            'photocrop-type' => (isset($data['photocropType'])) ? $data['photocropType'] : key($jsPhotoCropVars),
            'id' => 'photocropInput_0',
            'onChange' => "loadAndCrop(this); $('.photoCropModal').show();"
        ]);
    ?>
</div>


<!-- PHOTOCROP CROP SELECTED -->
<?php //echo $this->start('modals'); ?>
<div class="modal" style="display:none">
    <div class="modal__content">
        <h3 class="modal__title">Rognez votre image</h3>
        <div id="photocrop__preview" class="photocrop__preview"></div>
        <button onClick="javascript:$('.modal').hide();return false;" class="button">Valider</button>
    </div>
</div>
<?php //echo $this->end(); ?>

<div id="photocrop__selected" class="gallerie gallerie--queue"></div>


<!-- PHOTOCROP SAVED CROPS -->
<?php if(isset($data['displaySavedPhotocrops']) && $data['displaySavedPhotocrops']): ?>
    <div id="photocropGallerie" class="gallerie">

    <?php
    if(!empty($data['entity']['photocrops'])):
        foreach ($data['entity']['photocrops'] as $key => $photocrop): ?>
            <div class="gallerie__item photocrop__item_<?= $key ?>">
                <?php
                $path = DS . 'photocrops' . DS . $photocrop->type . DS;
                echo $this->Html->image($path . $photocrop->name, array(
                    'class' => 'gallerie__media'
                ));
                echo $this->Html->link('<i class="fa fa-trash"></i>', 'javascript:void(0);', [ 
                    'escape' => false,
                    'class' => 'gallerie__link',
                    'onClick' => "removeSavedPhotocrop(" . $key . "," . $photocrop['id'] . ")" 
                ]); ?>
            </div>
            <?php
        endforeach;
    endif;
    ?>
    </div>
<?php endif; ?>


<?php
    echo $this->Html->css("//jcrop-cdn.tapmodo.com/v0.9.12/css/jquery.Jcrop.css", ['block' => true, 'once' => true]);
    echo $this->Html->css("//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css", ['block' => true, 'once' => true]);
    echo $this->Html->script("//code.jquery.com/jquery-1.11.2.min.js", ['block' => true, 'once' => true]);
    echo $this->Html->script("//jcrop-cdn.tapmodo.com/v0.9.12/js/jquery.Jcrop.min.js", ['block' => true, 'once' => true]);
    echo $this->Html->script("PhotoCrop.photocrop.js", ['block' => true, 'once' => true]);
?>
