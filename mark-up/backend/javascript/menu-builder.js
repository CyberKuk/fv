$(function () {
    wrapMenu = function( animate ){
        var $menuWrapper = $(".main-menu"),
            wrapperHeight = parseInt($("body")[0].offsetHeight) - 140,
            $element = $menuWrapper.find(".tile").eq(0),
            $elementHeight = parseInt($element.css("height")),
            $elementMarginTop = parseInt($element.css("margin-top")),
            $elementMarginBottom = parseInt($element.css("margin-bottom")),
            $elementTotalHeight = $elementMarginTop + $elementHeight + $elementMarginBottom,
            $rowsInGroup = Math.floor(wrapperHeight / $elementTotalHeight);

        $menuWrapper
            .css("height", wrapperHeight + "px")
            .children()
            .each(function () {
                var tilesGroups = [],
                    append = function (i, j, value) {
                        if (typeof tilesGroups[i] == "undefined") {
                            tilesGroups[i] = [];
                        }

                        if (typeof tilesGroups[i][j] == "undefined") {
                            tilesGroups[i][j] = [];
                        }

                        tilesGroups[i][j].push(value);
                    },
                    length = function (i, j) {
                        if (typeof tilesGroups[i] == "undefined") {
                            tilesGroups[i] = [];
                        }

                        if (typeof tilesGroups[i][j] == "undefined") {
                            tilesGroups[i][j] = [];
                        }

                        return tilesGroups[i][j].length;
                    },
                    $tileGroupWrapper = $(this),
                    tileSet = $(this).find(".tile"),
                    rowNumber = 0, groupNumber = 0;

                tileSet.each(function () {
                    var $tile = $(this);

                    if ($tile.hasClass("double")) {
                        if (length(groupNumber, rowNumber) == 0) {
                            append(groupNumber, rowNumber, $tile)
                            groupNumber++;
                            rowNumber = 0;
                        }
                        else {
                            groupNumber++;
                            rowNumber = 0;
                            append(groupNumber, rowNumber, $tile)
                            groupNumber++;
                        }

                    }
                    else {
                        append(groupNumber, rowNumber, $tile)
                        if (length(groupNumber, rowNumber) == 2) {
                            groupNumber++;
                            rowNumber = 0;
                        }
                    }
                });


                var $div = $("<div></div>");
                for (var i = 0; i < tilesGroups.length; i++) {
                    if (i % $rowsInGroup == 0) {
                        $div.appendTo($tileGroupWrapper);
                        $div = $("<div></div>");
                    }

                    for (var j = 0; j < tilesGroups[i].length; j++) {
                        $(tilesGroups[i][j]).each(function () {
                            this.appendTo($div);
                        })
                    }
                }
                $div.appendTo($tileGroupWrapper);
                $tileGroupWrapper.children().each(function () {
                    if (!$(this).children().length) {
                        $(this).remove();
                    }
                });

                if( animate ){
                    var counter = 0, time = 300;
                    $menuWrapper.find(".tiles-group > div").hide();
                    $menuWrapper.children().show();

                    $menuWrapper.find(".tiles-group > div").each(function(){
                        var $this = $(this);
                        setTimeout( function(){ $this.fadeIn(time) }, time * counter / 4 );
                        ++counter;
                    });
                }
                else{
                    $menuWrapper.children().show();
                }
        });
    }
    unwrapMenu = function(){
        $(".tiles-group").each(function(){
            var $this = $(this);
            $this.children().each(function(){
                var $container = $(this);
                if( $container.hasClass( "tile" ) ){
                    return;
                }

                $container.find( ".tile").appendTo( $this );
                $container.remove();
            })
        })
    };

    wrapMenu( true );

    var timeout = 0;
    $(window).resize(function(){
        clearTimeout( timeout );
        timeout = setTimeout( function(){
            unwrapMenu();
            wrapMenu( true );
        }, 500 );
    });

    (function(){
        var domWrapperElement = $(".main-menu")[0],
            handleScroll = function(e){
            e = e || window.event;
            var delta = e.deltaY || e.detail || e.wheelDelta;

            if( window.opera ){
                delta = -delta;
            }
            delta = 120 * delta/Math.abs(delta);

            if( delta > 0 ){
                offsetWrapper( delta );
            }
            else{
                offsetWrapper( delta );
            }
            },
            offsetWrapper = function( offset ){
                console.log(offset);
                $(domWrapperElement).scrollLeft( domWrapperElement.scrollLeft - offset );
            }

        if (domWrapperElement.addEventListener) {
            if ('onwheel' in document) {
                domWrapperElement.addEventListener ("wheel", handleScroll, false);
            } else if ('onmousewheel' in document) {
                domWrapperElement.addEventListener ("mousewheel", handleScroll, false);
            } else {
                domWrapperElement.addEventListener ("MozMousePixelScroll", handleScroll, false);
            }
        } else { // IE<9
            domWrapperElement.attachEvent ("onmousewheel", handleScroll );
        }
    })();

});