(function($) {
    $(document).ready(function() {
      
        $('.slideshow').each(function(){
          if ($(this).find('.item').length < 1) return;
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
          var slideshow1 =  $('.slideshow')[0];
          var slideshow2 = $('.slideshow')[1];
            //fullscreen button, it checks for then adds a btn into dom with click event for fullscreen styling
            $(slideshow1).append("<span class='fullscreen-wrapper'><button class='slide-fullscreen-openBtn' onclick='openFullScreen()'><span class='fullscreen-label'>View in <br> Full Screen</span></button></span>");
            $(slideshow2).append("<span class='fullscreen-wrapper'><button class='slide-fullscreen-openBtn' onclick='openFullScreen2()'><span class='fullscreen-label'>View in <br> Full Screen</span></button></span>");

            var slideshow = $(slideshow1);
            var secondSlideshow = $(slideshow2);
    
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
            $('.slick-arrow').each(function(){
              $(this)
                .on('mouseenter',function(){
                  secondSlideshow.addClass('navHover');
                })
                .on('mouseleave',function(){
                  secondSlideshow.removeClass('navHover');
                })
              
            });
            
        }

        console.log($('.section-intro-splash').find('.item').length)
        $.each(['#homepage-splash', '.section-intro-splash'], function(idx, val) {
          console.log(val)
          console.log($(val).find('.items .item').length)
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
function openFullScreen(){
  var elem =  $('.slideshow')[0];
  $(elem).append("<button class='slide-fullscreen-closeBtn' onclick='closeFullscreen()'>X</button>");
  $('.slide-fullscreen-openBtn').remove();
  if (elem.requestFullscreen) {
    elem.requestFullscreen();
  } else if (elem.mozRequestFullScreen) { /* Firefox */
    elem.mozRequestFullScreen();
  } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
    elem.webkitRequestFullscreen();
  } else if (elem.msRequestFullscreen) { /* IE/Edge */
    elem.msRequestFullscreen();
  }
  $(document).keyup(function(e) {
    if (e.keyCode === 27) closeFullscreen();   // if esc is pressed to exit fullscreen
  });
}
function openFullScreen2(){
  var elem =  $('.slideshow')[1];
  $(elem).append("<button class='slide-fullscreen-closeBtn' onclick='closeFullscreen()'>X</button>");
  $('.slide-fullscreen-openBtn').remove();
  if (elem.requestFullscreen) {
    elem.requestFullscreen();
  } else if (elem.mozRequestFullScreen) { /* Firefox */
    elem.mozRequestFullScreen();
  } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
    elem.webkitRequestFullscreen();
  } else if (elem.msRequestFullscreen) { /* IE/Edge */
    elem.msRequestFullscreen();
  }
  $(document).keyup(function(e) {
    if (e.keyCode === 27) closeFullscreen();   // if esc is pressed to exit fullscreen
  });
}
/* Close fullscreen */
function closeFullscreen() {
  var slideshow =  $('.slideshow')[0];
  var slideshow2 =  $('.slideshow')[1];
  $(slideshow).append("<button class='slide-fullscreen-openBtn' onclick='openFullScreen()'><span class='fullscreen-label'>View in <br> Full Screen</span></button>");
  $(slideshow2).append("<button class='slide-fullscreen-openBtn' onclick='openFullScreen2()'><span class='fullscreen-label'>View in <br> Full Screen</span></button>");
  $('.slide-fullscreen-closeBtn').remove();
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
