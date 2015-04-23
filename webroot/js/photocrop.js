function initPhotocropVars(type) 
{
    console.log('jsPhotoCropVars', jsPhotoCropVars);
    if (typeof jsPhotoCropVars !== 'undefined' && !jQuery.isEmptyObject(jsPhotoCropVars)) {
        for(var prop in jsPhotoCropVars[type]) {
            console.log(prop, jsPhotoCropVars[type][prop]);
        }
        return jsPhotoCropVars[type];
    }
    return false;
}

function loadAndCrop(input) 
{
    if (input.files && input.files[0]) {
        if (!window.FileReader) {
            alert("The file API isn't supported on this browser yet.");
            return;
        }

        var file = input.dataTransfer !== undefined ? input.dataTransfer.files[0] : input.files[0];
        var reader = new FileReader();
        reader.onload = (function (theFile) {
            return function (e) {
                // Vérifier que le fichier proposé soit bien une image
                var match = e.target.result.match(/^data:image\/(.+);/);
                if (match === null || $.inArray(match[1], ["jpeg", "jpg", "png", "gif"]) === -1) {
                    alert("Le fichier doit être une image valide.");
                    clearFileInput(input); // Empty the input field
                    return false;
                }

                // get photocrop-type attribute from input to set the config vars
                var photocropType = (input.getAttribute("photocrop-type") && typeof jsPhotoCropVars[input.getAttribute("photocrop-type")] !== 'undefined') 
                    ? input.getAttribute("photocrop-type") 
                    : 'default';

                // getting all config values
                var photocropImg = initPhotocropVars(photocropType);
                if(!photocropImg) {
                    alert("Erreur d'initialisation");
                    clearFileInput(input); // Empty the input field
                    return false;
                };

                // Checking constraints with the size of the file
                console.log('file size in bytes is', file.size);
                if (photocropImg.maxFileSizeAllowed !== 'undefined'
                        && file.size > photocropImg.maxFileSizeAllowed * (1024 * 1024)) {
                            alert("The max file size allowed is " + photocropImg.maxFileSizeAllowed + "MB");
                            clearFileInput(input); // Empty the input field
                            return false;
                        }



                var inputId = input.id.split("_")[1];
                console.info('input id', inputId);
                var imgWrapper = document.createElement('div');
                imgWrapper.id = 'imgWrapper_' + inputId;

                var img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'imgToCrop';

                // TODO: WE CAN CHECK THE SIZE AND THE WIDTH FOR THOSE PICTURES THAT ARE TOO SMALL AND TOO BIG (SO LOW QUALITY)
                console.log('Image Width', file.naturalWidth);

                $('#photocrop__preview').children().remove();
                $('#photocrop__preview').append(imgWrapper).append(img);
                console.log('photocrop__preview created ready to crop!');                                

                var bounds = {};

                var numImagesCropped = 0;
                var maxNumImagesAllowed = (typeof photocropImg.maxNumImagesAllowed !== 'undefined') ? photocropImg.maxNumImagesAllowed : false;

                $(".imgCropped").each( function() { numImagesCropped++; });

                //on edit form change maxNumImagesAllowed according to existing images
                if($("#photocropGallerie img").length > 0) {
                    if(maxNumImagesAllowed > 0) {
                        maxNumImagesAllowed = maxNumImagesAllowed - $("#photocropGallerie > div > img").length;
                    }
                }

                //check if it didn't reach the maxNumImagesAllowed
                if(maxNumImagesAllowed === false || numImagesCropped < maxNumImagesAllowed) {
                    //JCrop pluggin to cut the image
                    $(img).Jcrop({
                        bgOpacity: photocropImg.bgOpacity,
                        trackDocument: (typeof photocropImg.trackDocument !== 'undefined') ? photocropImg.trackDocument : true,
                        allowResize: (typeof photocropImg.allowResize !== 'undefined') ? photocropImg.allowResize : false,
                        allowSelect: (typeof photocropImg.allowSelect !== 'undefined') ? photocropImg.allowSelect : true,
                        bgFade: (typeof photocropImg.bgFade !== 'undefined') ? photocropImg.bgFade : true,
                        setSelect: (typeof photocropImg.setSelect !== 'undefined') ? photocropImg.setSelect : false,
                        minSize: (typeof photocropImg.selMinSize !== 'undefined') ? photocropImg.selMinSize : 5,
                        boxWidth: (typeof photocropImg.maxWidthPreview !== 'undefined') ? photocropImg.maxWidthPreview : 700,
                        boxHeiht: (typeof photocropImg.maxHeightPreview !== 'undefined') ? photocropImg.maxHeightPreview : 700,
                        aspectRatio: (typeof photocropImg.aspectRatio !== 'undefined') ? photocropImg.aspectRatio : 1,
                        onSelect: function (cords) {
                            cords.pWidth = img.naturalWidth / bounds.x;
                            cords.pHeight = img.naturalHeight / bounds.y;
                            console.log('jCrop coords x, y, x2, y2, w, h', cords.x, cords.y, cords.x2, cords.y2, cords.w, cords.h);

                            //setting up the canvas to draw the cropped image
                            var canvas = document.createElement("canvas"),
                            context = canvas.getContext('2d');

                            photocropImg.aspectRatio = (typeof photocropImg.aspectRatio !== 'undefined') ? photocropImg.aspectRatio : 1;
                            canvas.width = (typeof photocropImg.cropImageWidth !== 'undefined') 
                                ? photocropImg.cropImageWidth 
                                : photocropImg.cropImageHeight*photocropImg.aspectRatio;
                            canvas.height = (typeof photocropImg.cropImageHeight !== 'undefined') 
                                ? photocropImg.cropImageHeight 
                                : photocropImg.cropImageWidth/photocropImg.aspectRatio;
                            console.log('cords.w * cords.pWidth', Math.round(cords.w * cords.pWidth));
                            console.log('cords.h * cords.pHeight', Math.round(cords.h * cords.pHeight));
                            console.log('canvas.width', Math.round(canvas.width));
                            console.log('canvas.height', Math.round(canvas.height));
                            context.drawImage(
                                img, 
                                Math.round(cords.x * cords.pWidth), 
                                Math.round(cords.y * cords.pHeight), 
                                Math.round(cords.w * cords.pWidth), 
                                Math.round(cords.h * cords.pHeight), 
                                0, 
                                0, 
                                Math.round(canvas.width), 
                                Math.round(canvas.height)
                            );

                            clearFileInput(input);                        

                            //onSelect gets called before selection and cords are filled with NaN
                            if (!isNaN(cords.x)) {  
                                if($('#imgPhotocropWrapper_' + inputId).length) {
                                    //add image to form if exists
                                    $('#imgPhotocropWrapper_' + inputId).find('img').attr('src', canvas.toDataURL());
                                } else {
                                    // create display image wrapper
                                    var imgPhotocropWrapper = document.createElement('div');
                                    imgPhotocropWrapper.id = 'imgPhotocropWrapper_' + inputId;
                                    imgPhotocropWrapper.className = 'gallerie__item';

                                    // create display image
                                    var imgPhotocrop = document.createElement('img');
                                    imgPhotocrop.src = canvas.toDataURL();
                                    imgPhotocrop.className = 'imgCropped gallerie__media';

                                    // create a remove display image link
                                    var removePhotocropWrapper = document.createElement('a');
                                    var removeInputText = '<i class="fa fa-trash"></i>';
                                    removePhotocropWrapper.innerHTML = removeInputText;
                                    removePhotocropWrapper.setAttribute('class', 'photocrop_input_remove gallerie__link');
                                    removePhotocropWrapper.setAttribute('onClick', 'removePhotocrop('+inputId+')');

                                    // display image
                                    $('#photocrop__selected').append(imgPhotocropWrapper);
                                    $('#imgPhotocropWrapper_' + inputId).append(imgPhotocrop); 
                                    $('#imgPhotocropWrapper_' + inputId).append(removePhotocropWrapper); 
                                }

                                // check first that the hidden form element is not created first!!
                                if($('#hiddenInputCrop_' + inputId).length) {
                                    $('#hiddenInputCrop_' + inputId).attr('value', function() { 
                                        return canvas.toDataURL("image/jpeg", photocropImg.qualityJpeg); 
                                    });
                                } else {
                                    var resultInput = document.createElement('input');
                                    resultInput.setAttribute('type', 'hidden');
                                    resultInput.setAttribute('value', canvas.toDataURL());
                                    resultInput.setAttribute('id', 'hiddenInputCrop_' + inputId);
                                    resultInput.setAttribute('name', 'photocrops[' + photocropImg.type + '_' + inputId + ']');
                                    input.form.appendChild(resultInput);
                                }

                                // change the id of the input to have it ready for the next photo
                                addNewInput(input, photocropImg);

                                // update slider height 
                                // (specified in height attribute, need to be updated since we have more elements in the DOM that must increase the height)
                                // Function from form_slider.js
                                //updateCurrentSlide();
                            }
                        }
                    }, function () {
                        // Use the API to get the real image size  
                        var boundsArr = this.getBounds();
                        bounds.x = boundsArr[0];
                        bounds.y = boundsArr[1];// Open modal preview

                        // Show the preview div after the resize of JCrop
                        $('.modal').show();
                    });
                } else {
                    alert("Vous avez atteint la limite d'images possibles pour ce formulaire");
                    return false;
                }     
            };
        })(file);
        reader.readAsDataURL(file);
    } else {
        alert("This browser doesn't seem to support the 'files' property of file inputs.");
        clearFileInput(input); // Empty the input field
        return false;
    }
}

/**
 * method activated by the remove link after each selected photocrop it removes the image and the hidden input field
 * 
 * @param string inputId
 * @returns void
 */
function removePhotocrop(inputId) 
{
    $("#imgPhotocropWrapper_" + inputId).fadeOut('slow');
    $("#hiddenInputCrop_" + inputId).remove();
}

/**
 * method activated by the remove link after each saved photocrop it removes the image
 * 
 * @param string key, photoCropId
 * @returns void
 */
function removeSavedPhotocrop(key, photoCropId) 
{
    $.ajax({
        async:true,
        cache: false,
        type:'post',
        url:'/photo_crop/photocrops/removePhotoCrop/' + photoCropId,
        beforeSend: function () {
            r = window.confirm('Êtes-vous sûr de vouloir supprimer cette image?');
            if (r === false) { return false; }
        }
    }).done(function(data) {
        $('.photocrop__item_' + key).fadeOut('slow').promise().done(function() {
            $(this).remove(); 
        });
    }).error(function(jqXHR, textStatus, errorThrown) {
        console.log('jqXHR', jqXHR);
        console.log('textStatus', textStatus);
        console.log('errorThrown', errorThrown);
        if(errorThrown !== 'canceled') {
            alert('Impossible de supprimer cette image.');
        }
    });
}

/**
 * After cropping one image clears the input field and sets a new input with new id
 * 
 * @param {object} input
 * @returns void
 */
function addNewInput(input) 
{
    var inputId = Number(input.id.split("_")[1]);

    clearFileInput(input);
    input.setAttribute('id', 'photocropInput_' + (inputId+1).toString());
    input.setAttribute('onChange', 'loadAndCrop(this);');   
    input.setAttribute('placeholder', 'Ajouter un autre fichier.');
}

/**
 * Clears the file Input
 * 
 * @param {object} input
 * @returns void
 */
function clearFileInput(input) 
{
    try {
        input.value = null;
    } catch (ex) { }

    if (input.value) {
        input.parentNode.replaceChild(input.cloneNode(true), input);
    }
}

$(document).ready(function () {

    // reloads the photocrop input fields that have no hidden field linked with
    $(".form__input--photocrop").each( function() {
        if(!$("#hiddenInputCrop_"+this.id.split("_")[1]).length) {
            clearFileInput(this);
        }
    });            
});

