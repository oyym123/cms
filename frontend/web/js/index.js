$('.js_login1').click(function () {
    $('.js_mask_login').fadeIn();
});
$('.close').click(function (e) {
    $('.js_mask_login').fadeOut();
});
$('.js_res').click(function(){
    $('.resbx').fadeIn();
    $('.logbx').hide();
})
$('.js_login').click(function () {
    $('.logbx').fadeIn();
    $('.resbx').hide();
});
