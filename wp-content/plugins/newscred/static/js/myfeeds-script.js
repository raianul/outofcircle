jQuery.noConflict();

/**
 * myfeeds scripts
 */

(function($){


    // source filter ajax call
    $("#source_guids").ajaxChosen({
        method: 'GET',
        url: NcAjax.ajaxurl,
        dataType: 'json',
        action : "ncajax-source-submit"


    }, function (data) {
        var terms = {};
        $.each(data, function (i, val) {
            terms[i] = val;
        });
        //console.log(terms)
        return terms;
    });

    // topics filter ajax call

    $("#topic_guids").ajaxChosen({
        method: 'GET',
        url: NcAjax.ajaxurl,
        dataType: 'json',
        action : "ncajax-topic-submit"

    }, function (data) {
        var terms = {};
        $.each(data, function (i, val) {
            terms[i] = val;
        });
        return terms;
    });


    // myFeeds create API modal
    $("#nc_api_create").colorbox({inline:true, width:"800px", scrolling:true});

    // auto publish chceck
    $('#myfeed-autopublish').click(function(){
        $("#myfeeds-settings").slideToggle();
    });

    $("#myfeed-create-category").click(function(){
        $("#myfeed-category-box").slideToggle();
    })

    if($("#myfeed-autopublish").is(":checked")){
        $("#myfeeds-settings").slideToggle();
    }
    // create api call
    $("#myfeed-api-form").submit(function(){

        $(".nc-search-loading").css("visibility", "visible");

        $.ajax({
            url: NcAjax.ajaxurl,
            type:'POST' ,
            data:$("#myfeed-api-form").serialize() ,
            success:function (response) {
                $("#apicall").val( response )
                $(".nc-search-loading").css("visibility", "hidden");
                $("#cboxClose").trigger("click");
            },
            timeout:90000,
            error:function () {
                $(".nc-search-loading").css("visibility", "hidden");
                $("#cboxClose").trigger("click");

            }
        });

        return false;
    })

    // add new wp category for my feed insert
    $("#add_feed_category").click(function(){

        var query_data =  {};

        var cat = $("#category").val();

        query_data['cat'] = cat;
        query_data['action'] = "ncajax-add-myfeed-category"
        $(".nc-category-loading").css("visibility", "visible");

        if( cat ){

            $.ajax({
                url: NcAjax.ajaxurl,
                type:'POST' ,
                data:query_data ,
                success:function (response) {
                    $(".nc-category-loading").css("visibility", "hidden");
                    if( response  ){
                        $('#myfeed_category').append('<option value="'+response+'" selected="selected">'+cat+'</option>');
                        $('#myfeed_category option:last').focus();
                    }

                },
                timeout:90000,
                error:function () {
                    $(".nc-category-loading").css("visibility", "visible");
                }
            });
        }
        return false;
    });
    // add new wp category IF HIT ENTER
    $("#myfeed-category-box #category").keyup(function(e){
        return false;
    });
    $("#myfeed-category-box #category").keypress(function(e){
        if(e.which == 13 ){
            $("#add_feed_category").trigger("click");
            return false;
        }
    });

})(jQuery);