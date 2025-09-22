import $ from 'jquery';

// Replace SVG image with inline SVG
$('img.imgtosvg').each(function(){
    var $img = $(this);
    var imgID = $img.attr('id');
    var imgClass = $img.attr('class');
    var imgWidth = $img.attr('width');
    var imgHeight = $img.attr('height');
    var imgURL = $img.attr('src');

    $.get(imgURL, function(data) {
        // Get the SVG tag, ignore the rest
        var $svg = $(data).find('svg');

        // Add replaced image's ID to the new SVG
        if(typeof imgID !== 'undefined') {
            $svg = $svg.attr('id', imgID);
        }
        // Add replaced image's classes to the new SVG
        if(typeof imgClass !== 'undefined') {
            $svg = $svg.attr('class', imgClass);
        }

        if(typeof imgWidth !== 'undefined') {
            $svg = $svg.attr('width', imgWidth);
        }

        if(typeof imgHeight !== 'undefined') {
            $svg = $svg.attr('height', imgHeight);
        }

        // Remove any invalid XML tags as per http://validator.w3.org
        $svg = $svg.removeAttr('xmlns:a');

        // Replace image with new SVG
        $img.replaceWith($svg);

    }, 'xml');
});