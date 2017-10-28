
// var jQuery_1_11_1 = $.noConflict(true);

// jQuery_1_11_1(document).on("click", "ul li span.delete", function () {
//     var listview = jQuery_1_11_1(this).closest("ul");
//     jQuery_1_11_1(".ui-content").css({
//         overflow: "hidden"
//     });
//     jQuery_1_11_1(this).parent().css({
//         display: "block"
//     }).animate({
//         opacity: 0
//     }, {
//         duration: 250,
//         queue: false
//     }).animate({
//         height: 0
//     }, 300, function () {
//         jQuery_1_11_1(this).remove();
//         listview.listview("refresh");
//         jQuery_1_11_1(".ui-content").removeAttr("style");
//     });
// }).on("click", "ul li span.flag", function () {
//     var text = jQuery_1_11_1("p", this),
//         button = jQuery_1_11_1(this).siblings("a"),
//         flagged = button.find(".flagged").hasClass("ui-screen-hidden") ? false : true;
//     if (!flagged) {
//         button.find(".flagged").removeClass("ui-screen-hidden");
//         text.text("Unflag");
//     } else {
//         button.find(".flagged").addClass("ui-screen-hidden");
//         text.text("Flag");
//     }
// }).on("click", "ul li span.more", function () {
//     alert("nothing");
// }).on("swipeleft", "ul li a", function (e) {
//     jQuery_1_11_1(this).prevAll("span").addClass("show");
//     jQuery_1_11_1(this).off("click").blur();
//     jQuery_1_11_1(this).css({
//         transform: "translateX(-80px)"
//     }).one("transitionend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd", function () {
//         jQuery_1_11_1(this).one("swiperight", function () {
//             jQuery_1_11_1(this).prevAll("span").removeClass("show");
//             //jQuery_1_11_1(this).on("click").blur();
//             jQuery_1_11_1(this).css({
//                 transform: "translateX(0px)"
//             }).blur();
//         });
//     });
// });


$.fn.extend({
    createBtn: function () {
        var elmWidth = $("li", $(this)).width(),
            listType = $(this).listview("option", "inset") ? true : false,
            btnWidth = elmWidth < 300 && listType ? "35%" : elmWidth > 300 && !listType ? "25%" : "20%";
        $("li", $(this)).each(function () {
            var text = $(this).html();
            $(this).html($("<div/>", {
                class: "wrapper"
            }).append($("<div/>", {
                class: "go"
            }).text("Save").width(btnWidth)).append($("<div/>", {
                class: "item"
            }).text(text)).append($("<div/>", {
                class: "del"
            }).text("Delete").width(btnWidth)).css({
                left: "-" + btnWidth
            }).on("swipeleft swiperight vclick tap", function (e) {

                $(this).revealBtn(e, btnWidth);
            }) /**/ );
        });
    },
    revealBtn: function (e, x) {
        var check = this.check(x),
            swipe = e.type;
        if (check == "closed") {
            swipe == "swiperight" ? this.open(e, x, "left") : swipe == "swipeleft" ? this.open(e, x, "right") : setTimeout(function () {
                this.close(e);
            }, 0);
            e.stopImmediatePropagation();
        }
        if (check == "right" || check == "left") {
            swipe == "swiperight" ? this.open(e, "left") : swipe == "swipeleft" ? this.open(e, x, "right") : setTimeout(function () {
                this.close(e);
            }, 0);
            e.stopImmediatePropagation();
        }
        if (check !== "closed" && e.isImmediatePropagationStopped() && (swipe == "vclick" || swipe == "tap")) {
            this.close(e);
        }
    },
    close: function (e) {
        var check = this.check();
        this.css({
            transform: "translateX(0)"
        });
    },
    open: function (e, x, dir) {
        var posX = dir == "left" ? x : "-" + x;
        $(this).css({
            transform: "translateX(" + posX + ")"
        });
    },
    check: function (x) {
        var matrix = this.css("transform").split(" "),
            posY = parseInt(matrix[matrix.length - 2], 10),
            btnW = (this.width() * parseInt(x) / 100) / 1.1;
        return isNaN(posY) ? "closed" : posY >= btnW ? "left" : posY <= "-" + btnW ? "right" : "closed";
    }
});

$(document).on("pagecreate", function () {
    $("ul").createBtn();
});




// var jQuery_1_9_1 = $.noConflict(true);

// jQuery_1_9_1.fn.extend({
//     createBtn: function () {
//         alert("in create btn");
//         var elmWidth = jQuery_1_9_1("li", jQuery_1_9_1(this)).width(),
//             listType = jQuery_1_9_1(this).listview("option", "inset") ? true : false,
//             btnWidth = elmWidth < 300 && listType ? "35%" : elmWidth > 300 && !listType ? "25%" : "20%";
//         jQuery_1_9_1("li", jQuery_1_9_1(this)).each(function () {
//             var text = jQuery_1_9_1(this).html();
//             jQuery_1_9_1(this).html(jQuery_1_9_1("<div/>", {
//                 class: "wrapper"
//             }).append(jQuery_1_9_1("<div/>", {
//                 class: "go"
//             }).text("Save").width(btnWidth)).append(jQuery_1_9_1("<div/>", {
//                 class: "item"
//             }).text(text)).append(jQuery_1_9_1("<div/>", {
//                 class: "del"
//             }).text("Delete").width(btnWidth)).css({
//                 left: "-" + btnWidth
//             }).on("swipeleft swiperight vclick tap", function (e) {

//                 jQuery_1_9_1(this).revealBtn(e, btnWidth);
//             }) /**/ );
//         });
//     },
//     revealBtn: function (e, x) {
//         var check = this.check(x),
//             swipe = e.type;
//         if (check == "closed") {
//             swipe == "swiperight" ? this.open(e, x, "left") : swipe == "swipeleft" ? this.open(e, x, "right") : setTimeout(function () {
//                 this.close(e);
//             }, 0);
//             e.stopImmediatePropagation();
//         }
//         if (check == "right" || check == "left") {
//             swipe == "swiperight" ? this.open(e, "left") : swipe == "swipeleft" ? this.open(e, x, "right") : setTimeout(function () {
//                 this.close(e);
//             }, 0);
//             e.stopImmediatePropagation();
//         }
//         if (check !== "closed" && e.isImmediatePropagationStopped() && (swipe == "vclick" || swipe == "tap")) {
//             this.close(e);
//         }
//     },
//     close: function (e) {
//         var check = this.check();
//         this.css({
//             transform: "translateX(0)"
//         });
//     },
//     open: function (e, x, dir) {
//         var posX = dir == "left" ? x : "-" + x;
//         jQuery_1_9_1(this).css({
//             transform: "translateX(" + posX + ")"
//         });
//     },
//     check: function (x) {
//         var matrix = this.css("transform").split(" "),
//             posY = parseInt(matrix[matrix.length - 2], 10),
//             btnW = (this.width() * parseInt(x) / 100) / 1.1;
//         return isNaN(posY) ? "closed" : posY >= btnW ? "left" : posY <= "-" + btnW ? "right" : "closed";
//     }
// });

// //jQuery_1_9_1("ul").createBtn();
// // $(document).on("pagecreate", function () {
// //     jQuery_1_9_1("ul").createBtn();
// // });

// // function isVisible(){
// //    //do something
// //    alert("loadeateries visible");
// // jQuery_1_9_1("ul").createBtn();
// // }

// // //hookup the event
// // $('#loadeateries').bind('isVisible', isVisible);

// // //show div and trigger custom event in callback when div is visible
// // $('#loadeateries').show('slow', function(){
// //     $(this).trigger('isVisible');
// // });


// document.addEventListener("deviceready", onfire42DeviceReady, false);

// function onfire42DeviceReady() {
//     // Now safe to use the PhoneGap API
//     jQuery_1_9_1("ul").createBtn();
// }