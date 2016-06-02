jQuery(function ($) {
    $("[href='#al_uploader_anchor']").on('click',function () {
        var elem = $(this);
        var img_wrap = $('.al_uploader-img-wrap');
        var input = img_wrap.parent().find('input');

        wp.media.editor.send.attachment = function (info, file) {
            var id = file.id;
            var url = file.url;
            img_wrap.find('img').remove();
            img_wrap.append("<img src='" + url + "' style='display: block; width:100%; height: auto' >");
            input.attr('value', id);
            elem.text(elem.attr('data-text'));
        };

        wp.media.editor.open(this);
        return false;
    });

});