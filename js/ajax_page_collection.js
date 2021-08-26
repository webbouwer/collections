// load ajax
jQuery(function($) {

  var container = $('#loopcontainer.isotope'),
    gutterWidth = 0,
    colWidth = container.width() / 7,
    currCat = '',

    defaultselect = 'foto',
    filters = '.' + defaultselect,
    filterlist = Array(filters); // *

  container.isotope({
    itemSelector: '.post-artifact',
    animationEngine: 'best-available',
    transitionDuration: '0.9s',
    masonry: {
      //isFitWidth: true,
      columnWidth: colWidth,
      gutter: gutterWidth,
    },
    initLayout: false
  });


  /* on resize */
  var resizeId;
  $(window).resize(function() {
    clearTimeout(resizeId);
    resizeId = setTimeout(doneResizing, 20);
  });

  function doneResizing() {
    setColumnWidth();
  }

  function setColumnWidth() {
    var w = container.width();
    /* TODO: check width for small screens .. */
    colWidth = w / 7;

    container.isotope('reloadItems')
      .isotope({
        masonry: {
          columnWidth: colWidth,
          gutter: gutterWidth,
        }
      }).isotope({
        filter: filters
      }).isotope('layout');
  }



  /* AJAX Function */
  let pullpage = 0; // starts onload
  let pullflag = true;

  function getCollectionData() {

    if ($('#loopcontainer.isotope').length && $('#loopcontainer-loader').length) {

      if (pullflag) {

        $('#loopcontainer-loader').fadeIn(200);

        pullflag = false;
        pullpage++;

        let type = $('#loopcontainer').data('posttype');
        let tax = $('#loopcontainer').data('taxname');
        let term = $('#loopcontainer').data('term'); //'chateau-du-lac';
        let amount = $('#loopcontainer').data('ppp');

        jQuery.ajax({
          type: "POST",
          url: ajax.url,
          data: {
            nonce: ajax.nonce,
            action: 'getCollectionData',
            dataType: 'json', // Choosing a JSON datatype
            data: {
              posttype: type,
              taxname: tax,
              slug: term,
              ppp: amount,
              page: pullpage
            },
          },
          success: function(response) {
            var items = [];
            $.each(response, function(key, val) {
              items.push(val.html);
            });
            $("#loopcontainer").append(items);
            $('#loopcontainer').imagesLoaded(function(instance) {
              setTypeMenu(); // set MEnu
              doneResizing(); // recall isotope
            });
            //console.log(JSON.stringify(response));
            if (response.length >= amount) {
              pullflag = true;
            }
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {
            //Error
          },
          timeout: 60000
        });

        $('#loopcontainer-loader').fadeOut(200);
        return false;
      }
    }
  }

  function setTypeMenu() {
    $('#typemenu ul li:not(#menubutton)').each(function() {
      var chk = '.' + $(this).data('type');
      if ($(chk).length > 0) {
        $(this).removeClass('notavailable');
        $(this).addClass('available');
        //$(this).find('span').append( '('+( $( chk ).length - 1)+')' );
      } else {
        $(this).removeClass('available');
        $(this).addClass('notavailable');
      }
    });
  }


  $(window).ready(function() {

    if ( $('#loopcontainer.isotope').length && $('#loopcontainer-loader').length) {

      getCollectionData();

      if($('body').hasClass('home') ){
        // see custom theme scripts
        let introcontent = $('#introview');
        activeIntrobox(introcontent);
      }
    }

  });




  $(document).on('scroll', function() {
    var scrollHeight = $(document).height();
    var scrollPosition = $(window).height() + $(window).scrollTop();
    if ((scrollHeight - scrollPosition) / scrollHeight === 0) {
      getCollectionData();
    }
  });




  $(document).on('click touch tap', '#loopcontainer-loader a', function(e) {
    e.preventDefault();
    getCollectionData();
  });

  $(document).on('click touch tap', '#display-toggle a', function(e) {
    e.preventDefault();
    if ($('#loopcontainer.grid-view').length) {
      $('#loopcontainer').removeClass('grid-view');
      $('#loopcontainer').addClass('list-view');
    } else {
      $('#loopcontainer').removeClass('list-view');
      $('#loopcontainer').addClass('grid-view');
    }
    doneResizing();
  });

  $('#typemenu ul').on('click', 'li.available', function() {
    var type = $(this).data('type');
    var butclass = '.' + type;
    var butname = '.but-' + type;
    if ($(this).data('type') != 'foto' && $.inArray(butclass, filterlist) < 0) { // remove default foto
      if ($.inArray('.foto', filterlist) >= 0) {
        filterlist.splice($.inArray('.foto', filterlist), 1);
        $('#typemenu ul li.but-foto').removeClass('selected');
      }
    }
    if ($.inArray(butclass, filterlist) >= 0) {
      filterlist.splice($.inArray(butclass, filterlist), 1);
      $(this).removeClass('selected');
    } else {
      filterlist.push(butclass);
      $(this).addClass('selected');
    }

    if (filterlist.length < 1) {
      filters = '.' + defaultselect,
        filterlist = Array(filters);
      $('#typemenu ul li.but-' + defaultselect).addClass('selected');
    } else {
      filters = filterlist.join(","); // = or/or .. and/and :: filterlist.join(",");
    }
    console.log(filters);
    //container.isotope({ filter: filters }).isotope( 'layout' );
    setColumnWidth();
  });



  $(document).on('click', '.post-artifact .overlay, .entry-title a,.item-icons ul li', function(event) {
    event.preventDefault();
    var pid = $(this).parent().closest('.post-artifact').data('id');
    var mtype = 'foto';
    if ($(this).hasClass('icon-button')) {
      mtype = $(this).data('type');
    }
    var data = {
      action: 'artifact_view',
      id: pid
    };

    $.getJSON(ajax_data.ajaxurl, data, function(json) {
      if (json.success) {
        var p = json.data.postdata;
        var html = '<div class="popcontainer">' +
          '<div class="mediabox ' + p.orientation + '"><img src="' + p.image + '" class="wp-post-image" alt="" /></div>' +
          '<div class="contentbox"><div class="column">' +
          '<h2>' + p.title + '</h2>' +
          '<div class="text">' + p.excerpt + '</div>' +
          '</div>';
        var bundle = json.data.postmedia;
        $('#typemenu ul li:not(#menubutton)').each(function(c, el) {
          let countmedia = 0;
          let mediacolumn = '';
          mediacolumn += '<div class="column">';
          mediacolumn += $(el).find('span').text();
          $.each(bundle, function(i, media) {
            if (media.type_slug === $(el).data('type')) {
              countmedia++;
              //html += media.title;
            }
          });
          mediacolumn += '(' + countmedia + ')';
          mediacolumn += '</div>';
          if(countmedia > 0){
            html += mediacolumn;
          }
        });

        html += '</div>';

        activeOverlay(html);
        //alert( JSON.stringify(json.data) );
      } else {
        //alert( json.data.message );
      }
    });
  });

  function activeOverlay(content) {
    if ($('#infoboxcontainer').length > 0) {
      $('#infoboxcontainer').fadeOut(300, function() {
        $(this).remove();
      });
    }
    if ($('#overlaycontainer').length < 1) {
      $('<div id="overlaycontainer"><div class="closeoverlay"></div><div class="outermargin"></div></div>').hide().appendTo($('#page').parent());
    }
    $('#overlaycontainer .outermargin').html(content);
    $('#overlaycontainer').fadeIn(200);
    $('#loopcontainer').fadeOut(300);

  }


  /*
  $(document).on('click', '#menubutton', function(event) {
    var menu = $('#mainmenu').clone();
    activeInfobox(menu);
    $(this).addClass('selected');
  });
  */


  function activeInfobox(content) {


    if ($('#overlaycontainer').length > 0) {
      $('#overlaycontainer').fadeOut(300, function() {
        $(this).remove();
      });
    }
    if ($('#infoboxcontainer').length < 1) {
      $('<div id="infoboxcontainer"><div class="closeinfobox"></div><div class="outermargin"></div></div>').hide().appendTo($('#loopcontainer').parent());
    }
    $('#infoboxcontainer .outermargin').html(content);
    $('#infoboxcontainer').fadeIn(200);
    $('#loopcontainer').fadeOut(300);

  }



  $(document).on('click', '.closeoverlay', function(event) {
    $('#overlaycontainer').removeClass('intro');
    $('#overlaycontainer').fadeOut(300, function() {
      $(this).remove();
    });
    $('#loopcontainer').fadeIn(200);
  });

  $(document).on('click', '.closeinfobox,#menubutton.selected', function(event) {
    var container = $("#overlaycontainer .outermargin");
    if ($('#menubutton').hasClass('selected')) {
      $('#menubutton').removeClass('selected');
    }
    $('#infoboxcontainer').fadeOut(300, function() {
      $(this).remove();
    });
    $('#loopcontainer').fadeIn(200);
  });





    function activeIntrobox(content) {


      if ($('#introboxcontainer').length < 1) {
        $('<div id="introboxcontainer"><div class="closeintrobox"></div><div class="outermargin"></div></div>').hide().appendTo($('#loopcontainer').parent());
      }
      $('#introboxcontainer .outermargin').html(content);
      $('#introboxcontainer').fadeIn(200);
      //$('#loopcontainer').fadeOut(300);
      $('#display-toggle').hide();
      $('html, body').animate({
        scrollTop: $("#introboxcontainer").offset().top
      },0);
      $('html, body').css({ overflow:'hidden', });
      /*
      {
						duration: 50,
						step: function( ){
							console.log( "Stopt scrolling" );
						}
					}); // */
    }

  $(document).on('click', '.closeintrobox,#introboxcontainer', function(e) {

    if (e.target.id == 'introview' || $(e.target).parents('#introview').length > 0) {
    }else{
      $('html, body').css({ overflow:'visible', });
      $('#introboxcontainer').fadeOut(300, function() {
        $('html, body').animate({
          scrollTop: $("body").offset().top
        }, 2000);

        $('#display-toggle').fadeIn(200);
        $(this).remove();
      });
    }
  });

});
