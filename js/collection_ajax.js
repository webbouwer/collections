jQuery(function($) {


  function setCookie() {
      var now = new Date();
      var minutes = 30;
      now.setTime(now.getTime() + (minutes * 60 * 1000));
      cookievalue = 'check';
      document.cookie="firsttime=" + cookievalue;
      document.cookie = "expires=" + now.toUTCString() + ";"
  }
  function getCookie(cname) {
  let name = cname + "=";
  let ca = document.cookie.split(';');
  for(let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}
  function checkCookie() {
    var chk = getCookie("firsttime");
    if (chk != ""){
      // second time
    }else{
      setCookie(); // firsttime
    }
  }


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

      //console.log('more?');

    if ($('#loopcontainer.isotope').length) {

      if (pullflag) {
        pullflag = false;
        pullpage++;
        let type = $('#loopcontainer').data('posttype');
        let tax = $('#loopcontainer').data('taxname');
        let term = $('#loopcontainer').data('term'); // cat or sub cat
        let amount = $('#loopcontainer').data('ppp');

        let orderby = $('#loopcontainer').attr('data-orderby');
        let order = $('#loopcontainer').attr('data-order');

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
              orderby: orderby,
              order: order,
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
      //console.log('set artifact types menu');
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

    setImagesOrient();

    $('#loopcontainer').fadeOut(200);


    $('#typemenu ul').removeClass("collection-types").addClass("artifact-types");

    $('#overlaycontainer').fadeIn(200, function() {
      if(type){
        // active filter type
        $('body').find('.popcontainer').addClass(type);
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

  function setMediaboxSlider( type = false ){



    if( $('.mediabox .mediacontainer').length > 0 ){

      var slidebox = $('.mediabox .mediacontainer');

      if(type == 'foto'){
        $('.cover img').hide();
      }else{
        $('.cover img').fadeIn();
      }

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


        $slider.find('div').eq(0).addClass('active').show();
        posPrevNext();

        function nextSlide() {
          if(currentSlide < allSlides) {
              $slide.eq(currentSlide).fadeOut(200);
              $slide.eq(currentSlide + 1).addClass('active').fadeIn(200);
              currentSlide+=1;
          }else{
            $slide.eq(currentSlide).fadeOut(200);
            $slide.eq(0).addClass('active').fadeIn(200);
            currentSlide=0;
          }
          posPrevNext();
        }

        function prevSlide() {
          if(currentSlide > 0) {
              $slide.eq(currentSlide).fadeOut(200);
              $slide.eq(currentSlide - 1).addClass('active').fadeIn(200);
              currentSlide-=1;
          }else{
            $slide.eq(currentSlide).fadeOut(200);
            $slide.eq(allSlides).addClass('active').fadeIn(200);
            currentSlide=allSlides;
          }
          posPrevNext();
        }

        $next.on('click', nextSlide);
        $prev.on('click', prevSlide);

       }else{
         $('#nextmedia,#prevmedia').hide();
       }


    }

  }

function posPrevNext(){
  setTimeout(function(){
    $('.prevnextnav').width( $('body').find('.mediaholder.active,.mediaholder.active .embed').width() );
  },300);
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
          '<div class="prevnextnav"><div id="prevmedia"><span>Prev</span></div><div id="nextmedia"><span>Next</span></div></div>'+
          '<div class="mediabox">'+
          '<div class="cover '+ p.orientation +'"><img src="'+ p.image +'" class="wp-post-image" alt="" />'+
          '<div class="title"><h1>' + p.title + '<h1></div><div class="text">' + p.excerpt + '</div></div>'+
          '</div>';


        var bundle = json.data.postmedia;

        var artifactmedia = '';

        $('#typemenu ul li:not(#menubutton)').each(function(c, el) {

          var countmedia = 0;
          var mediabox = '';

          //var option = '<div class="column">';
          //option += '<div class="media-icon but-' + $(el).data('type') + '" data-type="' + $(el).data('type') + '" >';
          //option += '<span>' + $(el).find('span').text();

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
                  mediabox += '<img class="embed zoom" src="'+media.src+'" width="600" height="auto" />';
                  break;
                case 'mp4':
                case 'mp3':
                  mediabox += '<video class="embed" src="'+media.src+'" width="600" height="350" controls></video>';
                  break;
                case 'pdf':
                  mediabox += '<iframe class="embed" src="'+media.src+'#toolbar=0" width="100%" height="640px">'+
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
          mediabox += '</div>';

          if (countmedia > 0) { // also active in type menu
            artifactmedia += mediabox;
          }

        });

        html += '<div class="artifact-media">'+artifactmedia+'</div>';

        activeOverlay(html,selected_mediatype);

      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        //Error
      },
      timeout: 60000
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
      if( !$('body').find('#infoboxcontainer,#overlaycontainer').length ){
        getCollectionData();
      }
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



  // girsd / list toggle
  $(document).on('click touchstart', '#display-toggle a', function(e){
    e.preventDefault();
    if($('#loopcontainer.grid-view').length){
      $('#loopcontainer').removeClass('grid-view');
      $('#loopcontainer').addClass('list-view');
      doneResizing();
    }else{
      $('#loopcontainer').removeClass('list-view');
      $('#loopcontainer').addClass('grid-view');
      doneResizing();
    }
  });

  // orderby select
  $(document).on('click touchend', '#display-options ul.orderby li', function(e){
    $('#loopcontainer').attr('data-orderby', $(this).data('orderby') );
    if( $(this).data('orderby') == 'menu_order' ){
      $('#loopcontainer').attr('data-order', 'asc' );
      $('#display-options ul.order li').removeClass('selected');
      $('#display-options ul.order li.asc').addClass('selected');
    }
    if( $(this).data('orderby') == 'menu_order' ){
      $('#display-options ul.order').html('<li class="asc selected" data-order="asc">Oplopend</li><li class="desc" data-order="desc">Aflopend</li>');
    }
    if( $(this).data('orderby') == 'title' ){
      $('#display-options ul.order').html('<li class="asc selected" data-order="asc">A t/m Z</li><li class="desc" data-order="desc">Z t/m A</li>');
    }
    if( $(this).data('orderby') == 'date' ){
      $('#display-options ul.order').html('<li class="asc selected" data-order="asc">Later</li><li class="desc" data-order="desc">Eerder</li>');
    }
    $('#display-options ul.orderby li').removeClass('selected');
    $(this).addClass('selected');
    pullpage = 0; // starts onload
    pullflag = true;
    $('#loopcontainer .post-artifact').remove();
    getCollectionData();
  });

  // order select
  $(document).on('click touchend', '#display-options ul.order li', function(e){
    $('#loopcontainer').attr('data-order', $(this).data('order') );
    $('#display-options ul.order li').removeClass('selected');
    $(this).addClass('selected');
    pullpage = 0; // starts onload
    pullflag = true;
    $('#loopcontainer .post-artifact').remove();
    getCollectionData();
  });




  // action type taxonomy menu
  $('#typemenu').on('click touchend', 'ul.collection-types li.available', function() {

    var type = $(this).data('type');
    var butclass = '.' + type;
    var butname = '.but-' + type;

    $('#typemenu ul li').removeClass('selected');
    $('#typemenu ul li'+butname).addClass('selected');

    filters = butclass;

    setColumnWidth();


  });

  // action type post menu
  $('body').on('click touchend', '#typemenu ul.artifact-types li.available', function() {
    //alert( $(this).data('type') );
    var picturebox = $( '.popcontainer .mediabox .cover');
    var container = $('.popcontainer .mediabox');

    container.html( $('.artifact-media .mediacontainer.'+ $(this).data('type') ).clone() );
    container.prepend(picturebox);
    container.addClass('display');

    setMediaboxSlider( $(this).data('type') );

    $('#typemenu ul.artifact-types li').removeClass('selected');
    $('#typemenu ul.artifact-types li.but-'+$(this).data('type')).addClass('selected');

    $('body').find('.popcontainer').addClass($(this).data('type'));

    $('.media-icon').removeClass('selected');
    $('.media-icon.but-'+$(this).data('type')).addClass('selected');

  });


  // popup overlay artifact
  $(document).on('click touchend', '.post-artifact .overlay, .entry-title a,.item-icons ul li', function(event) {

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

  $(document).on('click touchstart', '.closeoverlay', function(event) {
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
      //if ($('#infoboxcontainer').length) {
        //closeInfobox();
      //}

    } else {

      // console.log(hash);
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

    if( $('body.home').length ){
      // check cookie
      var chk = getCookie("firsttime");
      if (chk != ""){
        // allready visited
        setTimeout( function(){
          $('html, body').stop().animate({
                  'scrollTop': $('#content').offset().top
              }, 800, 'swing', function () {
                var content = '<div class="innerpadding">'+$('#collection-info').html()+'</div>'; //'Introtekst Chateau du Lac';
                activeInfobox(content, 'collection');
              });
          },300);

      }else{

      setCookie(); // firsttime

      setTimeout( function(){
        $('html, body').stop().animate({
                'scrollTop': $('#content').offset().top
            }, 800, 'swing', function () {


          var content = '<video controls autoplay width="640" height="480"><source type="video/mp4" src="wp-content/uploads/2021/09/Intro_Hoekse_Schatkist_Chateau_du_lac.mp4"></video>';
          content += '';
          activeInfobox( content, 'intro');

          });
        },300);
      }

      console.log(document.cookie);

    }

  });


      //
      function setImagesOrient(){
          var pics = $('body').find("img");
          for (i = 0; i < pics.length; i++) {
            pics[i].addEventListener("load", function() {
                if (this.naturalHeight > this.naturalWidth) {
                    this.classList.add("portrait")
                } else {
                    this.classList.add("landscape")
                }
             })
             if (pics[i].complete) {
                 if (pics[i].naturalHeight > pics[i].naturalWidth) {
                     pics[i].classList.add("portrait")
                 } else if (pics[i].naturalHeight < pics[i].naturalWidth) {
                     pics[i].classList.add("landscape")
                 } else{
                     pics[i].classList.add("square")
                 }
             }
          }
        }


  function activeInfobox(content, popclass) {

      if ($('#overlaycontainer').length > 0) {
        $('#overlaycontainer').fadeOut(100, function() {
          $(this).remove();
        });
      }
      if ($('#infoboxcontainer').length < 1) {
        $('<div id="infoboxcontainer" class="'+popclass+'"><div class="closeinfobox"></div><div class="outermargin"></div></div>').hide().appendTo($('#loopcontainer').parent());
      }
      //content = infoboxTemplate( content );
      $('#infoboxcontainer .outermargin').html(content);

      $('#infoboxcontainer').fadeIn(200);
      //$('#loopcontainer').fadeOut(200);
  }

    function closeInfobox(){

      if( $('#infoboxcontainer').hasClass('collection') ){

        $('html, body').stop().animate({
              'scrollTop': $('body').offset().top
          }, 800, 'swing' );

      }


      $('#infoboxcontainer').fadeOut(200, function() {
        $(this).remove();
      });

    }

    $(document).on('click touchstart', '.closeinfobox', function(event) {

        event.preventDefault();
        if( $(this).parent().hasClass('intro') ){
          collectionInfobox();
        }else{
          closeInfobox();
        }

    });


    $(document).on('click touchend', '#infoboxcontainer.intro', function(event) {

          if(!$(event.target).is('#infoboxcontainer .outermargin, #infoboxcontainer .outermargin li a, video, video source, #infoboxcontainer .outermargin a')){
            event.preventDefault();
            collectionInfobox();
          }
    });

    $(document).on('click touchend', '#infoboxcontainer.collection', function(event) {

          if(!$(event.target).is('#infoboxcontainer .outermargin, #infoboxcontainer .outermargin li a, video, video sourc, #infoboxcontainer .outermargin a')){
            event.preventDefault();
            closeInfobox();
          }
    });

    function collectionInfobox(){
      $('#infoboxcontainer').fadeOut(200, function() {
        $(this).remove();

        var content = '<div class="innerpadding">'+$('#collection-info').html()+'</div>'; //'Introtekst Chateau du Lac';
        activeInfobox(content, 'collection');
      });
    }



});
