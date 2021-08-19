// load ajax
jQuery(function($) {

let pull_page = 0; // starts onload
let jsonFlag = true;

function collection_loader(){

  if($('#loopcontainer').length && $('#loopcontainer-loader').length){

    if(jsonFlag){

      jsonFlag = false;
      pull_page++;

      let slug = $('#loopcontainer').data('collection');//'chateau-du-lac';
      let amount = $('#loopcontainer').data('loadamount');//'chateau-du-lac';

      $.getJSON("wp-json/artifacts/all-posts?page=" + pull_page + "&collection=" + slug+ "&load_amount=" + amount, function(data){

        $('#loopcontainer-loader').show();

        if(data.length){
          var items = [];
          $.each(data, function(key, val){
            items.push(val.html);
          });
          $("#loopcontainer").append(items);

          if(data.length >= amount){
            // there will be more to load
          }
        }

      }).done(function(data){
        if(data.length){
          jsonFlag = true;
          $('#loopcontainer-loader').hide(); // hide if also on scroll load
        }
      });

    } //end isflag
  }
}

$(window).ready(function() {
  collection_loader();
});

$(document).on('scroll', function(){
  var scrollHeight = $(document).height();
	var scrollPosition = $(window).height() + $(window).scrollTop();
	if ((scrollHeight - scrollPosition) / scrollHeight === 0) {
    collection_loader();
  }
});

$(document).on('click', '#artifact-loader', function(){
  collection_loader();
});


});
