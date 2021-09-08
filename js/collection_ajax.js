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

      if( $('#loopcontainer.isotope').length ) {

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

    // type taxonomy menu
    function setTypeMenu() {
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
        $(window).trigger( 'hashchange' );

    }

    // on start get data and set taxonomy menu
    $(window).ready(function() {

        getCollectionData();
        $('#primarymenubox').before( $('#typemenu') );
        if( $('#categorymenu').length ){
          $('#primarymenubox').before( $('#categorymenu') );
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
       setColumnWidth();
     }

     // action type taxonomy menu
     $('#typemenu ul').on('click', 'li.available', function(){

       var type = $(this).data('type');
       var butclass = '.'+type;
       var butname = '.but-'+type;

       if( $(this).data('type') != 'foto' && $.inArray( butclass, filterlist ) < 0){ // remove default foto
         if( $.inArray( '.foto', filterlist ) >= 0){
           filterlist.splice( $.inArray( '.foto', filterlist ), 1 );
           $('#typemenu ul li.but-foto').removeClass('selected');
         }
       }

       if( $.inArray( butclass, filterlist ) >= 0){
         filterlist.splice( $.inArray( butclass, filterlist ), 1 );
         $(this).removeClass('selected');
       }else{
         filterlist.push(butclass);
         $(this).addClass('selected');
       }

       if(filterlist.length < 1){

         filters = '.'+defaultselect,
         filterlist = Array(filters);
         $('#typemenu ul li.but-'+defaultselect).addClass('selected');

       }else{
         filters = filterlist.join(","); // = or/or .. and/and :: filterlist.join(",");
       }
       setColumnWidth();

     });

     // popup overlay artifact
     $(document).on( 'click', '.post-artifact .overlay, .entry-title a,.item-icons ul li', function( event ) {

       event.preventDefault();
       var pid = $(this).parent().closest('.post-artifact').data('id');
       var mtype = 'foto';

       if( $(this).hasClass('icon-button') ){
         mtype = $(this).data('type');
       }
       var data = {
         action: 'artifact_view',
         id: pid
       };

       $.getJSON(ajax_data.ajaxurl, data, function(json) {
        if (json.success) {
          var p = json.data.postdata;

          window.location.hash = p.slug;

          var html = '<div id="'+p.slug+'" class="popcontainer">' +
            '<div class="mediabox ' + p.orientation + '"><img src="' + p.image + '" class="wp-post-image" alt="" /></div>' +
            '<div class="contentbox"><div class="column">' +
            '<div class="text">' + p.excerpt + '</div>' +
            '</div>';

          var bundle = json.data.postmedia;
          $('#typemenu ul li:not(#menubutton)').each(function(c, el) {
            var countmedia = 0;
            html += '<div class="column">';

            html += $(el).find('span').text();
            $.each(bundle, function(i, media) {
              if (media.type_slug === $(el).data('type')) {
                countmedia++;
                //html += media.title;
              }
            });

            html += '(' + countmedia + ')';
            html += '</div>';

          });
          html += '</div>';

          activeOverlay(html);

        } else {
          // error
        }

        });

      });

      function activeOverlay(content) {
          if ($('#infoboxcontainer').length > 0) {
            $('#infoboxcontainer').fadeOut(100, function() {
              $(this).remove();
            });
          }
          if ($('#overlaycontainer').length < 1) {
            $('<div id="overlaycontainer"><div class="closeoverlay"></div><div class="outermargin"></div></div>').hide().appendTo($('#loopcontainer').parent());
          }
          $('#overlaycontainer .outermargin').html(content);

          $('#overlaycontainer').fadeIn(200);
          $('#loopcontainer').fadeOut(200);
      }

      function closeOverlay(){
        $('#overlaycontainer').removeClass('intro');
        $('#overlaycontainer').fadeOut(200, function() {
          $(this).remove();
        });
        $('#loopcontainer').fadeIn(200);
      }

      $(document).on('click', '.closeoverlay', function(event) {
          event.preventDefault();
          history.go(-1);
      });
      
      // hash events
      $(window).bind( 'hashchange', function(e) {

        var hash = window.location.hash.replace('#','');

        if( location.hash == '' ){
          // check popups to close
          if( $('#overlaycontainer').length ){
            closeOverlay();
          }
          if( $('#infoboxcontainer').length ){
            closeInfobox();
          }

        }else{
          console.log(hash);
          // check specific popups to close
          if( $('#overlaycontainer').length && $('#'+hash ).length < 1 ){
            closeOverlay();
          }
          if( $('#infoboxcontainer').length && $('#'+hash ).length < 1){
            closeInfobox();
          }
          if( $("#loopcontainer").find("div[data-slug='" + hash + "']").length ){
            $("#loopcontainer").find("div[data-slug='" + hash + "'] .overlay").trigger('click');
          }

        }

      });

      $( window ).load(function() {

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
