$(document).ready(function() {
    $('body').removeClass('fade-out');
    $(window).scroll(function () {
        var $navbar = $(".navbar-default");
        var scroll = $(window).scrollTop();
        $navbar.toggleClass('scrolled-nav', scroll > $(".navbar").height());
    });

    $('.filtr-container').filterizr({filter: "1"});

    $('.simplefilter div').click(function() {
        $('.simplefilter div').removeClass('active');
        $(this).addClass('active');
    });
});
