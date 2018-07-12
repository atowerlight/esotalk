// reload captcha
void function () {

$(document).off('click.captcha')
.on('click.captcha', '.captcha img:not(.loading)', function () {
  reload($(this))
})

if (typeof ETConversation !== 'undefined') {
  var startConversation = ETConversation.startConversation
  ETConversation.startConversation = function () {
    startConversation.apply(startConversation, arguments)
    .done(function (res) {
      if (typeof res.redirect !== 'undefined') return
      reload($('#reply .captcha img'))
      $('#reply .captcha input').val('')
    })
    .fail(function () {
      reload($('#reply .captcha img'))
      $('#reply .captcha input').val('')
    })
  }

  var addReply = ETConversation.addReply
  ETConversation.addReply = function () {
    addReply.apply(addReply, arguments)
    .always(function () {
      reload($('#reply .captcha img'))
      $('#reply .captcha input').val('')
    })
  }
}


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
