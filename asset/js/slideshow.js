(function($) {
    $(document).ready(function() {

      $('.slideshow').on('fullscreenchange', function(e) {
        let activeSlideshow = $('.slideshow.fullscreen-enabled')
        if (document.fullscreenElement === null) {
          closeFullscreen(activeSlideshow);
        }
      });

      $('.slideshow').on('click', '.slide-fullscreen-openBtn', function() {
        openFullscreen($(this).closest('.slideshow'));
      })

      $('.slideshow').on('click', '.slide-fullscreen-closeBtn', function() {
        closeFullscreen($(this).closest('.slideshow'));
      })
      
        $('.slideshow').each(function(){
          if ($(this).find('.item').length < 1) return;
          if ($(this).find('.item').length > 1) {
            $(this).not('.slick-initalized').slick(
                {
                  slidesToShow: 1,
                  slidesToScroll: 1,
                  autoplay: true,
                  autoplaySpeed: 8000,
                  dots: true,
                  adaptiveHeight: false,
                  prevArrow: "<div class='slick-prev'></div>",
                  nextArrow: "<div class='slick-next'></div>",
                  accessibility: true,
                  focusOnSelect: true,
                  fade: true,
                  cssEase: 'linear',
              }
            );
          }
          else {
            $(this).not('.slick-initalized').slick(
              {
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: false,
                autoplaySpeed: 8000,
                dots: true,
                adaptiveHeight: false,
                prevArrow: "<div class='slick-prev'></div>",
                nextArrow: "<div class='slick-next'></div>",
                accessibility: true,
                focusOnSelect: true,
                fade: true,
                cssEase: 'linear',
            }
          );
          }
        });

        $('.slideshow-with-audio').each(function(){
          var slideshow = $(this).find('.slideshow');
          var audioplayer = $(this).find('audio');
          audioplayer.attr('loop',true);
          
          var textplaybtn = $(this).find('.audio-text-play-control');
          
          if (textplaybtn.length>0) {
            var playmsg = textplaybtn.data('playbtnmsg');
            var stopmsg = textplaybtn.data('stopbtnmsg');
            textplaybtn.data('state','stopped').css('cursor','pointer');
            
            textplaybtn.on('click',function(e) {
              e.preventDefault();
              
              if ($(this).data('state') == 'stopped') {
                audioplayer[0].play();
                slideshow.slick('slickPlay');
                $(this)
                  .html(stopmsg)
                  .data('state','playing');
              } else {
                slideshow.slick('slickPause');
                audioplayer[0].pause();
                $(this)
                  .html(playmsg)
                  .data('state','playing');
              }
            });
            
          }
        });
        
        
        
        if ($('.slideshow').length > 0){
          var slideshow =  $('.slideshow');
            //fullscreen button, it checks for then adds a btn into dom with click event for fullscreen styling
            $(slideshow).append("<span class='fullscreen-wrapper'><button class='slide-fullscreen-openBtn'><span class='fullscreen-label'>View in <br> Full Screen</span></button></span>");
    
            // Adds a .navHover class to the slideshow to assist UI styling
            
            $('.slick-arrow').each(function(){
              $(this)
                .on('mouseenter',function(){
                  slideshow.addClass('navHover');
                })
                .on('mouseleave',function(){
                  slideshow.removeClass('navHover');
                })
              
            });
        }

        $.each(['#homepage-splash', '.section-intro-splash'], function(idx, val) {
          if ($(val).find('.items .item').length > 1) {
            $(val).find('.items').not('.slick-initalized').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 8000,
                infinite: true,
                fade: true,
                cssEase: 'linear',
                dots: true,
                arrows: true,
                prevArrow: "<div class='slick-prev'></div>",
                nextArrow: "<div class='slick-next'></div>",
                accessibility: true,
            });
          }
          else {
            $(val).find('.items').addClass('single-carousel');
            $(val).find('.items').not('.slick-initalized').slick({
              slidesToShow: 1,
              slidesToScroll: 1,
              autoplay: false,
              autoplaySpeed: 8000,
              infinite: true,
              fade: true,
              cssEase: 'linear',
              dots: true,
              arrows: true,
              prevArrow: "<div class='slick-prev'></div>",
              nextArrow: "<div class='slick-next'></div>",
              accessibility: true,
          });
        }
      });
    });
})(jQuery);

//onlick function for fullscreen
function openFullscreen(elem){
  elem.addClass('fullscreen-enabled');
  elem.append("<button class='slide-fullscreen-closeBtn'>X</button>");
  elem.find('.slide-fullscreen-openBtn').remove();
  if (elem.get(0).requestFullscreen) {
    elem.get(0).requestFullscreen();
  } else if (elem.mozRequestFullScreen) { /* Firefox */
    elem.get(0).mozRequestFullScreen();
  } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
    elem.get(0).webkitRequestFullscreen();
  } else if (elem.msRequestFullscreen) { /* IE/Edge */
    elem.get(0).msRequestFullscreen();
  }
  $('body').addClass('slideshow-fullscreen');
}

/* Close fullscreen */
function closeFullscreen(elem) {

  if (elem.hasClass('fullscreen-enabled')) {
    elem.find('.fullscreen-wrapper').append("<button class='slide-fullscreen-openBtn'><span class='fullscreen-label'>View in <br> Full Screen</span></button>");
  }
  elem.removeClass('fullscreen-enabled')
  elem.find('.slide-fullscreen-closeBtn').remove();
  if (document.fullscreenElement !== null) {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.mozCancelFullScreen) { /* Firefox */
      document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
      document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) { /* IE/Edge */
      document.msExitFullscreen();
    }
  }
  $('body').removeClass('slideshow-fullscreen');
}
