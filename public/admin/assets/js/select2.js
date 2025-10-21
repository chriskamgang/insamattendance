$(function() {
  'use strict'

  if ($(".js-example-basic-single").length) {
    $(".js-example-basic-single").select2({
      minimumResultsForSearch: Infinity
    });
    $(".js-example-basic-single2").select2({
      minimumResultsForSearch: Infinity
    });
  }
  if ($(".js-example-basic-multiple").length) {
    $(".js-example-basic-multiple").select2();
  }
  
  $(".js-tags-tokenizer").select2({
    tags: true,
    tokenSeparators: [',', ' ']
  })
});