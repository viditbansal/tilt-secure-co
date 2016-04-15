(function($) {
    $(function() {
        var jcarousel = $('.jcarousel');

        jcarousel
            .on('jcarousel:reload jcarousel:create', function () {
                var width = jcarousel.innerWidth();
				var total_items = jcarousel.jcarousel('items').length;
				
                if (width >= 600) {
                    width = (width+10) / 6;
					if(total_items < 6)
						$('.jcarousel-control-prev, .jcarousel-control-next, .jcarousel-pagination').hide();						
                } else if (width >= 350) {
                    width = width / 3;
					if(total_items < 3)
						$('.jcarousel-control-prev, .jcarousel-control-next, .jcarousel-pagination').hide();
                }	else if (width >= 320) {
                    width = width / 2;
					if(total_items < 2)
						$('.jcarousel-control-prev, .jcarousel-control-next, .jcarousel-pagination').hide();
                }
				
			  $('.jcarousel ul').css('width',total_items*width + 'px');			  
              jcarousel.jcarousel('items').css('width', width + 'px');
						
            })
			
            .jcarousel({
                wrap: 'circular'
            });

        $('.jcarousel-control-prev')
            .jcarouselControl({
                target: '-=1'
            });

        $('.jcarousel-control-next')
            .jcarouselControl({
                target: '+=1'
            });

        $('.jcarousel-pagination')
            .on('jcarouselpagination:active', 'a', function() {
                $(this).addClass('active');
            })
            .on('jcarouselpagination:inactive', 'a', function() {
                $(this).removeClass('active');
            })
            .on('click', function(e) {
                e.preventDefault();
            })			
            .jcarouselPagination({
                perPage: 1,
                item: function(page) {
                    return '<a href="#' + page + '">' + page + '</a>';
                }
            });
    });
})(jQuery);
