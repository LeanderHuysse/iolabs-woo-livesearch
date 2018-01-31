var form = jQuery('.search-field').parents('.search-form')[0];

var IO_LiveSearch = {
  onReady: function() {
		jQuery('.search-field').attr('autocomplete', 'off');
  },
  getResults: function(searchTerm) {
  	data = {
  		'searchTerm': searchTerm.val(),
  		'action': 'list_items'
		};
		jQuery.post(ioWooSearch.ajax_root, data, function(response) {
      IO_LiveSearch.render(response, searchTerm.val());
		}, 'json');
  },
	render: function(data, searchTerm) {

  	if(jQuery('.io-search-results').length == 0) {
      jQuery(form).append(jQuery("<div class='io-search-results'></div>"));
      var height = jQuery(form).height();
      var width = jQuery(form).width() * 2.2;
      jQuery('.io-search-results').css({'top': height, 'width': width});
		}

		if(data.success) {
      jQuery('.io-search-results').empty();
      var i = 0;
      jQuery.each(data.data.products, function (key, value) {
        i++;
        if(i > 6) {
          return false;
        }
        var row = jQuery([
          '<a href="' + value.permalink + '">',
          '<div class="io-row">',
          '<div class="io-thumb">',
          '<img src="' + value.image + '">',
          '</div>',
          '<div class="io-content">',
          '<h3>' + value.author_name + '</h3>',
          '<span>' + value.post_title + '</span>',
          '</div>',
          '</div>',
          '</a>'
        ].join("\n"));
        jQuery('.io-search-results').append(row);
        row.find('.io-row').delay(100 * i).animate({ 'opacity': 1 }, 300);
      });
      if ( data.data.count > 6 ) {
        var row = jQuery([
          '<a class="io-allresults" href="/all-results?term=' + encodeURIComponent(searchTerm) + '">',
          'View all results',
          '</a>'
        ].join("\n"));
        jQuery('.io-search-results').append(row);
      }
    } else {
      jQuery('.io-search-results').empty();
      var row = jQuery([
        '<span class="io-allresults">',
        'No results found',
        '</span>'
      ].join("\n"));
      jQuery('.io-search-results').append(row);
    }
	},
	showLoading: function()
	{
    if(jQuery('.io-search-results').length == 0) {
			jQuery(form).append(jQuery("<div class='io-search-results'><img src='" + ioWooSearch.url + "/public/img/loading.gif' class='loadingIndicator'></div>"));
      var height = jQuery(form).height();
      var width = jQuery(form).width() * 1.5;
      jQuery('.io-search-results').css({'top': height, 'width': width, 'display': 'none'});
      jQuery('.io-search-results').slideDown();
    } else {
      jQuery('.io-search-results').empty();
      jQuery('.io-search-results').append(jQuery("<div class='io-search-results'><img src='" + ioWooSearch.url + "/public/img/loading.gif' class='loadingIndicator'></div>"));
		}
	}
};

(function( $ ) {
	'use strict';
	IO_LiveSearch.onReady();

	var searchObject;
	var i = _.debounce(function(searchObject) { IO_LiveSearch.getResults(searchObject) }, 1200);
	$(document).ready(function() {
    $('.search-field').keyup(function(event) {
      searchObject = $(this);

      if(event.keyCode >= 65 && event.keyCode <= 90 || event.keyCode >= 48 && event.keyCode <= 57) {
        if($(this).val().length > 2) {
          IO_LiveSearch.showLoading();
          i(searchObject);
        }
			}

      if($(this).val().length === 0) {
        $('.io-search-results').empty();
      }
    });

    $('.search-field').on('focus', function() {
    	if($('.io-search-results').length > 0) {
        $('.io-search-results').slideDown();
			}
		});

    $('body').not('.search-field').click(function(event) {
    	if(!$(event.target).is('.search-field, .io-search-results')) {
        $('.io-search-results').slideUp();
			}
		});

    $('.search-field').on('keypress', function(e) {
      if (e.keyCode == 13) {
        return false;
      }
    })

	});
})( jQuery );
