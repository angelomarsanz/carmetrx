!(function($) {
    "use strict";

    /*============================================
        Sticky header
    ============================================*/
    $(window).on("scroll", function () {
        var header = $(".header-area");
        // If window scroll down .is-sticky class will added to header
        if ($(window).scrollTop() >= 50) {
            header.addClass("is-sticky");
        } else {
            header.removeClass("is-sticky");
        }
    });


    /*============================================
        Image to background image
    ============================================*/
    var bgImage = $(".bg-img")
    bgImage.each(function () {
        var el = $(this),
            src = el.attr("data-bg-image");

        el.css({
            "background-image": "url(" + src + ")",
            "background-size": "cover",
            "background-position": "center",
            "display": "block"
        });
    });


    /*============================================
        Mobile Menu
    ============================================*/
    var mobileMenu = function () {
        // Variables
        var body = $("body"),
            mainNavbar = $(".main-navbar"),
            mobileNavbar = $(".mobile-menu"),
            cloneInto = $(".mobile-menu-wrapper"),
            cloneItem = $(".mobile-item"),
            menuToggler = $(".menu-toggler"),
            offCanvasMenu = $("#offcanvasMenu")

        menuToggler.on("click", function () {
            $(this).toggleClass("active");
            body.toggleClass("mobile-menu-active")
        })

        mainNavbar.find(cloneItem).clone(!0).appendTo(cloneInto);

        if (offCanvasMenu) {
            body.find(offCanvasMenu).clone(!0).appendTo(cloneInto);
        }

        mobileNavbar.find("li").each(function (index) {
            var toggleBtn = $(this).children(".toggle")
            toggleBtn.on("click", function (e) {
                $(this)
                    .parent("li")
                    .children("ul")
                    .stop(true, true)
                    .slideToggle(350);
                $(this).parent("li").toggleClass("show");
            })
        })

        // check browser width in real-time
        var checkBreakpoint = function () {
            var winWidth = window.innerWidth;
            if (winWidth <= 1199) {
                mainNavbar.hide();
                mobileNavbar.show()
            } else {
                mainNavbar.show();
                mobileNavbar.hide()
            }
        }
        checkBreakpoint();

        $(window).on('resize', function () {
            checkBreakpoint();
        });
    }
    mobileMenu();


    /*============================================
        Navlink active class
    ============================================*/
    var a = $("#mainMenu .nav-link"),
        c = window.location;

    for (var i = 0; i < a.length; i++) {
        const el = a[i];

        if (el.href == c) {
            el.classList.add("active");
        }
    }

    /*============================================
        Swiper Slider
    ============================================*/
    var sponsorSlider = new Swiper(".sponsor-slider", {
        speed: 1200,
        loop: true,
        spaceBetween: 30,
        slidesPerView: 4,
        autoplay: {
            delay: 3000,
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        breakpoints: {
            320: {
                slidesPerView: 1
            },
            400: {
                slidesPerView: 2
            },
            768: {
                slidesPerView: 3
            },
            1200: {
                slidesPerView: 4
            }
        }
    });

    // User Slider
    var userSlider = new Swiper(".user-slider", {
        speed: 1200,
        loop: true,
        spaceBetween: 40,
        slidesPerView: 2,
        autoplay: {
            delay: 3000,
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        breakpoints: {
            320: {
                slidesPerView: 1
            },
            768: {
                slidesPerView: 2
            },
            992: {
                slidesPerView: 3
            },
            1200: {
                slidesPerView: 3
            }
        }
    });

    // Testimonial Slider
    new Swiper(".testimonial-slider", {
        speed: 1200,
        spaceBetween: 15,
        slidesPerView: 1,
        loop: true,
        grabCursor: true,

        // Pagination bullets
        pagination: {
            el: "#testimonial-slider-pagination",
            clickable: true,
        },

        on: {
            init: function() {
                var pagination = $('#testimonial-slider-pagination'),
                    paginationLength = $('#testimonial-slider-pagination span'),
                    currentSlide = 1,
                    totalSlide = paginationLength.length.toString().padStart(2, '0')

                pagination.attr('data-min', '0'+ currentSlide);
                pagination.attr('data-max', totalSlide);

                // setSlideHeight(this);
            },
            // slideChangeTransitionEnd:function(){
            //     setSlideHeight(this);
            // }
        }
    });

    // function setSlideHeight(that){
    //     $('.testimonial-slider .swiper-slide').css({height:'auto'});
    //     var currentSlide = that.activeIndex;
    //     var newHeight = $(that.slides[currentSlide]).height();

    //     $('.testimonial-slider .swiper-wrapper, .testimonial-slider .swiper-slide').css({ height : newHeight })
    //     that.update();
    // }


    /*============================================
        Popup
    ============================================*/
    $(".youtube-popup").magnificPopup({
        disableOn: 300,
        type: "iframe",
        mainClass: "mfp-fade",
        removalDelay: 160,
        preloader: false,
        fixedContentPos: false
    })


    /*============================================
        Go to Top
    ============================================*/
    $(window).on("scroll", function () {
        // If window scroll down .active class will added to go-top
        var goTop = $(".go-top");
        if ($(window).scrollTop() >= 200) {
            goTop.addClass("active");
        } else {
            goTop.removeClass("active")
        }
    })
    $(".go-top").on("click", function (e) {
        $("html, body").animate({
            scrollTop: 0,
        }, 0);
    });

    /*============================================
        Lazyload image
    ============================================*/
    function lazyLoad() {
        window.lazySizesConfig = window.lazySizesConfig || {};
        window.lazySizesConfig.loadMode = 2;
        lazySizesConfig.preloadAfterLoad = true;
    }


    /*============================================
        Pricing toggle
    ============================================*/
    $(".pricing-list").each(function(i) {
        var list = $(this).children();
        if (list.length > 5) {
            this.insertAdjacentHTML('afterEnd', '<span class="show-more">Show More +</span>');
            const showLink = $(this).next(".show-more");
            list.slice(5).toggle(300);
            showLink.on("click", function() {
                list.slice(5).toggle(300);
                showLink.html(showLink.html() === "Show Less -" ? "Show More +" : "Show Less -")
            })
        }
    })
    // Adding active class on hover
    $('.pricing-area .item:nth-child(2) .card').addClass('active');
    $('.pricing-area').on('mouseover', '.card', function() {
        $('.card.active').removeClass('active');
        $(this).addClass('active');
    });


    /*============================================
        Nice select
    ============================================*/
    $(".select").niceSelect();

    var selectList = $(".nice-select .list")
    $(".nice-select .list").each(function () {
        var list = $(this).children();
        if (list.length > 5) {
            $(this).css({
                "height": "160px",
                "overflow-y": "scroll"
            })
        }
    })


    /*============================================
        Magic Cursor
    ============================================*/
    var cursor = function () {
        // Variables Declaration
        var cursor = $('.cursor');
        if (window.innerWidth > 1199) {
            // Adding cursor effect
            $(window).on('mousemove', function (e) {
                cursor.css({
                    'transform': "translate(" + e.clientX + "px," + e.clientY + "px)"
                })
            })
            // Add hover class
            $('a, button, .cursor-pointer').on('mouseenter', function () {
                cursor.addClass('hover');
            })
            // Remove hover class
            $('a, button, .cursor-pointer').on('mouseleave', function () {
                cursor.removeClass('hover');
            })
        } else {
            cursor.remove();
        }
    }

    
    /*============================================
        Footer date
    ============================================*/
    var date = new Date().getFullYear();
    $("#footerDate").text(date);

    $(document).ready(function () {
        lazyLoad()
        cursor()
    })

})(jQuery);

$(window).on("load", function() {
    const delay = 1000;
    /*============================================
        Preloader
    ============================================*/
    $("#preLoader").delay(delay).fadeOut();
    /*============================================
        Aos animation
    ============================================*/
    var aosAnimation = function() {
        AOS.init({
            easing: "ease",
            duration: 1200,
            once: true,
            offset: 60,
            disable: 'mobile'
        });
    }
    if ($("#preLoader")) {
        setTimeout(() => {
            aosAnimation()
        }, delay);
    } else {
        aosAnimation();
    }
})
