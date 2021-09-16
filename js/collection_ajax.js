jQuery(function($) {

  // prepare html container
  var container = $('#loopcontainer.isotope'),
    gutterWidth = 0,
    colWidth = container.width() / 20,
    currCat = '',

    // prepare isotope
    defaultselect = 'foto',
    filters = '.' + defaultselect,
    filterlist = Array(filters);

    // prepare item selection
    var selected_id = '',
    selected_slug = '',
    selected_mediatype = defaultselect;

  container.isotope({
    itemSelector: '.post-artifact',
    animationEngine: 'best-available',
    transitionDuration: '0.9s',
    masonry: {
      columnWidth: colWidth,
      gutter: gutterWidth,
    },
    initLayout: false
  });

  // prepare data
  let pullpage = 0; // starts onload
  let pullflag = true;

  function getCollectionData() {

    if ($('#loopcontainer.isotope').length) {

      if (pullflag) {
        pullflag = false;
        pullpage++;
        let type = $('#loopcontainer').data('posttype');
        let tax = $('#loopcontainer').data('taxname');
        let term = $('#loopcontainer').data('term'); // cat or sub cat
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
              setTypeMenu();
              container.isotope('reloadItems');
              doneResizing(); // recall isotope
            });
            if (response.length >= amount) {
              pullflag = true;
            }
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {
            //Error
          },
          timeout: 60000
        });
        return false;
      }
    }
  }

  // type menu
  function setTypeMenu() {

    // collections type menu
    if ($('#typemenu ul').hasClass('collection-types')) {
      $('#typemenu ul li:not(#menubutton)').each(function() {
        var chk = '.' + $(this).data('type');
        if ($(chk).length > 0) {
          $(this).removeClass('notavailable');
          $(this).addClass('available');
          //$(this).find('span').append( '('+( $( chk ).length)+')' );
        } else {
          $(this).removeClass('available');
          $(this).addClass('notavailable');
        }
      });
    }
    // artifact type menu
    if ($('#typemenu ul').hasClass('artifact-types')) {
      $('#typemenu ul li:not(#menubutton)').each(function() {
        var chk = '.' + $(this).data('type');
        if ($('.popcontainer ' + chk).length > 0) {
          $(this).removeClass('notavailable');
          $(this).addClass('available');
          //$(this).find('span').append( '('+( $( chk ).length)+')' );
        } else {
          $(this).removeClass('available');
          $(this).addClass('notavailable');
        }
      });
      console.log('set artifact types menu');
    }
  }


  // load isotope grid
  function setColumnWidth() {

    var w = container.width();
    colWidth = w / 20; // TODO: check width for small screens

    container //.isotope('reloadItems')
      .isotope({
        masonry: {
          columnWidth: colWidth,
          gutter: gutterWidth,
        }
      }).isotope({
        filter: filters
      }).isotope('layout');
    // route
    $(window).trigger('hashchange');

  }






  function activeOverlay(content,type = false) {

    if ($('#infoboxcontainer').length > 0) {
      $('#infoboxcontainer').fadeOut(100, function() {
        $(this).remove();
      });
    }
    if ($('#overlaycontainer').length < 1) {
      $('<div id="overlaycontainer"></div>').hide().appendTo($('#loopcontainer').parent());
      $('<div class="closeoverlay"></div><div class="outermargin"></div>').appendTo($('#overlaycontainer'));
    }
    $('#overlaycontainer .outermargin').html(content);


    $('#loopcontainer').fadeOut(200);

    $('#typemenu ul').removeClass("collection-types").addClass("artifact-types");


    $('#overlaycontainer').fadeIn(200, function() {
      if(type){
        // active filter type
        setTypeMenu();
        $('body').find('#typemenu ul.artifact-types li.but-'+type).trigger('click');
      }
    });

  }

  function closeOverlay() {

    selected_id = '';
    selected_slug = '';
    selected_mediatype = defaultselect;

    $('#overlaycontainer').removeClass('intro');
    $('#overlaycontainer').fadeOut(200, function() {
      $(this).remove();
    });

    $('#typemenu ul').removeClass("artifact-types").addClass("collection-types");
    var butclass = filters;
    var type = butclass.replace(".","");
    var butname = '.but-' + type;

    $('#typemenu ul li').removeClass('selected');
    $('#typemenu ul li'+butname).addClass('selected');

    setTypeMenu();

    $('#loopcontainer').fadeIn(200, function() {
      setTimeout(doneResizing, 20); // // reposition items (if window resized)
    });


  }

  function setMediaboxSlider(){

    if( $('.mediabox .mediacontainer').length > 0 ){

      var slidebox = $('.mediabox .mediacontainer');
       if( slidebox.children().length > 1){
          // set slider box ..
          // http://jsbin.com/nalewayume/1/edit?html,js,console,output
        slidebox.addClass('slider');
        slidebox.children().css({ "position":"relative","display":"none" });
        $('#nextmedia,#prevmedia').show();

        var $slider = slidebox,
        $prev = $('#prevmedia'),
        $next = $('#nextmedia'),
        $slide = $slider.find('div');

        var currentSlide = 0,
            allSlides = $slider.find('div').length - 1; // index start from 0


        $slider.find('div').eq(0).show();

        function nextSlide() {

          if(currentSlide < allSlides) {

              $slide.eq(currentSlide).fadeOut(200);
              $slide.eq(currentSlide + 1).fadeIn(200);

              currentSlide+=1;
          }

        }

        function prevSlide() {

          if(currentSlide > 0) {

              $slide.eq(currentSlide).fadeOut(200);
              $slide.eq(currentSlide - 1).fadeIn(200);

              currentSlide-=1;
          }
        }

        $next.on('click', nextSlide);
        $prev.on('click', prevSlide);


       }else{
         $('#nextmedia,#prevmedia').hide();
       }


    }

  }

  function getArtifact(){

    var data = {
      nonce: ajax.nonce,
      action: 'artifact_view',
      dataType: 'json', // Choosing a JSON datatype
      id: selected_id
    };

    //jQuery.getJSON(ajax_data.ajaxurl, data, function(json) {

    jQuery.ajax({
      type: "GET",
      url: ajax.url,
      data: data,
      success: function(json) {

      //if (json.success) {

        var p = json.data.postdata;
        var html = '<div class="popcontainer ' + p.slug + '">' +
        '<button id="prevmedia">Prev</button><button id="nextmedia">Next</button>'+
          '<div class="mediabox">'+
          '<div class="cover '+ p.orientation +'"><img src="'+ p.image +'" class="wp-post-image" alt="" /></div>'+
          '</div>' +
          '<div class="contentbox"><div class="title"><h1>' + p.title + '<h1></div>' +
          '<div class="innerpadding">' +
          '<div class="infotext">'+
          '<div class="text">' + p.excerpt + '</div>' +
          '</div>';

        var bundle = json.data.postmedia;

        var artifactmedia = '';

        $('#typemenu ul li:not(#menubutton)').each(function(c, el) {

          var countmedia = 0;
          var mediabox = '';

          var option = '<div class="column">';
          option += '<div class="media-icon but-' + $(el).data('type') + '" data-type="' + $(el).data('type') + '" >';
          option += '<span>' + $(el).find('span').text();

          mediabox += '<div class="mediacontainer '+$(el).data('type')+'">';

          $.each(bundle, function(i, media) {
            if (media.type_slug === $(el).data('type')) {
              countmedia++;
              var file = media.src;
              var extension = file.substr( (file.lastIndexOf('.') +1) );

              mediabox += '<div class="mediaholder '+media.type_name+'">';
              switch(extension) {
                case 'jpg':
                case 'png':
                case 'gif':
                  mediabox += '<img src="'+media.src+'" width="600" height="auto" />';
                  break;
                case 'mp4':
                case 'mp3':
                  mediabox += '<video src="'+media.src+'" width="600" height="350" controls></video>';
                  break;
                case 'pdf':
                  mediabox += '<iframe src="'+media.src+'#toolbar=0" width="100%" height="500px">'+
                  '<p>It appears you do not have a PDF plugin for this browser.<a href="'+media.src+'">click here to download the PDF file.</a></p>'+
                  '</iframe>';//'</object>';
                  break;
                case 'doc':
                case 'docx':
                  //mediabox += '<iframe src="'+media.src+'" width="600" height="350"></iframe>';
                  mediabox += '<a class="media-link" href="'+media.src+'">'+media.title+'</a>';
                  break;
                default:
                  mediabox += '<a class="media-link" href="'+media.src+'">'+media.title+'</a>';
              }
              mediabox += '</div>';
              // title,excerpt,src,type_parent,type_slug,type_name
            }
          });

          option += '(' + countmedia + ')';
          option += '</span></div></div>';

          mediabox += '</div>';

          if (countmedia > 0) { // also active in type menu
            artifactmedia += mediabox;
            html += option;
          }

        });
        html += '</div></div><div class="artifact-media">'+artifactmedia+'</div>';

        activeOverlay(html,selected_mediatype);
        // alert( JSON.stringify(bundle) );

      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        //Error
      },
      timeout: 60000

      //} else {
        // error
      //}


    });
    return false;


  }



  // on start get data and set taxonomy menu
  $(window).ready(function() {

    getCollectionData();
    $('#primarymenubox').before($('#typemenu'));
    if ($('#categorymenu').length) {
      $('#primarymenubox').before($('#categorymenu'));
    }
    setTypeMenu();
  });

  // onscroll load more
  $(document).on('scroll', function() {
    var scrollHeight = $(document).height();
    var scrollPosition = $(window).height() + $(window).scrollTop();
    if ((scrollHeight - scrollPosition) / scrollHeight === 0) {
      getCollectionData();
    }
  });

  // on resize
  var resizeId;
  $(window).resize(function() {
    clearTimeout(resizeId);
    resizeId = setTimeout(doneResizing, 20);
  });

  function doneResizing() {
    //if($('#loopcontainer').is(':visible')) {
    setColumnWidth();
    //}
  }








  // action type taxonomy menu
  $('#typemenu').on('click', 'ul.collection-types li.available', function() {

    var type = $(this).data('type');
    var butclass = '.' + type;
    var butname = '.but-' + type;

    $('#typemenu ul li').removeClass('selected');
    $('#typemenu ul li'+butname).addClass('selected');

    filters = butclass;

    /* and / or selection
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
    */
    setColumnWidth();

  });

  // action type post menu
  $('body').on('click', '#typemenu ul.artifact-types li.available, .contentbox .column div.media-icon', function() {
    //alert( $(this).data('type') );
    var picturebox = $( '.popcontainer .mediabox .cover');
    var container = $('.popcontainer .mediabox');

    container.html( $('.artifact-media .mediacontainer.'+ $(this).data('type') ).clone() );
    container.prepend(picturebox);
    container.addClass('display');

    setMediaboxSlider();

    $('#typemenu ul.artifact-types li').removeClass('selected');
    $('#typemenu ul.artifact-types li.but-'+$(this).data('type')).addClass('selected');

    $('.media-icon').removeClass('selected');
    $('.media-icon.but-'+$(this).data('type')).addClass('selected');

  });







  // popup overlay artifact
  $(document).on('click', '.post-artifact .overlay, .entry-title a,.item-icons ul li', function(event) {

    event.preventDefault();

    selected_id = $(this).parent().closest('.post-artifact').data('id');
    selected_slug = $(this).parent().closest('.post-artifact').data('slug');

    selected_mediatype = 'foto';
    if ( $(this).data('type') ) { //  $(this).hasClass('icon-button')
      var type = $(this).data('type');
      filters = '.'+ type;
      var butname = '.but-' + type;

      $('#typemenu ul li').removeClass('selected');
      $('#typemenu ul li'+butname).addClass('selected');
    }
    selected_mediatype = filters.replace('.', "");

    window.location.hash = selected_slug;
    //activeOverlay( ''+selected_id+' - '+selected_slug+'..');

  });





  $(document).on('click', '.closeoverlay', function(event) {
    event.preventDefault();
    history.pushState("", document.title, window.location.pathname);
    closeOverlay(); //history.go(-1);
  });


  // hash events
  $(window).bind('hashchange', function(e) {


    var pagehash = window.location.hash;
    var hash = pagehash.replace('#', '');

    if (hash == '') {
      // check popups to close
      if ($('#overlaycontainer').length) {
        closeOverlay();
      }
      if ($('#infoboxcontainer').length) {
        closeInfobox();
      }

    } else {

      console.log(hash);
      // check specific popups to close
      if ($('#overlaycontainer').length && $('#' + hash).length < 1) {
        closeOverlay();
      }
      if ($('#infoboxcontainer').length && $('#' + hash).length < 1) {
        closeInfobox();
      }

      if($("#loopcontainer").find("div[data-slug='" + hash + "']").length && selected_id == '' ) {
        selected_id = $("#loopcontainer").find("div[data-slug='" + hash + "']").data('id');
      }
      if( selected_id != '' ){
        getArtifact();
      }

    }


  });

  $(window).load(function() {

  });

  /*
  $(document).on('click', '#menubutton', function(event) {
      event.preventDefault();
      var menu = $('#mainmenu').clone();
      activeInfobox(menu);
      $(this).addClass('selected');
  });

  function activeInfobox(content) {
      if ($('#overlaycontainer').length > 0) {
        $('#overlaycontainer').fadeOut(100, function() {
          $(this).remove();
        });
      }
      if ($('#infoboxcontainer').length < 1) {
        $('<div id="infoboxcontainer"><div class="closeinfobox"></div><div class="outermargin"></div></div>').hide().appendTo($('#loopcontainer').parent());
      }
      $('#infoboxcontainer .outermargin').html(content);

      $('#infoboxcontainer').fadeIn(200);
      $('#loopcontainer').fadeOut(200);
  }

  function closeInfobox(){
      var container = $("#overlaycontainer .outermargin");
      if ($('#menubutton').hasClass('selected')) {
        $('#menubutton').removeClass('selected');
      }

      $('#infoboxcontainer').fadeOut(200, function() {
        $(this).remove();
      });
      $('#loopcontainer').fadeIn(200);
  }

  $(document).on('click', '.closeinfobox,#menubutton.selected', function(event) {
        event.preventDefault();
        closeInfobox();
  });
  */

  /* click outside
  $('html').click(function(e) {
    //if clicked element is not your element and parents aren't your div
    if (e.target.id != 'overlaycontainer' && $(e.target).parents('#overlaycontainer').length == 0) {
      closeOverlay();
      history.replaceState(null, document.title, location.pathname);
    }
    if (e.target.id != 'infoboxcontainer' && $(e.target).parents('#infoboxcontainer').length == 0) {
      closeInfobox();
      history.replaceState(null, document.title, location.pathname);
    }
  });
  */



});
