$(function() {
    if ($("p.likes").length) {
        $("p.likes").css({'border': '0'});
        $('p.likes').each(
            function(){
                $(this).insertBefore($(this).closest('.postFooter'));
            });
    }
});

$(document).ajaxComplete(function(){
    if ($("p.likes").length) {
        $("p.likes").css({'border': '0'});
        $('p.likes').each(
            function(){
                $(this).insertBefore($(this).closest('.postFooter'));
            });
    }
})
