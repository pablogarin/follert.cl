/*
 * Script sitio Frutek
 * */
var url = "http://follert.neversaynever.cl"
var title = $("title").text();
var content = $("#main-content").html();
$(document).ready(function(evt){
    /*
    $("#main-nav li a, .link").on("click",function(evt){
        $("#main-content > *,footer").fadeOut('fast');
        evt.preventDefault();
        loading();
        var url = $(this).attr("href");
        $("#main-content").load(url+" #main-content > *",null,function(data){
            var title = $(data).filter("title").text();
            var content = $(data).filter("#main-content").html();
            document.title = title;
            window.history.pushState({"html":content,"pageTitle":title},"", url);
            $("#main-content > *,footer").fadeIn('fast');
            loadEvents();
            loading();
        });
    });
    window.onpopstate = function(e){
        if(e.state){
            loading();
            document.getElementById("main-content").innerHTML = e.state.html;
            document.title = e.state.pageTitle;
            loading();
        } else {
            loading();
            document.getElementById("main-content").innerHTML = content;
            document.title = title;
            loading();
        }
        loadEvents();
    };
    //*/
    loadEvents();
});
var slideFrase;
function loadEvents(){
    // ALL EVENTS
    $("#proyectos .thumbs-container .thumb a").off("click");
    $("#proyectos .thumbs-container .thumb a").on("click",function(e){
        e.preventDefault();
        var id = $(this).attr("data-rel");
        loading();
        $("#detalle-proyecto").load("/proyectos?id="+id+" #detalle-proyecto > *",null,function(){
            $("#detalle-proyecto div.carousel").slick({
                dots: false,
                infinite: true,
                autoplay: true,
                autoplaySpeed: 5000,
                slidesToShow: 1,
                slidesToScroll: 1,
                prevArrow:"#detalle-proyecto .slider-prev",
                nextArrow:"#detalle-proyecto .slider-next",
                fade: true,
                cssEase: 'linear'
            });
            $("#detalle-proyecto").modal();
        });
    });
    $("#detalle-proyecto").on('show.bs.modal', function(event){
        loading();
        setTimeout(function(){
            $("#detalle-proyecto .carousel")[0].slick.setPosition();
        },200);
    });
    /*
    $("#proyectos .thumbs-container .thumb a").fancybox({
        'transitionIn'  :   'elastic',
        'transitionOut' :   'elastic',
        'speedIn'       :   600, 
        'speedOut'      :   200, 
        'overlayShow'   :   false
    });
    //*/
    $("nav#main-nav > ul > li > a").on("click",function(){
        $("nav#main-nav > ul > li").removeClass("active");
        $(this).parents("li:first").addClass("active");
    });
    $("#banners-home").slick({
        dots: true,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 5000,
        slidesToShow: 1,
        slidesToScroll: 1,
        prevArrow:"#banner-home .slider-prev",
        nextArrow:"#banner-home .slider-next",
        fade: true,
        cssEase: 'linear'
    });
    $("#clientes-carrousel > div.carousel").slick({
        dots: false,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 5000,
        slidesToShow: 5,
        slidesToScroll: 1,
        prevArrow:"#clientes-carrousel .slider-prev",
        nextArrow:"#clientes-carrousel .slider-next",
        responsive: [
          {
            breakpoint: 400,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 2,
              arrows: false
            }
          },
          {
            breakpoint: 767,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 1,
              arrows: false
            }
          },
          {
            breakpoint: 993,
            settings: {
              slidesToShow: 4,
              slidesToScroll: 1,
              arrows: false
            }
          }
        ]
    });
    $("#detalle-proyecto div.carousel").slick({
        dots: false,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 5000,
        slidesToShow: 1,
        slidesToScroll: 1,
        prevArrow:"#detalle-proyecto .slider-prev",
        nextArrow:"#detalle-proyecto .slider-next",
        fade: true,
        cssEase: 'linear'
    });
    slideFrase = setInterval(function(){
        var active = $("#frases-slide div:first").clone();
        active.removeClass('active');
        active.css({'display':'none'});
        $("#frases-slide div:first").fadeOut(666,function(){ 
            $(this).remove();
        });
        $("#frases-slide div:nth-child(2)").fadeIn(666,function(){
            $(this).addClass('active');
        });
        $("#frases-slide").append(active);
    },3000);
    /*
    $("#frases-slide").slick({
        dots: false,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 3000,
        slidesToShow: 1,
        slidesToScroll: 1,
        prevArrow:"#banner-home .slider-prev",
        nextArrow:"#banner-home .slider-next",
        fade: true,
        cssEase: 'linear'
    });
    //*/
    $("form[name='contacto']").on("submit",function(evt){
        evt.preventDefault();
        var data = $("form[name='contacto']").serialize();
        $.ajax({
            url     : '/ajax/send.php',
            method  : 'POST',
            data    : data,
            success : function(resp){
                alert(resp);
            }
        }).done(function(){
            $("form[name='contacto']")[0].reset();
        });
    });
}
var showingLoader = false;
function loading(){
    //TODO: loader spinner
    if(showingLoader){
        $.fancybox.hideLoading()
    } else {
        $.fancybox.showLoading()
    }
    showingLoader = !showingLoader;
}
