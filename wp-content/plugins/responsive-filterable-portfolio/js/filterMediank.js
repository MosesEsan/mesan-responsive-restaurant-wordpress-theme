(function($) {
	
    
   $.fn.filterMediank = function( options ) {
    
   
       var thisElement=this;
        // This is the easiest way to have default options.
        var settings = $.extend({
            thumbsSpacing:15,
            backgroundColor: "white",
            galleryId:"gallery",
            thumbWidth: 200,
            thumbHeight: 200
        }, options );
 
         
    
	var thumbsSpacing = settings.thumbsSpacing;
        var galleryId_=settings.galleryId;
        
        $("#"+galleryId_).css('backgroundColor',settings.backgroundColor);
	$(thisElement).css('margin-bottom', thumbsSpacing + 'px');
	$("#"+galleryId_).find('div.photos div.thumbnail_wrap_ a.thumbnail_').addClass('showThumb');

	$(thisElement).find('a.sortLink').on('click', function(e) {
            
                $("#shield_"+galleryId_).height($("#shield_"+galleryId_).parent().height());
		e.preventDefault();
		$(thisElement).find('a.sortLink').removeClass('selected');
		$(e.target).addClass('selected');

		var category = $(e.target).data('category');
                $.when( filterThumbs(category,galleryId_) ).done(function() {
                    
                    setTimeout(function(){
                        $("#shield_"+galleryId_).height(0); 
                    }, 300);

                       
                        
                 });
		//positionThumbs(galleryId_);
	});

	positionThumbs(galleryId_);
        
        
	function filterThumbs(category,gallId) {
		
		$("#"+gallId+' a.thumbnail_').each(function() {
			var thumbCategory = $(this).data('categories');

			if ( category === 'all' ) {
				$(this).addClass('showThumb').removeClass('hideThumb').attr('rel', 'group');
			} else {
				if ( thumbCategory.indexOf(category) !== -1 ) {
					$(this).addClass('showThumb').removeClass('hideThumb').attr('rel', 'group');
				} else {
                                        
					$(this).addClass('hideThumb').removeClass('showThumb').attr('rel', 'none');
				}
			}
		});
      
    

		positionThumbs(gallId);
                
               

	}

	function positionThumbs(gallId) {

        
		$("#"+gallId +' div.photos div.thumbnail_wrap_ a.thumbnail_.hideThumb').animate({
			'opacity': 0
		}, 500, function() {
			$(this).css({
				'display': 'none',
				'top': '0px',
				'left': '0px'
			});
		});

                var container_Width = $("#"+gallId+' div.photos').width();

               // var otrwidth=$("#"+gallId+' .thumbnail_ img:first-child').outerWidth();
                //var otrheight=$("#"+gallId+' .thumbnail_ img:first-child').outerHeight();
                //$("#"+gallId+' .thumbnail_ ').css('position','relative');
           
                var thumbRow = 0,
                thumbColumn = 0;
                
               // thumbWidth = ((otrwidth)===0 ? settings.thumbWidth : otrwidth )+ thumbsSpacing,
                //thumbHeight = ((otrheight)===0 ? settings.thumbHeight : otrheight) + thumbsSpacing,
                if($("#"+gallId).width()<settings.thumbWidth ){
                   
               
                   var ratio = (settings.thumbHeight+thumbsSpacing) / (settings.thumbWidth+thumbsSpacing);
                   var height = $("#"+gallId).width() * ratio;
                  

                     var thumbWidth = ($("#"+gallId).width()-10)- thumbsSpacing;
                      $("#"+gallId+' .thumbnail_ img').css('maxWidth',thumbWidth);
                      $("#"+gallId+' .thumbnail_ img').css('maxHeight',height);
                     
                     var thumbHeight= height;
                    
                    
                     //var thumbHeight = ((settings.thumbHeight * $("#"+gallId).width()+thumbsSpacing) / (settings.thumbWidth-thumbsSpacing));
                     
                  
                    // $('.thum_div img').css('maxHeight',thumbHeight);
                  
                     //var thumbHeight = settings.thumbHeight+ thumbsSpacing;
                     
                }
                else{
                     var thumbWidth = settings.thumbWidth + thumbsSpacing;
                     var thumbHeight = settings.thumbHeight+ thumbsSpacing; 
                      $("#"+gallId+' .thumbnail_ img').css('maxWidth',thumbWidth);
                      $("#"+gallId+' .thumbnail_ img').css('maxHeight',thumbHeight);
                }
                      
                maxColumns = Math.floor( container_Width / thumbWidth );
                if(maxColumns==0){
                    maxColumns=1;
                }

		$("#"+gallId+' a.thumbnail_.showThumb').each(function(index){
			var remainder = ( index%maxColumns ) / 100,
				maxIndex = 0;
                                
			if( remainder === 0 ) {
				if( index !== 0 ) {
					thumbRow += thumbHeight;
				}
				thumbColumn = 0;
			} else {
				thumbColumn += thumbWidth;
			}
                        
                        
			$(this).css('display', 'block').animate({
				'opacity': 1,
				'top': thumbRow + 'px',
				'left': thumbColumn + 'px'
			}, 500);

			var newWidth = thumbColumn + thumbWidth,
				newHeight = thumbRow + thumbHeight;
			$("#"+gallId +' div.photos .thumbnail_wrap_').css({
				'width': newWidth + 'px',
				'height': newHeight + 'px'
			});
		});

		
	}
       
	
        
        return {
            resizeWin: function(gallId) {

                     positionThumbs(gallId);
                 }
          }

    };
})(jQuery);