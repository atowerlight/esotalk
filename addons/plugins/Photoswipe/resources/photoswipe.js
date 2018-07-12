!function () {

var pswpTpl = '\
<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">\
  <div class="pswp__bg"></div>\
  <div class="pswp__scroll-wrap">\
    <div class="pswp__container">\
      <div class="pswp__item"></div>\
      <div class="pswp__item"></div>\
      <div class="pswp__item"></div>\
    </div>\
    <div class="pswp__ui pswp__ui--hidden">\
      <div class="pswp__top-bar">\
        <div class="pswp__counter"></div>\
        <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>\
        <button class="pswp__button pswp__button--share" title="Share"></button>\
        <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>\
        <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>\
        <div class="pswp__preloader">\
          <div class="pswp__preloader__icn">\
            <div class="pswp__preloader__cut">\
            <div class="pswp__preloader__donut"></div>\
            </div>\
          </div>\
        </div>\
      </div>\
      <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">\
        <div class="pswp__share-tooltip"></div> \
      </div>\
      <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">\
      </button>\
      <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">\
      </button>\
      <div class="pswp__caption">\
        <div class="pswp__caption__center"></div>\
      </div>\
    </div>\
  </div>\
</div>\
'

var _initPost = ETConversation.initPost
ETConversation.initPost = function ($posts) {
  _initPost.apply(this, arguments)

  $posts.each(function () {
    listen($(this))
  })
}

var _togglePreview = ETConversation.togglePreview
ETConversation.togglePreview = function (id, preview) {
  var r = _togglePreview.apply(this, arguments)
  if (preview) {
    r.done(function () {
      listen($('#' + id + '-preview'))
    })
  }
}


function listen($target) {

  var _pswpElement
  function getPswpElement() {
    if (_pswpElement) return _pswpElement
    return _pswpElement = $(pswpTpl).appendTo('body').get(0)
  }

  var $items = $target.find('a > img + i')
    .filter(function () {
      return /\d+x\d+/.test($.text(this))
    })
    .map(function (i) {
      var item = this.parentNode
      $.attr(item, 'data-photoswipe-index', i)
      return item
    })

  if ($items.length < 1) return

  var items = $.map($items, function (item) {
    var $item = $(item)
    var size = $item.find('>img+i').text().split('x')
    return {
      src: $item.attr('href'),
      msrc: $item.find('>img').attr('src'),
      w: size[0],
      h: size[1]
    }
  })

  $target.on('click', 'a[data-photoswipe-index]', function (evt) {
    new PhotoSwipe( getPswpElement(), PhotoSwipeUI_Default, items, {
      index: parseInt($.attr(this, 'data-photoswipe-index')),
      shareEl: false,
      history: false,
      getThumbBoundsFn: function (index) {
        var $img = $items.eq(index).find('>img')
        var offset = $img.offset()
        return {
          x: offset.left,
          y: offset.top,
          w: $img.width()
        }
      }
    }).init()
    evt.preventDefault()
  })
}


}();
