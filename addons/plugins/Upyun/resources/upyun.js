var UPyun = {
  imageup: function(id) {ETConversation.wrapText($("#"+id+" textarea"), "[img]", "[/img]", "", "http://example.com/image.jpg");},
};
void function () {

//if ( typeof BBCode === 'undefined' || typeof FormData === 'undefined' ) return

function getSign() {
  return $.getJSON( '/upyun/signature' )
  .fail(function () {
    ETMessages.showMessage('无法进行上传验证', 'warning')
  })
}


// click upload
var $file = $('<input type="file" multiple accept="multiple">')
var $target = $()
$file.on('change', function(evt) {
  $.each(this.files, function(i, file) {
    upload(file, $target)
  });
  this.value = ''
})
UPyun.imageup = function (id) {
  $target = $('#' + id + ' textarea')
  $file.trigger('click')
}

// drag upload
$(document)
.on('drop', '#reply', function(evt) {
  evt.preventDefault()
  evt.stopPropagation()

  $(this).trigger('change') // active textarea
  $('> .postContent', this).removeClass('upyun-drag-highlight')
  var $target = $('textarea', this)
  $.each(evt.originalEvent.dataTransfer.files, function(i, file) {
    return upload(file, $target)
  })
})
.on('dragover', function(evt) {
  evt.preventDefault()
  evt.originalEvent.dataTransfer.dropEffect = 'none'
})
.on('enter dragover', '#reply', function(evt) {
  evt.preventDefault()
  evt.stopPropagation()
  evt.originalEvent.dataTransfer.dropEffect = 'copy'
  $('> .postContent', this).addClass('upyun-drag-highlight')
})
.on('dragleave', '#reply', function(evt) {
  $('> .postContent', this).removeClass('upyun-drag-highlight')
})

// clipboard upload
$(document).on('paste', '#reply', function (evt) {
  var data = evt.originalEvent.clipboardData
  if (typeof data !== 'object') return

  var $target = $('textarea', this)
  $.each(data.items, function(i, item) {
    if (item.kind === 'file') {
      var file = item.getAsFile()
      file.name = file.name || 'screenshot.png'
      return upload(file, $target)
    }
  })
})


function upload(file, $target) {
  // TODO queue upload
  var deferred = getSign()
  .then(function (sign) {
    var data = new FormData
    data.append('file', file, file.name)
    data.append('policy', sign.policy)
    data.append('signature', sign.signature)

    return $.ajax('//v0.api.upyun.com/' + sign.bucket, {
      data: data,
      type: 'POST',
      processData: false,
      contentType: false,
      dataType: 'json',
      timeout: 1000 * 60 * 15 // 15 minutes
    })
    .fail(function ($xhr, status, error) {
      if (status === 'timeout') {
        // TODO i18n
        ETMessages.showMessage('上传超时', 'warning')
      } else {
        ETMessages.showMessage('上传失败/文件太大', 'warning')
      }
    })
    .then(function (res) {
      return $.Deferred().resolve(res, sign.bucket)
    })
  })


  // ==== insert content

  var placeholder = '上传中 ' + file.name 

  var pos = $target.getSelection()
  var result = ''
  ETConversation.wrapText($target, placeholder, '', '', '')
  deferred
  .done(function (res, bucket) {
    var w = res['image-width']
    var h = res['image-height']
			if (w <= 500 && h <= 500) {
				result = '[upyun]//' + bucket + '.b0.upaiyun.com' + res.url + '[/upyun]'
      } else if (w == undefined) {
        result = '[upyun]//' + bucket + '.b0.upaiyun.com' + res.url + '[/upyun]'
      } else {
				result = '[url=//' + bucket + '.b0.upaiyun.com' + res.url + ']' + '[img]//' + bucket + '.b0.upaiyun.com' + res.url + '!s[/img]' + '[i]' + w + 'x' + h + '[/i]' + '[/url]'
			}
		})
  .always(function () {
    var cpos = $target.getSelection()
    $target.val($target.val().replace(placeholder, result))
    var x = cpos.end
    if ( cpos.end >= (pos.end + placeholder.length) ) {
      x = cpos.end + result.length - placeholder.length
    } else if (cpos.end > pos.end) {
      x = pos.end
    }
    // restore caret position
    $target.selectRange(x, x)
  })
}

}();
