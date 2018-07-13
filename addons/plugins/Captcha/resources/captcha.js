// reload captcha
void function () {

$(document).off('click.captcha')
.on('click.captcha', '.captcha img:not(.loading)', function () {
  reload($(this))
})

function reload($img) {
  var t = $.now()

  $img
  .addClass('loading')
  .one('load error', function () {
    $img.removeClass('loading')
  })
  // remove attr to show loading image
  .removeAttr('src')
  .removeAttr('srcset')
  .attr('src', '/captcha?t=' + t)
  .attr('srcset', '/captcha/2x?t=' + t + ' 2x')
}


// tooltip
$(function () {
  $('.captcha img').tooltip()
})
}();
