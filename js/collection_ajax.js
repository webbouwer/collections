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




  /* AJAX Function */
    let pullpage = 0; // starts onload
    let pullflag = true;

    function getCollectionData() {

      if( $('#loopcontainer.isotope').length ) {

        if (pullflag) {

          //$('#loopcontainer-loader').fadeIn(200);

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

          //$('#loopcontainer-loader').fadeOut(200);
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



    /* load isotope */
    function setColumnWidth() {

      var w = container.width();
      /* TODO: check width for small screens .. */
      colWidth = w / 4;

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

    $(window).ready(function() {
        getCollectionData();
    });

    $(document).on('scroll', function() {
       var scrollHeight = $(document).height();
       var scrollPosition = $(window).height() + $(window).scrollTop();
       if ((scrollHeight - scrollPosition) / scrollHeight === 0) {
         getCollectionData();
       }
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

});
