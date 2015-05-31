/* ===================================================
 * jqueryimgSmartLoad.js v1.0.0
 * ===================================================
 *
 * Replace all img src placeholder in the $element by the real src on scroll window event
 * Bind a 'smartload' event on each transformed img
 *
 * Note : the data-src attr has to be pre-processed before the actual page load
 * Example of regex to pre-process img server side with php :
 * preg_replace_callback('#<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>#', 'regex_callback' , $_html)
 *
 *
 * Example of gif 1px x 1px placeholder :
 * 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'
 *
 * inspired by the work of Luís Almeida
 * http://luis-almeida.github.com/unveil
 *
 * =================================================== */
;(function ( $, window, document, undefined ) {
  //defaults
  var pluginName = 'imgSmartLoad',
      defaults = {
        load_all_images_on_first_scroll : false,
        attribute : 'data-src',
        threshold : 200,
        fadeIn_options : { duration : 400 },
        delaySmartLoadEvent : 0
      };


  function Plugin( element, options ) {
    this.element = element;
    this.options = $.extend( {}, defaults, options) ;
    this._defaults = defaults;
    this._name = pluginName;
    this.init();
  }


  //can access this.element and this.option
  Plugin.prototype.init = function () {
    var self        = this,
        $_imgs   = $( 'img[' + this.options.attribute + ']' , this.element );
    this.increment  = 1;//used to wait a little bit after the first user scroll actions to trigger the timer
    this.timer      = 0;

    //attach action to the load event
    $_imgs.bind( 'load_img', {}, function() { self._load_img(this); });

    $(window).scroll( function( _evt ) { self._better_scroll_event_handler( $_imgs, _evt ); });
    $(window).resize( function( _evt ) { self._maybe_trigger_load( $_imgs, _evt ); });
    //on load
    this._maybe_trigger_load( $_imgs );
  };


  /*
  * @param : array of $img
  * @param : current event
  * @return : void
  * scroll event performance enhancer => avoid browser stack if too much scrolls
  */
  Plugin.prototype._better_scroll_event_handler = function( $_imgs , _evt ) {
    var self = this;
    //use a timer
    if ( 0 !== this.timer ) {
        this.increment++;
        window.clearTimeout( this.timer );
    }

    this.timer = window.setTimeout(function() {
      self._maybe_trigger_load( $_imgs , _evt );
    }, self.increment > 5 ? 50 : 0 );
  };


  /*
  * @param : array of $img
  * @param : current event
  * @return : void
  */
  Plugin.prototype._maybe_trigger_load = function( $_imgs , _evt ) {
    var self = this;
        //get the visible images list
        _visible_list = $_imgs.filter( function( ind, _img ) { return self._is_visible( _img ,  _evt ); } );
    //trigger load_img event for visible images
    _visible_list.map( function( ind, _img ) { $(_img).trigger( 'load_img' );  } );
  };


  /*
  * @param single $img object
  * @param : current event
  * @return bool
  * helper to check if an image is the visible ( viewport + custom option threshold)
  */
  Plugin.prototype._is_visible = function( _img, _evt ) {
    var $_img       = $(_img),
        wt = $(window).scrollTop(),
        wb = wt + $(window).height(),
        it  = $_img.offset().top,
        ib  = it + $_img.height(),
        th = this.options.threshold;

    //force all images to visible if first scroll option enabled
    if ( _evt && 'scroll' == _evt.type && this.options.load_all_images_on_first_scroll )
      return true;

    return ib >= wt - th && it <= wb + th;
  };


  /*
  * @param single $img object
  * @return void
  * replace src place holder by data-src attr val which should include the real src
  */
  Plugin.prototype._load_img = function( _img ) {
    var $_img = $(_img),
        _src  = $_img.attr( this.options.attribute ),
        self = this;

    $_img.parent().addClass('smart-loading');

    $_img.unbind('load_img')
    .hide()
    .removeAttr( this.options.attribute )
    .attr('src' , _src )
    .load( function () {
      $_img.fadeIn(self.options.fadeIn_options).addClass('tc-smart-loaded').trigger('smartload');
    });//<= create a load() fn
    //http://stackoverflow.com/questions/1948672/how-to-tell-if-an-image-is-loaded-or-cached-in-jquery
    if ( $_img[0].complete )
      $_img.load();
    $_img.parent().removeClass('smart-loading');
  };


  // prevents against multiple instantiations
  $.fn[pluginName] = function ( options ) {
    return this.each(function () {
        if (!$.data(this, 'plugin_' + pluginName)) {
            $.data(this, 'plugin_' + pluginName,
            new Plugin( this, options ));
        }
    });
  };
})( jQuery, window, document );