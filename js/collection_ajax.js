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


  // touch detections
  var element = $('#loopcontainer');
  var moved;
  var downListener = () => {
    moved = false;
  }
  element.on('mousedown', downListener);
  var moveListener = () => {
    moved = true;
  }
  element.on('mousemove', moveListener);
  var upListener = () => {
    if (moved) {
      console.log('moved');
    } else {
      console.log('not moved');
    }
  }
  element.on('mouseup', upListener);




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
    selected_mediatype = defaultselect,
    viewtype = 'grid-view';


  container.isotope({
    itemSelector: '.post-object',
    animationEngine: 'best-available',
    transitionDuration: '0.9s',
    masonry: {
      columnWidth: colWidth,
      gutter: gutterWidth,
    },
    percentPosition: false,
    initLayout: true
  });



  // prepare data
  let pullpage = 0; // starts onload
  let pullflag = true;


  function getCollectionData() {

    if ($('#loopcontainer.isotope').length) {
      if (pullflag) {   //console.log('more?');
        pullflag = false;
        pullpage++;
        var amount = $('#loopcontainer').data('ppp');

        jQuery.ajax({
          type: "POST",
          url: ajax.url,
          data: {
            nonce: ajax.nonce,
            action: 'getCollectionData',
            dataType: 'json', // Choosing a JSON datatype
            data: {
              posttype: $('#loopcontainer').data('posttype'),
              taxname: $('#loopcontainer').data('taxname'),
              slug: $('#loopcontainer').data('term'),
              orderby: $('#loopcontainer').attr('data-orderby'),
              order: $('#loopcontainer').attr('data-order'),
              ppp: amount,
              page: pullpage
            },
          },
          success: function(response) {

            var items = [];
            for (var i = 0; i < response.length; ++i) {
              items.push(response[i].html);
            }

            $("#loopcontainer").append(items).imagesLoaded(function(instance) {
              setTypeMenu();
              container.isotope('reloadItems');
              doneResizing(); // recall isotope
              //$(window).trigger('hashchange'); // check hash
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


          $('body').find('#typemenu ul.collection-types li').hover(
            function () {
              if( $(this).hasClass('available') && $('#typemenu ul').hasClass('collection-types') ){
              $('.menuinfo').html( '<span>'+ $(this).data('desc') +'<span>');
              }
            },
           function () {
              $('.menuinfo').html('');
           }
          );

    // object type menu
    if ($('#typemenu ul').hasClass('object-types')) {
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
      //console.log('set object types menu');
    }




  }



  // load isotope grid
  function setColumnWidth() {

    container = $('body').find('#loopcontainer');

    var w = container.width();
    colWidth = w / 20; // TODO: check width for small screens

    container //.isotope('reloadItems')
      .isotope({
        masonry: {
          columnWidth: colWidth,
          gutter: gutterWidth,
        },
        percentPosition: false,
        initLayout: true
      }).isotope({
        filter: filters +', .featured'
      }).isotope('layout');

    // route
    //$(window).trigger('hashchange');

  }

  function activeOverlay(content,type = false) {

    $('#isotopemenu').fadeOut();

    if ($('#infoboxcontainer').length > 0) {
      $('#infoboxcontainer').fadeOut(100, function() {
        $(this).remove();
      });
    }

    if ($('#overlaycontainer').length < 1) {
      $('<div id="overlaycontainer"></div>').hide().appendTo($('#objectcontainer'));
      $('<div class="closeoverlay"><span>close</span></div><div class="outermargin"></div>').appendTo($('#overlaycontainer'));
    }
    $('#overlaycontainer .outermargin').html(content);

    setImagesOrient();

    $('#overlaycontainer .outermargin .mediacontainer.'+type+' div.mediaholder').sort(function(a,b) {
      return $(a).data('order') < $(b).data('order');
    }).appendTo('#overlaycontainer .outermargin .mediacontainer.'+type);


    // object navigation
    viewtype = 'grid-view';
    if( $('#loopcontainer').hasClass('list-view') ){
      viewtype = 'list-view';
    }
    $('#loopcontainer').removeClass('grid-view list-view');//$('#loopcontainer').fadeOut(200);
    $('#loopcontainer').addClass('nav-view');

    $('#typemenu ul').removeClass("collection-types").addClass("object-types");//$('#typemenu .reset').fadeOut();

    $('#overlaycontainer').fadeIn(200, function() {

      if(type){
        // active filter type
        $('body').find('.popcontainer').addClass(type);
        setTypeMenu();
        $('body').find('#typemenu ul.object-types li.but-'+type).addClass('selected').trigger('click');
      }

    });

  }

  function closeOverlay() {

    selected_id = '';
    selected_slug = '';

    //selected_mediatype = $('body').find('#typemenu .icon-button.selected').data('type');

    $('#overlaycontainer').removeClass('intro');
    $('#overlaycontainer').fadeOut(200, function() {
      $(this).remove();
    });

    $('#typemenu ul').removeClass("object-types").addClass("collection-types");

    setTypeMenu();

    //$('#loopcontainer').fadeIn(200, function() {
    $('#loopcontainer').removeClass('nav-view');//$('#loopcontainer').fadeOut(200);
    $('#loopcontainer').addClass(viewtype);

    $('#isotopemenu').fadeIn();
    $('body').find('#typemenu ul.collection-types li.selected').trigger('click');
    setTimeout(doneResizing, 50); // // reposition items (if window resized)

    //});

  }

  function setMediaboxSlider( type = false ){

    if( $('.mediabox .mediacontainer').length > 0 ){

      var slidebox = $('.mediabox .mediacontainer');

      if(type == 'foto' || $('body').hasClass('medium') || $('body').hasClass('small') ){
        $('.cover img').hide();
      }else{
        $('.cover img').fadeIn();
      }

      if( slidebox.find('.mediaholder').length > 1){
      //if( slidebox.find('div:not(.tooltip, .tooltip *)'); ){
      // set slider box ..
      // http://jsbin.com/nalewayume/1/edit?html,js,console,output
      slidebox.addClass('slider');
      slidebox.find('.mediaholder').css({ "position":"relative","display":"none" });

        var $slider = slidebox,
        $prev = $('#prevmedia'),
        $next = $('#nextmedia'),
        $slide = $slider.find('div.mediaholder');

        var currentSlide = 0,
        allSlides = $slider.find('div.mediaholder').length - 1; // index start from 0

        function setPrevNext(){

          if(currentSlide == allSlides ){
            $next.hide();
          }else{
            $next.show();
          }
          if(currentSlide < 1){
            $prev.hide();
          }else{
            $prev.show();
          }
        }

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
          setPrevNext();
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
          setPrevNext();
        }

        $next.on('click', nextSlide);
        $prev.on('click', prevSlide);

        $('#nextmedia,#prevmedia').show();
        $slider.find('div').eq(0).addClass('active').show();

        $prev.hide();

       }else{

         $('#nextmedia,#prevmedia').hide();

       }
    }
  }


  function posPrevNext(){

    $('.prevnextnav').hide();
    setTimeout(function(){
      $('.prevnextnav').width( $('body').find('.mediaholder.active').outerWidth() );
      $('.prevnextnav').fadeIn();
    },300);

  }





  function getobject(){

    var selected_id = $('#objectcontainer').data('id');
    var selected_type = $('#objectcontainer').data('type');
    var data = {
      nonce: ajax.nonce,
      action: 'object_view',
      dataType: 'json', // Choosing a JSON datatype
      id: selected_id
    };


    jQuery.ajax({

      type: "GET",
      url: ajax.url,
      data: data,
      success: function(json) {

        var p = json.data.postdata;
        var html = '<div class="popcontainer ' + p.slug + '">' +
          '<div class="prevnextnav"><div id="prevmedia"><span>Prev</span></div><div id="nextmedia"><span>Next</span></div></div>'+
          '<div class="mediabox">'+
          '<div class="cover '+ p.orientation +'"><img src="'+ p.image +'" class="wp-post-image" alt="" />'+
          '<div class="title"><h1>' + p.title + '<h1></div><div class="text">' + p.content + '</div></div>'+
          '</div>';

        var bundle = json.data.postmedia;
        var objectmedia = '';

        $('#typemenu ul li:not(#menubutton)').each(function(c, el) {

          var countmedia = 0;
          var mediabox = '';

          mediabox += '<div class="mediacontainer '+$(el).data('type')+'">';

          $.each(bundle, function(i, media) {
            if (media.type_slug === $(el).data('type')) {
              countmedia++;
              var file = media.src;
              var extension = file.substr( (file.lastIndexOf('.') +1) );

              mediabox += '<div class="mediaholder '+media.type_name+'" data-order="'+media.order+'">';
              switch(extension) {
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                  mediabox += '<img class="embed zoom" src="'+media.src+'" alt="'+media.alt+'" data-desc="'+media.desc+'" data-caption="'+media.caption+'" width="600" height="auto" />';
                  break;
                case 'mp4':
                case 'mp3':
                  mediabox += '<video class="embed" src="'+media.src+'" data-desc="'+media.desc+'" data-caption="'+media.caption+'" width="600" height="350" controls></video>';
                  break;
                case 'pdf':
                  mediabox += '<iframe class="embed" src="'+media.src+'#toolbar=0" data-desc="'+media.desc+'" data-caption="'+media.caption+'" width="100%" height="640px">'+
                  '<p>It appears you do not have a PDF plugin for this browser.<a href="'+media.src+'">click here to download the PDF file.</a></p>'+
                  '</iframe>';//'</object>';
                  break;
                case 'doc':
                case 'docx':
                  //mediabox += '<iframe src="'+media.src+'" width="600" height="350"></iframe>';
                  mediabox += '<a class="media-link" href="'+media.src+'" title="'+media.title+'" data-desc="'+media.desc+'" data-caption="'+media.caption+'">'+media.title+'</a>';
                  break;
                default:
                mediabox += '<a class="media-link" href="'+media.src+'" title="'+media.title+'" data-desc="'+media.desc+'" data-caption="'+media.caption+'">'+media.title+'</a>';

              }
              mediabox += '<div class="caption">'+media.caption+'</div>';
              mediabox += '</div>';
              // title,excerpt,src,type_parent,type_slug,type_name
            }
          });
          mediabox += '</div>';

          if (countmedia > 0) { // also active in type menu
            objectmedia += mediabox;
          }

        });

        html += '<div class="object-media">'+objectmedia+'</div>';
        activeOverlay(html,selected_type);

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

    if( $('#loopcontainer.isotope').length > 0 ){
      getCollectionData();
      let type = 'foto';
      let queryString = window.location.search;
      let urlParams = new URLSearchParams(queryString);
      let qtype = urlParams.get('type');
      if( qtype != ''){
        type = qtype;
      }
      $('body').find('#typemenu ul.collection-types li.but-'+type).addClass('selected');
    }

    setTypeMenu();

    if( $('#objectcontainer').length > 0  && $('#objectcontainer').data('id') ){
      $('#isotopemenu').hide();
      getobject();
    }



  });

  // onscroll load more
  $(document).on('scroll', function() {

    var scrollHeight = $(document).height();
    var scrollPosition = $(window).height() + $(window).scrollTop();
    if ((scrollHeight - scrollPosition) / scrollHeight === 0) {
      if( $('#loopcontainer.isotope').length > 0 &&
      !$('body').find('#infoboxcontainer,#overlaycontainer').length ){
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
      setColumnWidth();
  }



  // grid / list toggle
  $(document).on('click touchend', '#display-toggle a', function(e){
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
  /*
  $(document).on('click touchend', '#typemenu .reset', function(e){
      e.preventDefault();
      $('body').find('#typemenu ul.collection-types li.but-foto').trigger('click');
  });
*/
  // orderby select
  $(document).on('click touchstart', '#display-options ul.orderby li', function(e){
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
    $('#loopcontainer .post-object').remove();
    getCollectionData();
  });

  // order select
  $(document).on('click touchend', '#display-options ul.order li', function(e){
    $('#loopcontainer').attr('data-order', $(this).data('order') );
    $('#display-options ul.order li').removeClass('selected');
    $(this).addClass('selected');
    pullpage = 0; // starts onload
    pullflag = true;
    $('#loopcontainer .post-object').remove();
    getCollectionData();
  });

  $(document).on('click touchend', '#show-recent', function(e){
    $('#loopcontainer').attr('data-orderby', 'date' );
    $('#loopcontainer').attr('data-order', 'DESC' );
    pullpage = 0; // starts onload
    pullflag = true;
    $('#loopcontainer .post-object').remove();
    $('#overlaycontainer .closeoverlay').trigger('click');
    getCollectionData();
  });




  // action type taxonomy menu
  $('body').on('click touchend', '#typemenu ul.collection-types li.available', function() {

    var type = $(this).data('type');
    var butclass = '.' + type;
    var butname = '.but-' + type;

    $('#typemenu ul li').removeClass('selected');
    $('#typemenu ul li'+butname).addClass('selected');

    filters = butclass;
    /*
    if( type == defaultselect ){
      $('#typemenu .reset').fadeOut();
    }else{
      $('#typemenu .reset').fadeIn();
    }*/
    $('#loopcontainer,#objectcontainer').attr('data-type', type);


    let url = new window.URL( $('#loopcontainer').data('homeurl') );
    url.searchParams.set("type", type);
    history.pushState('', '', url);

    setColumnWidth();
  });

  // action type post menu
  $('body').on('click touchend', '#typemenu ul.object-types li.available', function() {
    //alert( $(this).data('type') );
    var picturebox = $( '.popcontainer .mediabox .cover');
    let container = $('.popcontainer .mediabox');
    let type = $(this).data('type');

    container.html( $('.object-media .mediacontainer.'+ type ).clone() );
    container.prepend(picturebox);
    container.addClass('display');

    setMediaboxSlider( type );

    $('#typemenu ul.object-types li').removeClass('selected');
    $('#typemenu ul.object-types li.but-'+type).addClass('selected');

    $('body').find('.popcontainer').addClass(type);

    $('.media-icon').removeClass('selected');
    $('.media-icon.but-'+type).addClass('selected');


    $('#loopcontainer,#objectcontainer').attr('data-type', type);

    // set object nav
    var butclass = '.' + type;
    filters = butclass;
    setColumnWidth();

    var url = new window.URL(document.location);
    url.searchParams.set("type", type);
    history.pushState('', '', url);

  });


  // click url vs popup overlay object
  $(document).on('click touchend', '.post-object .overlay', function(event) {
      if( moved == false){

        let type = $('#typemenu .icon-button.selected').data('type');
        let link = $(this).find('.entry-title a').attr('href');

        if( !$(this).closest('.post-object').hasClass(type)){
          type = 'foto';
        }
        link = link + '?type='+type;

        //let url = link + '?type='+$('#loopcontainer').data('type');
        //$(this).find('.entry-title a').attr('href', url);
        window.location = link;
        //$(this).find('.entry-title a')[0].click();
      }
  });



  /*
  $(document).on('click touchend', '.post-object .overlay, .entry-title a,.item-icons ul li', function(event) {

    event.preventDefault();

    selected_id = $(this).parent().closest('.post-object').data('id');
    selected_slug = $(this).parent().closest('.post-object').data('slug');

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
  */

  $(document).on('click touchstart', '#infoboxcontainer.helpbox,.closeoverlay,.skippintro', function(event) {
    event.preventDefault();
    history.pushState("", document.title, window.location.pathname);
    closeOverlay(); //history.go(-1);
  });

  $(document).on('click touchstart', '#helpinfo', function(event) {
    var helpcontent = '<div id="helpcontent"><h3>Help</h3><p>Beweeg met je muis over een foto.<br/>Klik vervolgens op een van de iconen.</p><p>Wilt u terug naar het hoofdmenu? Klik op Home.</p></div>';
    activeInfobox( helpcontent , 'helpbox')
  });

  // hash events
  /*
  $(window).bind('hashchange', function(e) {

    var pagehash = window.location.hash;
    var hash = pagehash.replace('#', '');

    if (hash == '') {
      // check popups to close
      if ($('#overlaycontainer').length) {
        closeOverlay();
      }
      if ($('#infoboxcontainer').length) {
        //closeInfobox();
      }

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
        getobject();
      }

    }

  });
  */

  $(window).load(function() {

    //var pagehash = window.location.hash;
    //var hash = pagehash.replace('#','');
    //if( $('body.home').length && hash == '' ){

      if( $('body.home').length > 0 ){// must be homepage

      // check cookie
      var chk = getCookie("firsttime");
      if (chk == "" ){
      // firsttime
      setCookie();
        setTimeout( function(){
          $('html, body').stop().animate({
                  'scrollTop': $('#content').offset().top
            }, 800, 'swing',
            function () {
              var content = '<video controls autoplay width="640" height="480"><source type="video/mp4" src="wp-content/uploads/2021/10/intro_de_hoekse_schatkist.mp4"></video><div class="skippintro"><span>Introductie overslaan</span></div>';
              activeInfobox( content, 'intro');
            });

        }, 300);

      } //console.log(document.cookie);

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

      /*if ($('#overlaycontainer').length > 0) {
        $('#overlaycontainer').fadeOut(100, function() {
          $(this).remove();
        });
      }*/
      if ($('#infoboxcontainer').length < 1) {
        $('<div id="infoboxcontainer" class="'+popclass+'"><div class="closeinfobox"><span>close</span></div><div class="outermargin"></div></div>').hide().appendTo($('header'));
      }
      //content = infoboxTemplate( content );
      $('#infoboxcontainer .outermargin').html(content);
      $('.closeinfobox').prependTo( $('#infoboxcontainer .outermargin') );

      $('#infoboxcontainer').fadeIn(200);
      //$('#loopcontainer').fadeOut(200);
  }


    function closeInfobox(){

      //if( $('#infoboxcontainer').hasClass('collection') ){

        $('html, body').stop().animate({
              'scrollTop': $('body').offset().top
          }, 800, 'swing' );

      //}
      $('#infoboxcontainer').fadeOut(200, function() {
        $(this).remove();
      });

    }

    $(document).on('click touchstart', '.closeinfobox', function(event) {

        event.preventDefault();
        //if( $(this).parent().hasClass('intro') ){
          //collectionInfobox();
        //}else{
          closeInfobox();
        //}

    });

    $(document).on('click touchend', '#infoboxcontainer.intro', function(event) {
          if(!$(event.target).is('#infoboxcontainer .outermargin, #infoboxcontainer .outermargin li a, video, video source, #infoboxcontainer .outermargin a')){
            event.preventDefault();
            //collectionInfobox();
            closeInfobox();
          }
    });

});
