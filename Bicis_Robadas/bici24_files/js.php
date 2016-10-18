$(document).ready(function () {
	// toggle customise edit panel
	$('a.toggle_customise_edit_panel').click(function () {
		$('div#customise_editpanel').slideToggle("fast");
		return false;
	});

	// toggle plugin's settings nad more info on admin tools admin
	$('a.pluginsettings_link').click(function () {
		$(this.parentNode.parentNode).children(".pluginsettings").slideToggle("fast");
		return false;
	});
	$('a.manifest_details').click(function () {
		$(this.parentNode.parentNode).children(".manifest_file").slideToggle("fast");
		return false;
	});
	// reusable generic hidden panel
	$('a.collapsibleboxlink').click(function () {
		$(this.parentNode.parentNode).children(".collapsible_box").slideToggle("fast");
		return false;
	});

	// Setting user local time
	var server_offset = -120;
	var local_offset = (new Date()).getTimezoneOffset();
	if (server_offset != local_offset) {
		$('abbr[data-utime]').each(function() {
			var gmt = new Date($(this).attr('data-utime')*1000);
			var offset = ' UTC'+(local_offset>=0?'-':'+')+Math.abs(local_offset/60)
			var local_datetime = gmt.toLocaleDateString()+' @ '+gmt.getHours()+':'+(gmt.getMinutes()<10?'0':'')+gmt.getMinutes()+offset;
			$(this).attr('title', local_datetime);
		});
	}

	// Dynamic time
	if ($('abbr.dynamictime').length>0) {
		(function rc_doDynamicTimeInfo() {
			setTimeout(function() {
				updateTimeAbbrs();
				rc_doDynamicTimeInfo();
			}, 20000);
		})();
	}
});

function updateTimeAbbrs() {
	var actual = Math.round((new Date()).getTime() / 1000);
	var minute = 60;
	var hour = 60*60;
	var day = 24*60*60;
	$('abbr.dynamictime').each(function() {
		var diff = actual - $(this).attr('data-utime');
		if (diff < minute) {
			$(this).html('en este momento');
		} else if (diff < hour) {
			diff = Math.round(diff / minute);
			if (diff == 0) {
				diff = 1;
			}
			if (diff > 1) {
				changeTimeAbbrs($(this),'hace '+diff+' minutos');
			} else {
				changeTimeAbbrs($(this),'hace 1 minuto');
			}
		} else if (diff < day) {
			diff = Math.round(diff / hour);
			if (diff == 0) {
				diff = 1;
			}
			if (diff > 1) {
				changeTimeAbbrs($(this),'hace '+diff+' horas');
			} else {
				changeTimeAbbrs($(this),'hace 1 hora');
			}
		} else if (diff >= day ) {
			changeTimeAbbrs($(this),'hace 1 día');
		}
	});
};

function changeTimeAbbrs(f,text) {
	if ($(f).text()!=text) {
		$(f).fadeOut('fast',function() {
			$(this).html(text).fadeIn();
		});
	}
}

// List active widgets for each page column
function outputWidgetList(forElement) {
	return( $("input[name='handler'], input[name='guid']", forElement ).makeDelimitedList("value") );
}

// Make delimited list
jQuery.fn.makeDelimitedList = function(elementAttribute) {

	var delimitedListArray = new Array();
	var listDelimiter = "::";

	// Loop over each element in the stack and add the elementAttribute to the array
	this.each(function(e) {
			var listElement = $(this);
			// Add the attribute value to our values array
			delimitedListArray[delimitedListArray.length] = listElement.attr(elementAttribute);
		}
	);

	// Return value list by joining the array
	return(delimitedListArray.join(listDelimiter));
}


// Read each widgets collapsed/expanded state from cookie and apply
function widget_state(forWidget) {

	var thisWidgetState = $.cookie(forWidget);

	if (thisWidgetState == 'collapsed') {
		forWidget = "#" + forWidget;
		$(forWidget).find("div.collapsable_box_content").hide();
		$(forWidget).find("a.toggle_box_contents").html('+');
		$(forWidget).find("a.toggle_box_edit_panel").fadeOut('medium');
	};
}


// Toggle widgets contents and save to a cookie
var toggleContent = function(e) {
var targetContent = $('div.collapsable_box_content', this.parentNode.parentNode);
	if (targetContent.css('display') == 'none') {
		targetContent.slideDown(400);
		$(this).html('-');
		$(this.parentNode).children(".toggle_box_edit_panel").fadeIn('medium');

		// set cookie for widget panel open-state
		var thisWidgetName = $(this.parentNode.parentNode.parentNode).attr('id');
		$.cookie(thisWidgetName, 'expanded', { expires: 365 });

	} else {
		targetContent.slideUp(400);
		$(this).html('+');
		$(this.parentNode).children(".toggle_box_edit_panel").fadeOut('medium');
		// make sure edit pane is closed
		$(this.parentNode.parentNode).children(".collapsable_box_editpanel").hide();

		// set cookie for widget panel closed-state
		var thisWidgetName = $(this.parentNode.parentNode.parentNode).attr('id');
		$.cookie(thisWidgetName, 'collapsed', { expires: 365 });
	}
	return false;
};

// More info tooltip in widget gallery edit panel
function widget_moreinfo() {

	$("img.more_info").hover(function(e) {
	var widgetdescription = $("input[name='description']", this.parentNode.parentNode.parentNode ).attr('value');
	$("body").append("<p id='widget_moreinfo'><b>"+ widgetdescription +" </b></p>");

		if (e.pageX < 900) {
			$("#widget_moreinfo")
				.css("top",(e.pageY + 10) + "px")
				.css("left",(e.pageX + 10) + "px")
				.fadeIn("medium");
		}
		else {
			$("#widget_moreinfo")
				.css("top",(e.pageY + 10) + "px")
				.css("left",(e.pageX - 210) + "px")
				.fadeIn("medium");
		}
	},
	function() {
		$("#widget_moreinfo").remove();
	});

	$("img.more_info").mousemove(function(e) {
		// action on mousemove
	});
};

// COOKIES
jQuery.cookie = function(name, value, options) {
	if (typeof value != 'undefined') { // name and value given, set cookie
	options = options || {};
		if (value === null) {
			value = '';
			options.expires = -1;
		}
	var expires = '';
	if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
		var date;
		if (typeof options.expires == 'number') {
			date = new Date();
			date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
		} else {
			date = options.expires;
		}
		expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
	}
	// CAUTION: Needed to parenthesize options.path and options.domain
	// in the following expressions, otherwise they evaluate to undefined
	// in the packed version for some reason.
	var path = options.path ? '; path=' + (options.path) : '';
	var domain = options.domain ? '; domain=' + (options.domain) : '';
	var secure = options.secure ? '; secure' : '';
	document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');

	} else { // only name given, get cookie
		var cookieValue = null;
		if (document.cookie && document.cookie != '') {
			var cookies = document.cookie.split(';');
			for (var i = 0; i < cookies.length; i++) {
				var cookie = jQuery.trim(cookies[i]);
				// Does this cookie string begin with the name we want?
				if (cookie.substring(0, name.length + 1) == (name + '=')) {
					cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
					break;
				}
			}
		}
		return cookieValue;
	}
};

// ELGG TOOLBAR MENU
$.fn.elgg_topbardropdownmenu = function(options) {

options = $.extend({speed: 350}, options || {});

this.each(function() {

	var root = this, zIndex = 5000;

	function getSubnav(ele) {
	  if (ele.nodeName.toLowerCase() == 'li') {
		var subnav = $('> ul', ele);
		return subnav.length ? subnav[0] : null;
	  } else {

		return ele;
	  }
	}

	function getActuator(ele) {
	  if (ele.nodeName.toLowerCase() == 'ul') {
		return $(ele).parents('li')[0];
	  } else {
		return ele;
	  }
	}

	function hide() {
	  var subnav = getSubnav(this);
	  if (!subnav) return;
	  $.data(subnav, 'cancelHide', false);
	  setTimeout(function() {
		if (!$.data(subnav, 'cancelHide')) {
		  $(subnav).slideUp(100);
		}
	  }, 250);
	}

	function show() {
	  var subnav = getSubnav(this);
	  if (!subnav) return;
	  $.data(subnav, 'cancelHide', true);
	  $(subnav).css({zIndex: zIndex++}).slideDown(options.speed);
	  if (this.nodeName.toLowerCase() == 'ul') {
		var li = getActuator(this);
		$(li).addClass('hover');
		$('> a', li).addClass('hover');
	  }
	}

	$('ul, li', this).hover(show, hide);
	$('li', this).hover(
	  function() { $(this).addClass('hover'); $('> a', this).addClass('hover'); },
	  function() { $(this).removeClass('hover'); $('> a', this).removeClass('hover'); }
	);

});

};

var submenuLayer = 1000;

function setup_avatar_menu(parent) {
	if (!parent) {
		parent = document;
	}

	// avatar image menu link
	$(parent).find("div.usericon img").mouseover(function() {
		// find nested avatar_menu_button and show
		$(this.parentNode.parentNode).children(".avatar_menu_button").show();
		$(this.parentNode.parentNode).children("div.avatar_menu_button").addClass("avatar_menu_arrow");
		//$(this.parentNode.parentNode).css("z-index", submenuLayer);
	})
	.mouseout(function() {
		if($(this).parent().parent().find("div.sub_menu").css('display')!="block") {
			$(this.parentNode.parentNode).children("div.avatar_menu_button").removeClass("avatar_menu_arrow");
			$(this.parentNode.parentNode).children("div.avatar_menu_button").removeClass("avatar_menu_arrow_on");
			$(this.parentNode.parentNode).children("div.avatar_menu_button").removeClass("avatar_menu_arrow_hover");
			$(this.parentNode.parentNode).children(".avatar_menu_button").hide();
		}
		else {
			$(this.parentNode.parentNode).children("div.avatar_menu_button").removeClass("avatar_menu_arrow");
			$(this.parentNode.parentNode).children("div.avatar_menu_button").removeClass("avatar_menu_arrow_on");
			$(this.parentNode.parentNode).children("div.avatar_menu_button").removeClass("avatar_menu_arrow_hover");
			$(this.parentNode.parentNode).children(".avatar_menu_button").show();
			$(this.parentNode.parentNode).children("div.avatar_menu_button").addClass("avatar_menu_arrow");
		}
	});


	// avatar contextual menu
	$(".avatar_menu_button img").click(function(e) {

		var submenu = $(this).parent().parent().find("div.sub_menu");

		// close submenu if arrow is clicked & menu already open
		if(submenu.css('display') == "block") {
			//submenu.hide();
		}
		else {
			// get avatar dimensions
			var avatar = $(this).parent().parent().parent().find("div.usericon");
			//alert( "avatarWidth: " + avatar.width() + ", avatarHeight: " + avatar.height() );

			// move submenu position so it aligns with arrow graphic
			if (e.pageX < 840) { // popup menu to left of arrow if we're at edge of page
			submenu.css("top",(avatar.height()) + "px")
					.css("left",(avatar.width()-15) + "px")
					.fadeIn('normal');
			}
			else {
			submenu.css("top",(avatar.height()) + "px")
					.css("left",(avatar.width()-166) + "px")
					.fadeIn('normal');
			}

			// force z-index - workaround for IE z-index bug
			avatar.css("z-index",  submenuLayer);
			avatar.find("a.icon img").css("z-index",  submenuLayer);
			submenu.css("z-index", submenuLayer+1);

			submenuLayer++;

			// change arrow to 'on' state
			$(this.parentNode.parentNode).children("div.avatar_menu_button").removeClass("avatar_menu_arrow");
			$(this.parentNode.parentNode).children("div.avatar_menu_button").removeClass("avatar_menu_arrow_hover");
			$(this.parentNode.parentNode).children("div.avatar_menu_button").addClass("avatar_menu_arrow_on");
		}

		// hide any other open submenus and reset arrows
		$("div.sub_menu:visible").not(submenu).hide();
		$(".avatar_menu_button").removeClass("avatar_menu_arrow");
		$(".avatar_menu_button").removeClass("avatar_menu_arrow_on");
		$(".avatar_menu_button").removeClass("avatar_menu_arrow_hover");
		$(".avatar_menu_button").hide();
		$(this.parentNode.parentNode).children("div.avatar_menu_button").addClass("avatar_menu_arrow_on");
		$(this.parentNode.parentNode).children("div.avatar_menu_button").show();
		//alert("submenuLayer = " +submenu.css("z-index"));
	})
	// hover arrow each time mouseover enters arrow graphic (eg. when menu is already shown)
	.mouseover(function() {
		$(this.parentNode.parentNode).children("div.avatar_menu_button").removeClass("avatar_menu_arrow_on");
		$(this.parentNode.parentNode).children("div.avatar_menu_button").removeClass("avatar_menu_arrow");
		$(this.parentNode.parentNode).children("div.avatar_menu_button").addClass("avatar_menu_arrow_hover");
	})
	// if menu not shown revert arrow, else show 'menu open' arrow
	.mouseout(function() {
		if($(this).parent().parent().find("div.sub_menu").css('display')!="block"){
			$(this.parentNode.parentNode).children("div.avatar_menu_button").removeClass("avatar_menu_arrow_hover");
			$(this.parentNode.parentNode).children("div.avatar_menu_button").removeClass("avatar_menu_arrow");
			$(this.parentNode.parentNode).children("div.avatar_menu_button").addClass("avatar_menu_arrow");
		}
		else {
			$(this.parentNode.parentNode).children("div.avatar_menu_button").removeClass("avatar_menu_arrow_hover");
			$(this.parentNode.parentNode).children("div.avatar_menu_button").removeClass("avatar_menu_arrow");
			$(this.parentNode.parentNode).children("div.avatar_menu_button").addClass("avatar_menu_arrow_on");
		}
	});

	// hide avatar menu if click occurs outside of menu
	// and hide arrow button
	$(document).click(function(event) {
			var target = $(event.target);
			if (target.parents(".usericon").length == 0) {
				$(".usericon div.sub_menu").fadeOut();
				$(".avatar_menu_button").removeClass("avatar_menu_arrow");
				$(".avatar_menu_button").removeClass("avatar_menu_arrow_on");
				$(".avatar_menu_button").removeClass("avatar_menu_arrow_hover");
				$(".avatar_menu_button").hide();
			}
	});


}

$(document).ready(function() {

	setup_avatar_menu();

});

	function elggUpdateContent(content, entityname) {
		content = ' ' + content + ' ';
		
	if(window.tinyMCE)
		window.tinyMCE.execCommand("mceInsertRawHTML",true,content);
			$.facebox.close();
	}


/*
 * Facebox (for jQuery)
 * version: 1.2 (05/05/2008)
 * @requires jQuery v1.2 or later
 *
 * Examples at http://famspam.com/facebox/
 *
 * Licensed under the MIT:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2007, 2008 Chris Wanstrath [ chris@ozmm.org ]
 *
 * Usage:
 *  
 *  jQuery(document).ready(function() {
 *    jQuery('a[rel*=facebox]').facebox() 
 *  })
 *
 *  <a href="#terms" rel="facebox">Terms</a>
 *    Loads the #terms div in the box
 *
 *  <a href="terms.html" rel="facebox">Terms</a>
 *    Loads the terms.html page in the box
 *
 *  <a href="terms.png" rel="facebox">Terms</a>
 *    Loads the terms.png image in the box
 *
 *
 *  You can also use it programmatically:
 * 
 *    jQuery.facebox('some html')
 *
 *  The above will open a facebox with "some html" as the content.
 *    
 *    jQuery.facebox(function($) { 
 *      $.get('blah.html', function(data) { $.facebox(data) })
 *    })
 *
 *  The above will show a loading screen before the passed function is called,
 *  allowing for a better ajaxy experience.
 *
 *  The facebox function can also display an ajax page or image:
 *  
 *    jQuery.facebox({ ajax: 'remote.html' })
 *    jQuery.facebox({ image: 'dude.jpg' })
 *
 *  Want to close the facebox?  Trigger the 'close.facebox' document event:
 *
 *    jQuery(document).trigger('close.facebox')
 *
 *  Facebox also has a bunch of other hooks:
 *
 *    loading.facebox
 *    beforeReveal.facebox
 *    reveal.facebox (aliased as 'afterReveal.facebox')
 *    init.facebox
 *
 *  Simply bind a function to any of these hooks:
 *
 *   $(document).bind('reveal.facebox', function() { ...stuff to do after the facebox and contents are revealed... })
 *
 */
(function($) {
  $.facebox = function(data, klass) {
    $.facebox.loading()

    if (data.ajax) fillFaceboxFromAjax(data.ajax)
    else if (data.image) fillFaceboxFromImage(data.image)
    else if (data.div) fillFaceboxFromHref(data.div)
    else if ($.isFunction(data)) data.call($)
    else $.facebox.reveal(data, klass)
  }

  /*
   * Public, $.facebox methods
   */

  $.extend($.facebox, {
    settings: {
      opacity      : 0.7,
      overlay      : true,
      loadingImage : 'http://www.redciclista.com/mod/embed/images/loading.gif',
      closeImage   : 'http://www.redciclista.com/mod/embed/images/button_spacer.gif',
      imageTypes   : [ 'png', 'jpg', 'jpeg', 'gif' ],
      faceboxHtml  : '\
    <div id="facebox" style="display:none;"> \
      <div class="popup"> \
	      <div class="body"> \
		      <div class="footer"> \
		          <a href="#" class="close"> \
		            <img src="http://www.redciclista.com/mod/embed/images/button_spacer.gif" title="close" class="close_image" width="22" height="22" border="0" /> \
		          </a> \
		        </div> \
		        <div class="content"> \
		        </div> \
	      </div> \
      </div> \
    </div>'
    },

    loading: function() {
      init()
      if ($('#facebox .loading').length == 1) return true
      showOverlay()

      $('#facebox .content').empty()
      $('#facebox .body').children().hide().end().
        append('<div class="loading"><br /><br /><img src="'+$.facebox.settings.loadingImage+'"/><br /><br /></div>')

      $('#facebox').css({
        top:	getPageScroll()[1] + (getPageHeight() / 10),
        // Curverider addition (pagewidth/2 - modalwidth/2)
        left: ((getPageWidth() / 2) - ($('#facebox').width() / 2))
      }).show()

      $(document).bind('keydown.facebox', function(e) {
        if (e.keyCode == 27) $.facebox.close()
        return true
      })
      $(document).trigger('loading.facebox')
    },

    reveal: function(data, klass) {
      $(document).trigger('beforeReveal.facebox')
      if (klass) $('#facebox .content').addClass(klass)
      $('#facebox .content').append(data)
      
	setTimeout(function() {
	    $('#facebox .loading').remove();
	    $('#facebox .body').children().fadeIn('slow');
        $('#facebox').css('left', $(window).width() / 2 - ($('#facebox').width() / 2));
        $(document).trigger('reveal.facebox').trigger('afterReveal.facebox');
        }, 1000);
      
      //$('#facebox .loading').remove()
      //$('#facebox .body').children().fadeIn('slow')
      //$('#facebox').css('left', $(window).width() / 2 - ($('#facebox').width() / 2))
      //$(document).trigger('reveal.facebox').trigger('afterReveal.facebox')
      
    },

    close: function() {
      $(document).trigger('close.facebox')
      return false
    }
  })

  /*
   * Public, $.fn methods
   */
   
   // Curverider addition
/*
	$.fn.wait = function(time, type) {
	    time = time || 3000;
	    type = type || "fx";
	    return this.queue(type, function() {
	        var self = this;
	        setTimeout(function() {
	            //$(self).queue();
	            $('#facebox .loading').remove();
	        }, time);
	    });
	};
*/

  $.fn.facebox = function(settings) {
    init(settings)

    function clickHandler() {
      $.facebox.loading(true)

      // support for rel="facebox.inline_popup" syntax, to add a class
      // also supports deprecated "facebox[.inline_popup]" syntax
      var klass = this.rel.match(/facebox\[?\.(\w+)\]?/)
      if (klass) klass = klass[1]

      fillFaceboxFromHref(this.href, klass)
      return false
    }

    return this.click(clickHandler)
  }

  /*
   * Private methods
   */

  // called one time to setup facebox on this page
  function init(settings) {
    if ($.facebox.settings.inited) return true
    else $.facebox.settings.inited = true

    $(document).trigger('init.facebox')
    /* makeCompatible() */

    var imageTypes = $.facebox.settings.imageTypes.join('|')
    $.facebox.settings.imageTypesRegexp = new RegExp('\.' + imageTypes + '$', 'i')

    if (settings) $.extend($.facebox.settings, settings)
    $('body').append($.facebox.settings.faceboxHtml)

    var preload = [ new Image(), new Image() ]
    preload[0].src = $.facebox.settings.closeImage
    preload[1].src = $.facebox.settings.loadingImage
	preload.push(new Image())

/*
    $('#facebox').find('.b:first, .bl, .br, .tl, .tr').each(function() {
      preload.push(new Image())
      preload.slice(-1).src = $(this).css('background-image').replace(/url\((.+)\)/, '$1')
    })
*/

    $('#facebox .close').click($.facebox.close)
    $('#facebox .close_image').attr('src', $.facebox.settings.closeImage)
  }
  
  // getPageScroll() by quirksmode.com
  function getPageScroll() {
    var xScroll, yScroll;
    if (self.pageYOffset) {
      yScroll = self.pageYOffset;
      xScroll = self.pageXOffset;
    } else if (document.documentElement && document.documentElement.scrollTop) {	 // Explorer 6 Strict
      yScroll = document.documentElement.scrollTop;
      xScroll = document.documentElement.scrollLeft;
    } else if (document.body) {// all other Explorers
      yScroll = document.body.scrollTop;
      xScroll = document.body.scrollLeft;	
    }
    return new Array(xScroll,yScroll) 
  }

	// Adapted from getPageSize() by quirksmode.com
	function getPageHeight() {
	var windowHeight
	if (self.innerHeight) {	// all except Explorer
	  windowHeight = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
	  windowHeight = document.documentElement.clientHeight;
	} else if (document.body) { // other Explorers
	  windowHeight = document.body.clientHeight;
	}	
	return windowHeight
	}

	// Curverider addition
	function getPageWidth() {
	  var windowWidth;
	  if( typeof( window.innerWidth ) == 'number' ) {
	    windowWidth = window.innerWidth; //Non-IE
	  } else if( document.documentElement && ( document.documentElement.clientWidth ) ) {
	    windowWidth = document.documentElement.clientWidth; //IE 6+ in 'standards compliant mode'
	  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
	    windowWidth = document.body.clientWidth; //IE 4 compatible
	  }
	  return windowWidth
	} 



  // Backwards compatibility
/*
  function makeCompatible() {
    var $s = $.facebox.settings

    $s.loadingImage = $s.loading_image || $s.loadingImage
    $s.closeImage = $s.close_image || $s.closeImage
    $s.imageTypes = $s.image_types || $s.imageTypes
    $s.faceboxHtml = $s.facebox_html || $s.faceboxHtml
  }
*/

  // Figures out what you want to display and displays it
  // formats are:
  //     div: #id
  //   image: blah.extension
  //    ajax: anything else
  function fillFaceboxFromHref(href, klass) {
    // div
    if (href.match(/#/)) {
      var url    = window.location.href.split('#')[0]
      var target = href.replace(url,'')
      $.facebox.reveal($(target).clone().show(), klass)

    // image
    } else if (href.match($.facebox.settings.imageTypesRegexp)) {
      fillFaceboxFromImage(href, klass)
    // ajax
    } else {
      fillFaceboxFromAjax(href, klass)
    }
  }

  function fillFaceboxFromImage(href, klass) {
    var image = new Image()
    image.onload = function() {
      $.facebox.reveal('<div class="image"><img src="' + image.src + '" /></div>', klass)
    }
    image.src = href
  }

  function fillFaceboxFromAjax(href, klass) {
    $.get(href, function(data) { $.facebox.reveal(data, klass) })
  }

  function skipOverlay() {
    return $.facebox.settings.overlay == false || $.facebox.settings.opacity === null 
  }

  function showOverlay() {
    if (skipOverlay()) return

    if ($('facebox_overlay').length == 0) 
      $("body").append('<div id="facebox_overlay" class="facebox_hide"></div>')

    $('#facebox_overlay').hide().addClass("facebox_overlayBG")
      .css('opacity', $.facebox.settings.opacity)
      /* .click(function() { $(document).trigger('close.facebox') }) */
      .fadeIn(400)
    return false
  }

  function hideOverlay() {
    if (skipOverlay()) return

    $('#facebox_overlay').fadeOut(400, function(){
      $("#facebox_overlay").removeClass("facebox_overlayBG")
      $("#facebox_overlay").addClass("facebox_hide") 
      $("#facebox_overlay").remove()
    })
    
    return false
  }

  /*
   * Bindings
   */

  $(document).bind('close.facebox', function() {
    $(document).unbind('keydown.facebox')
    $('#facebox').fadeOut(function() {
      $('#facebox .content').removeClass().addClass('content')
      hideOverlay()
      $('#facebox .loading').remove()
    })
  })
  
  
  
  
	// Curverider addition
	$(window).resize(function(){
	  //alert("resized");
	  
    $('#facebox').css({
        top:	getPageScroll()[1] + (getPageHeight() / 10),
        left: ((getPageWidth() / 2) - 365)
      })
	  
	  
	});





})(jQuery);
