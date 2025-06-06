"use strict";
function menulayout() {
    resetlayout();
    var e = $(window)[0].innerWidth;
    $("#mobile-collapse").on("click", function (o) {
        e > 991 ? $(".pcoded-navbar:not(.theme-horizontal)").toggleClass("navbar-collapsed") : ($(".pcoded-navbar").toggleClass("mob-open"), o.stopPropagation());
    }),
        $(".pcoded-navbar").on("click tap", function (e) {
            e.stopPropagation();
        }),
        $(".pcoded-main-container,.pcoded-header").on("click", function () {
            e < 992 && 1 == $(".pcoded-navbar").hasClass("mob-open") && ($(".pcoded-navbar").removeClass("mob-open"), $("#mobile-collapse,#mobile-collapse1").removeClass("on"));
        }),
        e < 992
            ? $(".pcoded-navbar").hasClass("theme-horizontal") && ($(".pcoded-navbar").addClass("theme-horizontal-dis"), $(".pcoded-navbar").removeClass("theme-horizontal"))
            : $(".pcoded-navbar").hasClass("theme-horizontal-dis") && ($(".pcoded-navbar").addClass("theme-horizontal"), $(".pcoded-navbar").removeClass("theme-horizontal-dis"));
}
function resetlayout() {
    $("#mobile-collapse").off("click"), $("#mobile-collapse").off("hover"), $(".pcoded-navbar:not(.theme-horizontal)").removeClass("navbar-collapsed");
}
function togglemenu() {}
function toggleFullScreen() {
    $(window).height();
    document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement
        ? document.cancelFullScreen
            ? document.cancelFullScreen()
            : document.mozCancelFullScreen
            ? document.mozCancelFullScreen()
            : document.webkitCancelFullScreen && document.webkitCancelFullScreen()
        : document.documentElement.requestFullscreen
        ? document.documentElement.requestFullscreen()
        : document.documentElement.mozRequestFullScreen
        ? document.documentElement.mozRequestFullScreen()
        : document.documentElement.webkitRequestFullscreen && document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT),
        $(".full-screen > i").toggleClass("icon-maximize"),
        $(".full-screen > i").toggleClass("icon-minimize");
}
$(document).ready(function () {
    function e(e) {
        var o = 0;
        try {
            o = e.attr("placeholder").length;
        } catch (e) {
            o = 0;
        }
        e.val().length > 0 || o > 0 ? e.parent(".form-group").addClass("fill") : e.parent(".form-group").removeClass("fill");
    }
    $('[data-toggle="tooltip"]').tooltip(), $('[data-toggle="popover"]').popover();
    var o = $(window)[0].innerWidth;
    if (
        ($(".to-do-list input[type=checkbox]").on("click", function () {
            $(this).prop("checked") ? $(this).parent().addClass("done-task") : $(this).parent().removeClass("done-task");
        }),
        $(".mobile-menu").on("click", function () {
            $(this).toggleClass("on");
        }),
        $(".mob-toggler").on("click", function () {
            $(".pcoded-header > .collapse,.pcoded-header > .container > .collapse").toggleClass("d-flex");
        }),
        $(".search-btn").on("click", function () {
            $(this);
            $(".main-search").addClass("open"), o <= 991 ? $(".main-search .form-control").css({ width: "90px" }) : $(".main-search .form-control").css({ width: "150px" });
        }),
        $(".search-close").on("click", function () {
            $(this);
            $(".main-search").removeClass("open"), $(".main-search .form-control").css({ width: "0" });
        }),
        $(".pop-search").on("click", function () {
            $(".search-bar").slideToggle("fast"), $(".search-bar input").focus();
        }),
        $(".search-bar .close").on("click", function () {
            $(".search-bar").slideToggle("fast");
        }),
        o <= 991 && ($(".main-search").addClass("open"), $(".main-search .form-control").css({ width: "100px" })),
        $(".noti-body")[0])
    ) {
        new PerfectScrollbar(".notification  .noti-body", { wheelSpeed: 0.5, swipeEasing: 0, suppressScrollX: !0, wheelPropagation: 1, minScrollbarLength: 40 });
    }
    if (!$(".pcoded-navbar").hasClass("theme-horizontal")) {
        var o = $(window)[0].innerWidth;
        if ($(".navbar-content")[0])
            if (o < 992 || $(".pcoded-navbar").hasClass("menupos-static")) {
                new PerfectScrollbar(".navbar-content", { wheelSpeed: 0.5, swipeEasing: 0, suppressScrollX: !0, wheelPropagation: 1, minScrollbarLength: 40 });
            } else {
                new PerfectScrollbar(".navbar-content", { wheelSpeed: 0.5, swipeEasing: 0, suppressScrollX: !0, wheelPropagation: 1, minScrollbarLength: 40 });
            }
    }
    $(".card-option .close-card").on("click", function () {
        var e = $(this);
        e.parents(".card").addClass("anim-close-card"),
            e.parents(".card").animate({ "margin-bottom": "0" }),
            setTimeout(function () {
                e.parents(".card").children(".card-block").slideToggle(),
                    e.parents(".card").children(".card-body").slideToggle(),
                    e.parents(".card").children(".card-header").slideToggle(),
                    e.parents(".card").children(".card-footer").slideToggle();
            }, 600),
            setTimeout(function () {
                e.parents(".card").remove();
            }, 1500);
    }),
        $(".card-option .reload-card").on("click", function () {
            var e = $(this);
            e.parents(".card").addClass("card-load"),
                e.parents(".card").append('<div class="card-loader"><i class="pct-loader1 anim-rotate"></div>'),
                setTimeout(function () {
                    e.parents(".card").children(".card-loader").remove(), e.parents(".card").removeClass("card-load");
                }, 3e3);
        }),
        $(".card-option .minimize-card").on("click", function () {
            var e = $(this),
                o = $(e.parents(".card"));
            $(o).children(".card-block").slideToggle(), $(o).children(".card-body").slideToggle();
            o.hasClass("full-card") || $(o).css("height", "auto"), $(this).children("a").children("span").toggle();
        }),
        $(".card-option .full-card").on("click", function () {
            var e = $(this),
                o = $(e.parents(".card"));
            if ((o.toggleClass("full-card"), $(this).children("a").children("span").toggle(), o.hasClass("full-card"))) {
                $("body").css("overflow", "hidden"), $("html,body").animate({ scrollTop: 0 }, 1e3);
                var d = $(o, this),
                    n = d.offset(),
                    s = n.left,
                    i = n.top,
                    r = $(window).height(),
                    a = $(window).width();
                o.animate({ marginLeft: s - 2 * s, marginTop: i - 2 * i, width: a, height: r });
            } else
                $("body").css("overflow", ""),
                    o.removeAttr("style"),
                    setTimeout(function () {
                        $("html,body").animate({ scrollTop: $(o).offset().top - 70 }, 500);
                    }, 400);
        }),
        $(".event-btn").each(function () {
            $(this).children(".spinner-border").hide(), $(this).children(".spinner-grow").hide(), $(this).children(".load-text").hide();
        }),
        $(".event-btn").on("click", function () {
            var e = $(this);
            e.children(".spinner-border").show(),
                e.children(".spinner-grow").show(),
                e.children(".load-text").show(),
                e.children(".btn-text").hide(),
                e.attr("disabled", "true"),
                setTimeout(function () {
                    e.children(".spinner-border").hide(), e.children(".spinner-grow").hide(), e.children(".load-text").hide(), e.children(".btn-text").show(), e.removeAttr("disabled");
                }, 3e3);
        }),
        $(".form-control").each(function () {
            e($(this));
        }),
        $(".form-control").on("blur", function () {
            e($(this));
        }),
        $(".form-control").on("focus", function () {
            $(this).parent(".form-group").addClass("fill");
        }),
        setTimeout(function () {
            $(".loader-bg").fadeOut("slow", function () {
                $(this).remove();
            });
        }, 400),
        menulayout();
}),
    $(window).resize(function () {
        menulayout(), ($(".pcoded-navbar").hasClass("theme-horizontal") || $(".pcoded-navbar").hasClass("theme-horizontal-dis")) && menulayout();
    }),
    $(window).scroll(function () {}),
    ($.fn.pcodedmenu = function (e) {
        var o = (this.attr("id"), { themelayout: "vertical", MenuTrigger: "click", SubMenuTrigger: "click" }),
            e = $.extend({}, o, e),
            d = {
                PcodedMenuInit: function () {
                    d.HandleMenuTrigger(), d.HandleSubMenuTrigger(), d.HandleOffset();
                },
                HandleSubMenuTrigger: function () {
                    var o = $(window),
                        d = o.width();
                    if (1 == $(".pcoded-navbar").hasClass("theme-horizontal") || 1 == $(".pcoded-navbar").hasClass("theme-horizontal-dis"))
                        if (d >= 992) {
                            var n = $(".pcoded-navbar.theme-horizontal .pcoded-inner-navbar .pcoded-submenu > li.pcoded-hasmenu");
                            n.off("click")
                                .off("mouseenter mouseleave")
                                .hover(
                                    function () {
                                        $(this).addClass("pcoded-trigger").addClass("active");
                                    },
                                    function () {
                                        $(this).removeClass("pcoded-trigger").removeClass("active");
                                    }
                                );
                        } else {
                            var n = $(".pcoded-navbar.theme-horizontal-dis .pcoded-inner-navbar .pcoded-submenu > li > .pcoded-submenu > li");
                            n.off("mouseenter mouseleave")
                                .off("click")
                                .on("click", function (e) {
                                    e.stopPropagation();
                                    var o = $(this).closest(".pcoded-submenu").length;
                                    console.log("123123"),
                                        0 === o
                                            ? $(this).hasClass("pcoded-trigger")
                                                ? ($(this).removeClass("pcoded-trigger"), $(this).children(".pcoded-submenu").slideUp())
                                                : ($("pcoded-navbar.theme-horizontal-dis .pcoded-inner-navbar > .pcoded-submenu > li > .pcoded-submenu > li.pcoded-trigger").children(".pcoded-submenu").slideUp(),
                                                  $(this).closest(".pcoded-inner-navbar").find("li.pcoded-trigger").removeClass("pcoded-trigger"),
                                                  $(this).addClass("pcoded-trigger"),
                                                  $(this).children(".pcoded-submenu").slideDown())
                                            : $(this).hasClass("pcoded-trigger")
                                            ? ($(this).removeClass("pcoded-trigger"), $(this).children(".pcoded-submenu").slideUp())
                                            : ($("pcoded-navbar.theme-horizontal-dis .pcoded-inner-navbar > .pcoded-submenu > li > .pcoded-submenu > li.pcoded-trigger").children(".pcoded-submenu").slideUp(),
                                              $(this).closest(".pcoded-submenu").find("li.pcoded-trigger").removeClass("pcoded-trigger"),
                                              $(this).addClass("pcoded-trigger"),
                                              $(this).children(".pcoded-submenu").slideDown());
                                });
                        }
                    switch (e.SubMenuTrigger) {
                        case "click":
                            $(".pcoded-navbar .pcoded-hasmenu").removeClass("is-hover"),
                                $(".pcoded-inner-navbar > .pcoded-hasmenu > .pcoded-submenu > li > .pcoded-submenu > li").on("click", function (e) {
                                    e.stopPropagation(),
                                        0 === $(this).closest(".pcoded-submenu").length
                                            ? $(this).hasClass("pcoded-trigger")
                                                ? ($(this).removeClass("pcoded-trigger"), $(this).children(".pcoded-submenu").slideUp())
                                                : ($(".pcoded-submenu > li > .pcoded-submenu > li.pcoded-trigger").children(".pcoded-submenu").slideUp(),
                                                  $(this).closest(".pcoded-inner-navbar").find("li.pcoded-trigger").removeClass("pcoded-trigger"),
                                                  $(this).addClass("pcoded-trigger"),
                                                  $(this).children(".pcoded-submenu").slideDown())
                                            : $(this).hasClass("pcoded-trigger")
                                            ? ($(this).removeClass("pcoded-trigger"), $(this).children(".pcoded-submenu").slideUp())
                                            : ($(".pcoded-submenu > li > .pcoded-submenu > li.pcoded-trigger").children(".pcoded-submenu").slideUp(),
                                              $(this).closest(".pcoded-submenu").find("li.pcoded-trigger").removeClass("pcoded-trigger"),
                                              $(this).addClass("pcoded-trigger"),
                                              $(this).children(".pcoded-submenu").slideDown());
                                }),
                                $(".pcoded-inner-navbar > .pcoded-hasmenu > .pcoded-submenu > li").on("click", function (e) {
                                    e.stopPropagation(),
                                        0 === $(this).closest(".pcoded-submenu").length
                                            ? $(this).hasClass("pcoded-trigger")
                                                ? ($(this).removeClass("pcoded-trigger"), $(this).children(".pcoded-submenu").slideUp())
                                                : ($(".pcoded-hasmenu > li.pcoded-trigger").children(".pcoded-submenu").slideUp(),
                                                  $(this).closest(".pcoded-inner-navbar").find("li.pcoded-trigger").removeClass("pcoded-trigger"),
                                                  $(this).addClass("pcoded-trigger"),
                                                  $(this).children(".pcoded-submenu").slideDown())
                                            : $(this).hasClass("pcoded-trigger")
                                            ? ($(this).removeClass("pcoded-trigger"), $(this).children(".pcoded-submenu").slideUp())
                                            : ($(".pcoded-hasmenu > li.pcoded-trigger").children(".pcoded-submenu").slideUp(),
                                              $(this).closest(".pcoded-submenu").find("li.pcoded-trigger").removeClass("pcoded-trigger"),
                                              $(this).addClass("pcoded-trigger"),
                                              $(this).children(".pcoded-submenu").slideDown());
                                });
                    }
                },
                HandleMenuTrigger: function () {
                    if ($(window).width() >= 992) {
                        if (1 == $(".pcoded-navbar").hasClass("theme-horizontal")) {
                            var o = $(".theme-horizontal .pcoded-inner-navbar > li");
                            o.off("click")
                                .off("mouseenter mouseleave")
                                .hover(
                                    function () {
                                        if (($(this).addClass("pcoded-trigger").addClass("active"), $(".pcoded-submenu", this).length)) {
                                            var e = $(".pcoded-submenu:first", this),
                                                o = e.offset(),
                                                d = o.left,
                                                n = e.width();
                                            if (($(window).height(), d + n <= $(window).width())) $(this).removeClass("edge");
                                            else {
                                                var s = $(".sidenav-inner").attr("style");
                                                $(".sidenav-inner").css({ "margin-left": parseInt(s.slice(12, s.length - 3)) - 80 }), $(".sidenav-horizontal-prev").removeClass("disabled");
                                            }
                                        }
                                    },
                                    function () {
                                        $(this).removeClass("pcoded-trigger").removeClass("active");
                                    }
                                );
                        }
                    } else {
                        var o = $(".pcoded-navbar.theme-horizontal-dis .pcoded-inner-navbar > li");
                        o.off("mouseenter mouseleave")
                            .off("click")
                            .on("click", function () {
                                $(this).hasClass("pcoded-trigger")
                                    ? ($(this).removeClass("pcoded-trigger"), $(this).children(".pcoded-submenu").slideUp())
                                    : ($("li.pcoded-trigger").children(".pcoded-submenu").slideUp(),
                                      $(this).closest(".pcoded-inner-navbar").find("li.pcoded-trigger").removeClass("pcoded-trigger"),
                                      $(this).addClass("pcoded-trigger"),
                                      $(this).children(".pcoded-submenu").slideDown());
                            });
                    }
                    switch (e.MenuTrigger) {
                        case "click":
                            $(".pcoded-navbar").removeClass("is-hover"),
                                $(".pcoded-inner-navbar > li:not(.pcoded-menu-caption) ").on("click", function () {
                                    $(this).hasClass("pcoded-trigger")
                                        ? ($(this).removeClass("pcoded-trigger"), $(this).children(".pcoded-submenu").slideUp())
                                        : ($("li.pcoded-trigger").children(".pcoded-submenu").slideUp(),
                                          $(this).closest(".pcoded-inner-navbar").find("li.pcoded-trigger").removeClass("pcoded-trigger"),
                                          $(this).addClass("pcoded-trigger"),
                                          $(this).children(".pcoded-submenu").slideDown());
                                });
                    }
                },
                HandleOffset: function () {
                    switch (e.themelayout) {
                        case "horizontal":
                            "hover" === e.SubMenuTrigger
                                ? $("li.pcoded-hasmenu").on("mouseenter mouseleave", function (e) {
                                      if ($(".pcoded-submenu", this).length) {
                                          var o = $(".pcoded-submenu:first", this),
                                              d = o.offset(),
                                              n = d.left,
                                              s = o.width();
                                          $(window).height();
                                          n + s <= $(window).width() ? $(this).removeClass("edge") : $(this).addClass("edge");
                                      }
                                  })
                                : $("li.pcoded-hasmenu").on("click", function (e) {
                                      if ((e.preventDefault(), $(".pcoded-submenu", this).length)) {
                                          var o = $(".pcoded-submenu:first", this),
                                              d = o.offset(),
                                              n = d.left,
                                              s = o.width();
                                          $(window).height();
                                          n + s <= $(window).width() || $(this).toggleClass("edge");
                                      }
                                  });
                    }
                },
            };
        d.PcodedMenuInit();
    }),
    $("#pcoded").pcodedmenu({ MenuTrigger: "click", SubMenuTrigger: "click" }),
    $(".pcoded-navbar .close").on("click", function () {
        $(this).parents(".card").fadeOut("slow").remove();
    }),
    $(".pcoded-navbar .pcoded-inner-navbar a").each(function () {
        var e = window.location.href.split(/[?#]/)[0];
        $("body").hasClass("layout-14") || (this.href == e && "" != $(this).attr("href") && ($(this).parent("li").addClass("active"),
                $(".pcoded-navbar").hasClass("theme-horizontal") ||
                    ($(this).parent("li").parent().parent(".pcoded-hasmenu").addClass("active").addClass("pcoded-trigger"),
                    $(this).parent("li").parent().parent(".pcoded-hasmenu").parent().parent(".pcoded-hasmenu").addClass("active").addClass("pcoded-trigger")),
                $(this).parent("li").parent().parent(".sidelink").addClass("active"),
                $(this).parent("li").parent().parent().parent().parent(".sidelink").addClass("active"),
                $(this).parent("li").parent().parent().parent().parent().parent().parent(".sidelink").addClass("active")));
    }),
    $(window).scroll(function () {
        $(".pcoded-header").hasClass("headerpos-fixed") ||
            ($(this).scrollTop() > 60
                ? ($(".pcoded-navbar.menupos-fixed").css("position", "fixed"), $(".pcoded-navbar.menupos-fixed").css("transition", "none"), $(".pcoded-navbar.menupos-fixed").css("margin-top", "0px"))
                : ($(".pcoded-navbar.menupos-fixed").removeAttr("style"), $(".pcoded-navbar.menupos-fixed").css("position", "absolute"), $(".pcoded-navbar.menupos-fixed").css("margin-top", "60px"))),
            $("body").hasClass("box-layout") &&
                ($(this).scrollTop() > 60
                    ? ($(".pcoded-navbar").css("position", "fixed"), $(".pcoded-navbar").css("transition", "none"), $(".pcoded-navbar").css("margin-top", "0px"), $(".pcoded-navbar").css("border-radius", "0px"))
                    : ($(".pcoded-navbar").removeAttr("style"), $(".pcoded-navbar").css("position", "absolute"), $(".pcoded-navbar").css("margin-top", "60px")));
    }),
    $.ripple(".btn, .pcoded-navbar a,.pcoded-header .navbar-nav > li > .dropdown > a,.page-link, .nav .nav-link", {
        debug: !1,
        on: "mousedown",
        opacity: 0.4,
        color: "auto",
        multi: !1,
        duration: 0.7,
        rate: function (e) {
            return e;
        },
        easing: "linear",
    }),
    $("#more-details").on("click", function () {
        $("#nav-user-link").slideToggle();
    });
