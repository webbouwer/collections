// load ajax
jQuery(function($) {

let pull_page = 0; // starts onload
let jsonFlag = true;

function collection_loader(){

const fromBottom = $(window).scrollTop() + $(window).height();

if($('#artifact-list').length && $('#artifact-loader').length){

  const loader_height = $('#artifact-loader').offset().top + $('#artifact-loader').height();

  $('#artifact-loader').show();

  if(jsonFlag){

  jsonFlag = false;
  pull_page++;

  let collection_slug = $('#artifact-list').data('collection');//'chateau-du-lac';
  let load_amount = $('#artifact-list').data('loadamount');//'chateau-du-lac';

  $.getJSON("wp-json/artifacts/all-posts?page=" + pull_page + "&collection=" + collection_slug+ "&load_amount=" + load_amount, function(data){
    if(data.length){
      var items = [];
      $.each(data, function(key, val){

        /*
        const arr = $.map(val, function(el) { return el });
        const valstring = JSON.stringify(val); //arr.toString();
        const arrstring = arr.toString(); //arr.toString();
        const post_url = arr[1];
        const post_title = arr[3];
        const post_img = arr[4];
        //const post_featured = arr[5];
        const post_cat = arr[5];

        //let featured = "";
        //if(post_featured){
        //	featured = "featured";
        //}
        */
        let item_string = '<h3><a href="'+ val.url +'">'+ val.title +'</a></h3>';
        items.push(item_string);
      });
      if(data.length >= load_amount){
        //$('li.loader').fadeOut();
        $("#artifact-list").append(items);
        $('#artifact-loader').hide(); // hide if also on scroll load
      }else{
        $("#artifact-list").append(items);
        $('#artifact-loader').hide();
      }
    }else{
      $('#artifact-loader').hide();
    }
  }).done(function(data){
    if(data.length){ jsonFlag = true; }
  });

  } //end isflag

}
}

$(window).ready(function() {
  collection_loader();
});

$(document).on('scroll', function(){
  collection_loader();
});

$(document).on('click', '#artifact-loader', function(){
  collection_loader();
});


});
