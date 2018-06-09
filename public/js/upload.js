var isAdvancedUpload = function ()
{
    var div = document.createElement ('div');
    return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
} ();

/**
 * 
 * @returns {calculate.test_L218.testAnonym$5}
 */
function findObjectByKey (array)
{
    var response = null;

    $.each (array, function (index, value)
    {
        if ($.trim (index) === "name")
        {
            response = value;
        }
    });

    return response;
}

var $form = $ (".box"),
        $input = $form.find ('input[type="file"]'),
        $label = $form.find ('.filelist'),
        $errorMsg = $form.find ('.box__error span'),
        $restart = $form.find ('.box__restart'),
        droppedFiles = false,
        showFiles = function (files)
        {
            var name = findObjectByKey (files);

            $label.text (files.length > 1 ? ($input.attr ('data-multiple-caption') || '').replace ('{count}', files.length) : name);

            if (showPreview && thumbnailWidth)
            {
                $.each (files, function (index, file)
                {
                    var reader = new FileReader ();

                    reader.onload = function (e)
                    {
                        var img = $ ('<img />', {
                            src: e.target.result,
                            alt: 'MyAlt'
                        });

                        img.css ({
                            'width': thumbnailWidth,
                            'float': 'left'
                        });

                        img.appendTo ($ ('.FilePreviews'));
                    }

                    reader.readAsDataURL (file);
                });






            }
        };

// letting the server side to know we are going to make an Ajax request
$form.append ('<input type="hidden" name="ajax" value="1" />');

// automatically submit the form on file select
$input.on ('change', function (e)
{
    var files = e.target.files;

    if (maxNoOfFiles && parseInt (files.length) > parseInt (maxNoOfFiles))
    {
        alert ("You can only upload a maximum of " + maxNoOfFiles);
        return false;
    }

    showFiles (files);


});

// drag&drop files if the feature is available
if (isAdvancedUpload && dragEnabled)
{
    $ (".dragBox")
            .addClass ('has-advanced-upload') // letting the CSS part to know drag&drop is supported by the browser
            .on ('drag dragstart dragend dragover dragenter dragleave drop', function (e)
            {
                // preventing the unwanted behaviours
                e.preventDefault ();
                e.stopPropagation ();
            })
            .on ('dragover dragenter', function () //
            {
                $ (".dragBox").addClass ('is-dragover');
            })
            .on ('dragleave dragend drop', function ()
            {
                $ (".dragBox").removeClass ('is-dragover');
            })
            .on ('drop', function (e)
            {
                droppedFiles = e.originalEvent.dataTransfer.files; // the files that were dropped

                if (maxNoOfFiles && parseInt (droppedFiles.length) > parseInt (maxNoOfFiles))
                {
                    alert ("You can only upload a maximum of " + maxNoOfFiles);
                    return false;
                }

                showFiles (droppedFiles);

            });
}

// restart the form if has a state of error/success

$restart.on ('click', function (e)
{
    e.preventDefault ();
    $form.removeClass ('is-error is-success');
    $input.trigger ('click');
});

// Firefox focus bug fix for file input
$input
        .on ('focus', function ()
        {
            $input.addClass ('has-focus');
        })
        .on ('blur', function ()
        {
            $input.removeClass ('has-focus');
        });