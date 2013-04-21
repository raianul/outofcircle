jQuery.noConflict();
var nc_plugin = this.nc_plugin || {};

var nc_aritcle_request;
var nc_image_request;
var nc_myfeeds_request;
var nc_metabox_search_request;
var nc_related_topics_source_request;
var nc_source_auto_suggestion_request;
var nc_topic_auto_suggestion_request;

var global_auto_suggest_results;
var aritcle_page,image_page,myfeeds_page;

(function($){

    // insert text in mouse cursor position in TEXTAREA
    $.fn.extend({
        insertAtCaret: function(myValue){
            return this.each(function(i) {
                if (document.selection) {
                    //For browsers like Internet Explorer
                    this.focus();
                    sel = document.selection.createRange();
                    sel.text = myValue;
                    this.focus();
                }
                else if (this.selectionStart || this.selectionStart == '0') {
                    //For browsers like Firefox and Webkit based
                    var startPos = this.selectionStart;
                    var endPos = this.selectionEnd;
                    var scrollTop = this.scrollTop;
                    this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
                    this.focus();
                    this.selectionStart = startPos + myValue.length;
                    this.selectionEnd = startPos + myValue.length;
                    this.scrollTop = scrollTop;
                } else {
                    this.value += myValue;
                    this.focus();
                }
            })
        }
    });

    /**
     * nc_plugin object
     */


    // auto Suggestion  for topics and sources

    nc_plugin.autoSuggestion = function(e){

        var query = $("#nc-search").val();

        var query_data = {};

        query_data['query']         = query;
        query_data['action']        = "ncajax-get-topics-sources"

        if (nc_related_topics_source_request !== undefined)
            nc_related_topics_source_request.abort();

        $(".nc-search-loading").css("visibility", "visible");

        nc_related_topics_source_request = $.ajax({
            url: NcAjax.ajaxurl,
            type:'GET',
            data:query_data,
            responseType: 'json',
            success:function (response) {

                $(".nc-search-loading").css("visibility", "hidden");


                var results = $.parseJSON(response);
                var global_auto_suggest_results = results;

                // topics
                var topics_html = "";
                if(results.topics.length > 0){
                    topics_html = '<dt>Topics</dt>';
                    for(var i = 0; i< results.topics.length; i++ ){
                        topics_html += '<dd><a href="javascript:void(0);" guid="'+results.topics[i].guid+'" class="nc-filter-topics">'+results.topics[i].name+'</a></dd>';
                    }
                }

                $('div.auto-suggest-popup dl').html("");
                $('div.auto-suggest-popup dl').append(topics_html);


                // sources
                var source_html = "";
                if(results.sources.length > 0){
                    source_html = '<dt>Source</dt>';
                    for(var i = 0; i< results.sources.length; i++ ){
                        source_html += '<dd><a href="javascript:void(0);" guid="'+results.sources[i].guid+'" class="nc-filter-source">'+results.sources[i].name+'</a></dd>';
                    }
                }
                $('div.auto-suggest-popup dl').append(source_html);



                $('div.auto-suggest-popup').show();
                $(document).bind('focusin.auto-suggest-popup click.auto-suggest-popup',function(e) {
                    if ($(e.target).closest('.auto-suggest-popup, #example').length) return;
                    $(document).unbind('.auto-suggest-popup');
                    $('div.auto-suggest-popup').fadeOut('medium');
                });


            },
            timeout:50000,
            error:function () {
                $(".nc-search-loading").css("visibility", "hidden");

            }
        });
    };

//    // auto Suggestion  for topics and sources
//
//    nc_plugin.autoSuggestion = function(e){
//
//        var query = $("#nc-search").val();
//
//        var query_data = {};
//
//        query_data['term']       = query;
//        query_data['pagesize']   = 5;
//        query_data['action']     = "ncajax-get-auto-suggestion-source"
//
//        var topic_query_data = {};
//
//        topic_query_data['term']       = query;
//        topic_query_data['pagesize']   = 5;
//        topic_query_data['action']     = "ncajax-get-auto-suggestion-topic"
//
//
//        if (nc_source_auto_suggestion_request !== undefined)
//            nc_source_auto_suggestion_request.abort();
//
//        if (nc_topic_auto_suggestion_request !== undefined)
//            nc_topic_auto_suggestion_request.abort();
//
//        $('div.auto-suggest-popup dl').html("");
//
//        $(".nc-search-loading").css("visibility", "visible");
//        var count = 0;
//        nc_source_auto_suggestion_request = $.ajax({
//            url: NcAjax.ajaxurl,
//            type:'GET',
//            data:query_data,
//            dataType: 'json',
//            responseType: 'json',
//            timeout:50000,
//            error:function () {
//                $(".nc-search-loading").css("visibility", "hidden");
//
//            }
//        });
//
//        nc_source_auto_suggestion_request.done(function(response){
//            // sources
//            var topics_html = "";
//
//            topics_html = '<dt>Source</dt>';
//            var chk_empty = 0;
//            $.each(response, function (i, val) {
//                chk_empty = 1;
//                topics_html += '<dd><a href="javascript:void(0);" guid="'+i+'" class="nc-filter-source">'+val+'</a></dd>';
//            });
//
//            if( ! chk_empty )
//                topics_html += '<dd>Nothing found</dd>';
//
//            $('div.auto-suggest-popup dl').append(topics_html);
//
//
//            $('div.auto-suggest-popup').show();
//            $(document).bind('focusin.auto-suggest-popup click.auto-suggest-popup',function(e) {
//                if ($(e.target).closest('.auto-suggest-popup, #example').length) return;
//                $(document).unbind('.auto-suggest-popup');
//                $('div.auto-suggest-popup').fadeOut('medium');
//            });
//
//            count ++;
//            if( count == 2 )
//                $(".nc-search-loading").css("visibility", "hidden");
//
//        });
//
//
//        nc_topic_auto_suggestion_request  = $.ajax({
//            url: NcAjax.ajaxurl,
//            type:'GET',
//            data:topic_query_data,
//            dataType: 'json',
//            responseType: 'json',
//            timeout:50000,
//            error:function () {
//                $(".nc-search-loading").css("visibility", "hidden");
//
//            }
//        });
//
//        nc_topic_auto_suggestion_request.done(function(response){
//
//            // topics
//            var topics_html = "";
//
//            topics_html = '<dt>Topic</dt>';
//            var chk_empty = 0;
//            $.each(response, function (i, val) {
//                chk_empty = 1;
//                topics_html += '<dd><a href="javascript:void(0);" guid="'+i+'" class="nc-filter-topics">'+val+'</a></dd>';
//            });
//
//            if( ! chk_empty )
//                topics_html += '<dd>Nothing found</dd>';
//
//            $('div.auto-suggest-popup dl').append(topics_html);
//
//
//            $('div.auto-suggest-popup').show();
//            $(document).bind('focusin.auto-suggest-popup click.auto-suggest-popup',function(e) {
//                if ($(e.target).closest('.auto-suggest-popup, #example').length) return;
//                $(document).unbind('.auto-suggest-popup');
//                $('div.auto-suggest-popup').fadeOut('medium');
//            });
//
//            count ++;
//            if( count == 2 )
//                $(".nc-search-loading").css("visibility", "hidden");
//
//
//        });
//
//
//    };


    /**
     * metaboxSearch
     */


    nc_plugin.metaboxSearch = function(e){

        e.preventDefault()
        $('#nc-message-box').html("");

        var query_data =  {};

        var sources = [];
        $('span.nc-auto-source').each(function () {
            var source_guid = $(this).attr('guid');
            sources.push(source_guid);
        });
        var topics = [];
        $('span.nc-auto-topics').each(function () {
            var topic_guids = $(this).attr('guid');
            topics.push(topic_guids);
        });


        query_data['query'] = $("#nc-search").val();
        query_data['action'] = "ncajax-metabox-search";
        query_data['page'] = 1 ;
        query_data['sources'] = sources ;
        query_data['topics'] = topics ;
        query_data['only_articles'] = true;
        query_data['sort'] = $("input[name='sort']:checked").val();

        // abort the source autosuggestion request if any
        if (nc_source_auto_suggestion_request !== undefined) {
            nc_source_auto_suggestion_request.abort();
        }
        // abort the topic autosuggestion request if any
        if (nc_topic_auto_suggestion_request !== undefined) {
            nc_topic_auto_suggestion_request.abort();
        }


        if(nc_metabox_search_request !== undefined){
            nc_metabox_search_request.abort();
        }


        if( $("#nc-current-tab").val() == 2 ){
            image_page = 1;
            nc_plugin.searchImages();
            return;
        }

        if( $("#nc-current-tab").val() == 3 ){
            myfeeds_page = 1;
            nc_plugin.searchMyFeeds();
            return;
        }

        image_page = 1;

        aritcle_page = 1;

        $(".nc-search-loading").css("visibility", "visible");

        nc_metabox_search_request = $.ajax({
                url: NcAjax.ajaxurl,
                type:'POST',
                data:query_data,
                success:function (response) {
                    $(".nc-search-loading").css("visibility", "hidden");
                    $(".nc-aritcle-results p").remove();
                    $("#dyna").html( response );

                    // load the meta box tool tip
                    nc_plugin.loadArticleToolTip(0);


                    /**
                     * infinite scrolling for articles
                     */

                    var article_scrole = $('.nc-aritcle-results').scroll(function(){

                        if ( ( $(this)[0].scrollHeight - $(this).scrollTop() == $(this).outerHeight()  - 1 ) && nc_aritcle_request == undefined  ){
                                nc_plugin.searchArticles()
                        }
                    });

                    nc_metabox_search_request = undefined;


                },
                timeout:30000,
                error:function () {
                    $(".nc-search-loading").css("visibility", "hidden");
                    var html = '<div id="message" class="updated below-h2">'+
                                '<p>Please try again later</p>'+
                                '</div>';
                    $('#nc-message-box').html(html);
                }
            });

            nc_plugin.searchImages();

    };

    /**
     * seach articles
     * @param e
     */
    nc_plugin.searchArticles = function(){
        var query_data =  {};

        var sources = [];
        $('span.nc-auto-source').each(function () {
            var source_guid = $(this).attr('guid');
            sources.push(source_guid);
        });
        var topics = [];
        $('span.nc-auto-topics').each(function () {
            var topic_guids = $(this).attr('guid');
            topics.push(topic_guids);
        });


        query_data['query'] = $("#nc-search").val();
        query_data['action'] = "ncajax-metabox-search"
        query_data['page'] = ++aritcle_page ;
        query_data['only_articles'] = true ;
        query_data['sources'] = sources ;
        query_data['topics'] = topics ;
        query_data['sort'] = $("input[name='sort']:checked").val();

        $(".nc-page-loader").css("display", "block");

        console.log(query_data);

        nc_aritcle_request = $.ajax({
                url: NcAjax.ajaxurl,
                type:'POST',
                data:query_data,
                success:function (response) {
                    $(".nc-page-loader").css("display", "none");
                    $("#dyna").append( response );

                    //nc_plugin.image_loader();
                    // load the meta box tool tip
                    nc_plugin.loadArticleToolTip(0);

                    nc_aritcle_request = null;

                },
                timeout:50000,
                error:function () {
                    $(".nc-page-loader").css("display", "none");
                }
            });


    };

    /**
     * seach images
     * @param e
     */
    nc_plugin.searchImages = function(page){

        var query_data =  {};

        var sources = [];
        $('span.nc-auto-source').each(function () {
            var source_guid = $(this).attr('guid');
            sources.push(source_guid);
        });
        var topics = [];
        $('span.nc-auto-topics').each(function () {
            var topic_guids = $(this).attr('guid');
            topics.push(topic_guids);
        });

        var page = image_page;
        query_data['query'] = $("#nc-search").val();
        query_data['action'] = "ncajax-metabox-search"
        query_data['page'] = image_page++ ;
        query_data['only_images'] = true ;
        query_data['sources'] = sources ;
        query_data['topics'] = topics ;
        query_data['sort'] = $("input[name='sort']:checked").val();

        if( $("#nc-current-tab").val() != "" && $("#nc-current-tab").val() != 1){
            if( page == 1)
                $(".nc-search-loading").css("visibility", "visible");
            else
                $(".nc-page-loader").css("display", "block");
        }

        nc_image_request = $.ajax({
                url: NcAjax.ajaxurl,
                type:'POST',
                data:query_data,
                success:function (response) {

                    if( page == 1 ){
                        $(".nc-search-loading").css("visibility", "hidden");
                        $(".thumbnail-box").html( response );
                    }else{
                        $(".nc-page-loader").css("display", "none");
                        $(".thumbnail-box").append( response );
                    }

                    /**
                     * infinite scrolling for articles
                     */

                    $('.nc-image-results').scroll(function(){

                        if ( ( $(this)[0].scrollHeight - $(this).scrollTop() == $(this).outerHeight()  - 1 ) && nc_image_request == undefined ){
                            nc_plugin.searchImages()
                        }
                    })


                    // load the meta box tool tip
                    nc_plugin.loadImageToolTip(1);

                    if( $("#nc-current-tab").val() == 1 || $("#nc-current-tab").val() == "")
                        $.NC_APP("ul.tabs").tabs("div.panes > div" ,{initialIndex: 0} );

                    nc_image_request = null;

                },
                timeout:50000,
                error:function () {
                    if( page == 1 ){
                        $(".nc-search-loading").css("visibility", "hidden");
                    }else{
                        $(".nc-page-loader").css("display", "none");
                    }


                }
            });


    };

    /**
     * seach myFeeds
     * @param e
     */
    nc_plugin.searchMyFeeds = function(){
        var query_data =  {};

//        if(nc_myfeeds_request !== undefined){
//            nc_myfeeds_request.abort();
//        }

        var sources = [];
        $('span.nc-auto-source').each(function () {
            var source_guid = $(this).attr('guid');
            sources.push(source_guid);
        });
        var topics = [];
        $('span.nc-auto-topics').each(function () {
            var topic_guids = $(this).attr('guid');
            topics.push(topic_guids);
        });

        var page = myfeeds_page;

        query_data['query']         = $("#nc-search").val();
        query_data['action']        = "ncajax-metabox-search"
        query_data['page']          = myfeeds_page++ ;
        query_data['only_myfeeds']  = true ;
        query_data['myfeed_id']  = $("#myFeeds").val() ;
        query_data['sources'] = sources ;
        query_data['topics'] = topics ;
        query_data['sort'] = $("input[name='sort']:checked").val();

        if( page == 1 ){
            $(".nc-search-loading").css("visibility", "visible");
        }else{
            $(".nc-page-loader").css("display", "block");
        }


        //console.log(query_data);

        nc_myfeeds_request = $.ajax({
            url: NcAjax.ajaxurl,
            type:'POST',
            data:query_data,
            success:function (response) {

                if( page == 1 ){
                    $(".nc-search-loading").css("visibility", "hidden");
                    $("#myFeeds-content").html( response );
                }else{
                    $(".nc-page-loader").css("display", "none");
                    $("#myFeeds-content").append( response );
                }

                /**
                 * infinite scrolling for myfeeds
                 */

                $('.nc-myfeeds-results').scroll(function(){
                    if ( ( $(this)[0].scrollHeight - $(this).scrollTop() == $(this).outerHeight()  - 1 ) && nc_myfeeds_request == undefined ){

                        nc_plugin.searchMyFeeds()
                    }
                })


                nc_plugin.loadMyFeedsToolTip(2);
                nc_myfeeds_request = null;
            },
            timeout:50000,
            error:function () {
                $(".nc-search-loading").css("visibility", "hidden");
            }
        });


    };


    nc_plugin.loadArticleToolTip = function(index){

        $.NC_APP("ul.tabs").tabs("div.panes > div" ,{initialIndex: index} );


        // for articles
        // loop over all elements creating a tooltip based on their data-tooltip-id attribute
        $('.tooltip-from-element').each(function() {
            var selector = '#' + $(this).data('tooltip-id');

            if(!$(this).attr("add")){
                Tipped.create(this, $(selector)[0],{
                    hook:   'lefttop',
                    onShow: function(content, element) {
                        $("#"+$(element).attr("data-tooltip-id")+" .image-tooltip img.bimg").attr("src", $(element).find(".nc-large-image").attr("url") + "?width=260&height=146");
                    }
                });
                $(this).attr("add",1);
            }
        });

        $('.box-hover').hover(function(){
            $(this).find('.contenthover').fadeIn("middle");
        }, function(){
            $(this).find('.contenthover').fadeOut("middle");
        });


    }

    nc_plugin.loadImageToolTip = function(index){

        $.NC_APP("ul.tabs").tabs("div.panes > div" ,{initialIndex: index} );

        // loop over all elements creating a tooltip based on their data-tooltip-id attribute
        $('.thumbnail-box li').each(function() {
            var selector = '#' + $(this).data('tooltip-id');

            if(!$(this).attr("add")){
                Tipped.create(this, $(selector)[0],{
                    hook:   'lefttop',
                    onShow: function(content, element) {
                        $("#"+$(element).attr("data-tooltip-id")+" .image-tooltip img.bimg").attr("src", $(element).find(".nc-large-image").attr("url") + "?width=260&height=146");
                    }

                });
                $(this).attr("add",1);
            }
        });

    }

    nc_plugin.loadMyFeedsToolTip = function(index){

        $.NC_APP("ul.tabs").tabs("div.panes > div" ,{initialIndex: index} );

        // for myfeeds

        // loop over all elements creating a tooltip based on their data-tooltip-id attribute
        $('.myfeeds-tooltip-from-element').each(function() {
            var selector = '#' + $(this).data('tooltip-id');
            if(!$(this).attr("add")){
//                Tipped.create(this, $(selector)[0],{
//                    hook:   'lefttop'
//                });
//                $(this).attr("add",1);
                if(!$(this).attr("add")){
                    Tipped.create(this, $(selector)[0],{
                        hook:   'lefttop',
                        onShow: function(content, element) {
                            $("#"+$(element).attr("data-tooltip-id")+" .image-tooltip img.bimg").attr("src", $(element).find(".nc-large-image").attr("url") + "?width=260&height=146");
                        }

                    });
                    $(this).attr("add",1);
                }
            }
        });

        $('.box-hover').hover(function(){
            $(this).find('.contenthover').fadeIn("middle");
        }, function(){
            $(this).find('.contenthover').fadeOut("middle");
        });

    }


    nc_plugin.insertPostCategory = function(e){

    };

    nc_plugin.image_loader = function(){

        $('.nc-large-image').each(function() {
            if(!$(this).attr("add")){
                $(this).next().css("visibility", "visible");
                $(this).attr('src', $(this).attr("url")+"?width=70&height=41").load(function() {
                    $(this).attr("add",1)
                    $(this).next().css("visibility", "hidden");
                });
            }
        });

    };
    /**
     * add newscred api category list as
     * wp dewfault tag list
     */
    nc_plugin.add_post_category = function(category_str){
        $("#taxonomy-category .nc-cat-list").remove();
        var cat_list = category_str.split(",");
        if(cat_list){
            var cat_html_str = '<div class="tagchecklist nc-cat-list">';
            for(var i =0 ;  i < cat_list.length; i++){
                cat_html_str += '<span><a  class="ntdelbutton nc-new-category">X</a>&nbsp;'+cat_list[i]+'<input name="nc-cat-list[]" value="'+cat_list[i]+'" type="hidden" /></span>'
            }
            cat_html_str +="</div>";
            $("#taxonomy-category").append(cat_html_str);

        }
    };
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


    /*
     * metabox  search events
     */


    /**
     * enter search keyword event
     *  keyup
     */
    $("#nc-search").keyup( function(e){

        var query = $("#nc-search").val();

        // for enter key press
        if(e.which == 13)
            return false;

        if(query.length > 2){
            nc_plugin.autoSuggestion();
        }
    })
    /**
     * key press
     */

    $("#nc-search").keypress( function(e){
        var query = $("#nc-search").val();
        if(e.which == 13 && query != "enter keyword..."){
            e.preventDefault();
            nc_plugin.metaboxSearch(e);
            return false;
        }
    })


    // add source and topics filter from auto suggestiongs
    $(".nc-filter-source").live("click", function(){
        var self = $(this);
        var chk = 0;
        $(this).parent().toggleClass("nc-selected-filter")
        $(".nc-fliter-tag .tag").each(function(i){
            if( $(this).attr("guid") == self.attr("guid") ){
                chk = 1;
                $(this).remove();

            }
        })
        if(chk == 0){
            var html ='<span class="tag nc-auto-source" title="Source" guid="'+self.attr("guid")+'"><em class="source">S</em>'+$(this).text()+'&nbsp;<a class="topic_remove" href="javascript:void(0);"></a></span>';

            if($('.nc-fliter-tag').html()== "")
                html += '<a href="#" class="nc-filter-clearll" >Clear All</a>';

            $('.nc-fliter-tag').prepend(html)
        }

        $('div.auto-suggest-popup').fadeOut('medium');
        $("#nc-search").val("");

    });

    // add source and topics filter from auto suggestiongs
    $(".nc-filter-topics").live("click", function(){
        var self = $(this);
        var chk = 0;
        $(this).parent().toggleClass("nc-selected-filter")
        $(".nc-fliter-tag .tag").each(function(i){
            if( $(this).attr("guid") == self.attr("guid") ){
                chk = 1;
                $(this).remove();
                cosole.log($(".nc-fliter-tag .tag").length+">>>")

            }
        })
        if(chk == 0){
            var html ='<span class="tag nc-auto-topics" title="Topic" guid="'+self.attr("guid")+'"><em class="topic">T</em>'+$(this).text()+'&nbsp;<a class="topic_remove" href="javascript:void(0);"></a></span>';

            if($('.nc-fliter-tag').html()== "")
                html += '<a href="#" class="nc-filter-clearll" >Clear All</a>';

            $('.nc-fliter-tag').prepend(html)
        }
        $('div.auto-suggest-popup').fadeOut('medium');
        $("#nc-search").val("");

    });

    // remove the filter tag
    $('span.tag').live("click", function(){
        $(this).remove();
        if($(".nc-fliter-tag .tag").length == 0)
            $(".nc-fliter-tag").html("");

    })

    // close the auto suggestion
    $(".close-autosuggestion").live("click", function(){
        $('div.auto-suggest-popup').fadeOut('medium');
    })

    // clear all filters
    $(".nc-filter-clearll").live("click", function(){
        $(".nc-fliter-tag").html("");
    })



    //search articles and images
    $("#nc-search-submit").click(function(e){
        var query = $("#nc-search").val();

        if(query != "enter keyword..."){
            e.preventDefault();
            $('div.auto-suggest-popup').fadeOut('medium');
            nc_plugin.metaboxSearch(e);
        }
        return false;
    });

    //search articles and images by sort
    $("input[name='sort']").change(function(e){
        var query = $("#nc-search").val();
        if(query != "enter keyword..."){
            $('div.auto-suggest-popup').fadeOut('medium');
            nc_plugin.metaboxSearch(e);
        }
        return false;
    });

    // add the tab index

    $(".tabs li a").live("click", function(){
        $("#nc-current-tab").val($(this).attr("tab"))
    })


    /*
     Articles  scripts
     */

    // insert search articles

    $('.nc-article-title').live("click", function(){

        /* hide/show insert post or remove */

        $(this).hide();

        $(this).removeClass("nc-inactive-article");

        $(".nc-active-article").css({display: "none"});
        $(".nc-inactive-article").css({display: "block"});

        // check its myFeeds or articles for add remove link
        if( $("#nc-current-tab").val() == 3 ){

            $("#nc-article-title-removed3-"+$(this).attr("index")).css({display: "block"});

            $(this).addClass("nc-inactive-article");
            $("#nc-article-title-removed3-"+$(this).attr("index")).addClass("nc-active-article");

        }else{

            $("#nc-article-title-removed-"+$(this).attr("index")).css({display: "block"});

            $(this).addClass("nc-inactive-article");
            $("#nc-article-title-removed-"+$(this).attr("index")).addClass("nc-active-article");

        }

        //$("#content-tmce").trigger("click")
        $("#title-prompt-text").html("");

        /* add title */
        var title = $(this).closest("li").find("h2 a").html();
        $("#title").val(title)

        /* add content  */
        var content = $(this).closest("li").find(".hidden-description").html();

        // check tinyMCE active or not
        var tinyMceActive = $(".tmce-active").length;

        if(tinyMceActive){
            tinyMCE.get('content').setContent(content);
        }else{
            $("#content").val("");
            $("#content").insertAtCaret(content)
        }


        /* add tags */
        if($("#nc_tags").val()){
            var tags = $(this).closest('li').find(".nc-tags").html();
            $("#new-tag-post_tag").val(tags);
        }
        /* add category */
        //$("#nc-selected-category").val( $(this).next().next().html() );
        if($("#nc_categories").val()){
            nc_plugin.add_post_category($(this).next().next().html());
//            $("#category-add").slideDown();
//            $("#category-add-toggle").click()
//            $("#newcategory").val($(this).next().next().html())
        }

        //$("#category-add-submit").click();
        /* add publish date */
        if($("#nc_publish_time").val()){

            var mm = $(this).closest(".contenthover").find(".nc-publish-date .nc-mm").html();
            var dd = $(this).closest(".contenthover").find(".nc-publish-date .nc-dd").html();
            var yy = $(this).closest(".contenthover").find(".nc-publish-date .nc-yy").html();
            var hh = $(this).closest(".contenthover").find(".nc-publish-date .nc-hh").html();
            var ii = $(this).closest(".contenthover").find(".nc-publish-date .nc-ii").html();

            //$(".edit-timestamp").trigger( "click" );

            $( "#mm").val(mm);
            $( "#jj").val(dd)
            $( "#aa").val(yy);
            $( "#hh").val(hh)
            $( "#mn").val(ii);

            //$( ".save-timestamp").trigger( "click" );
        }

        /* add author */
        var author = $(this).closest(".contenthover").find(".nc-publish-date .nc-author").html();
        $("#nc-post-author").val(author);
        $("#nc-add-post").val(1);

        /* add post image_set */

        var image_set_html = $(this).closest(".contenthover").find(".nc_hidden_image_sets").html();
        $("#nc-image-set-content .inside").html(image_set_html);

        $("#nc-image-set-div").slideUp();
        $("#nc-image-set-div").html($("#nc-image-set-content").html());
        $("#nc-image-set-div").slideDown();

        // loop over all elements creating a tooltip based on their data-tooltip-id attribute


        // loop over all elements creating a
        // tooltip based on their data-tooltip-id attribute for myFeeds
        //console.log('.image-set-thumbnail-box2'+$(this).attr("index"))
        if( $("#nc-current-tab").val() == 3 ){
            $('.image-set-thumbnail-box2'+$(this).attr("index")+' li').each(function() {
                var selector = '#' + $(this).data('tooltip-id');
                Tipped.create(this, $(selector)[0],{
                    hook:   'lefttop',
                    onShow: function(content, element) {
                        $("#"+$(element).attr("data-tooltip-id")+" .image-tooltip img.bimg").attr("src", $(element).find(".nc-large-image").attr("url") + "?width=260&height=146");
                    }
                });
            });
        }else{
            $('.image-set-thumbnail-box'+$(this).attr("index")+' li').each(function() {
                var selector = '#' + $(this).data('tooltip-id');
                Tipped.create(this, $(selector)[0],{
                    hook:   'lefttop',
                    onShow: function(content, element) {
                        $("#"+$(element).attr("data-tooltip-id")+" .image-tooltip img.bimg").attr("src", $(element).find(".nc-large-image").attr("url") + "?width=260&height=146");
                    }
                });
            });
        }
        nc_plugin.image_loader();
        return false;
    });

    $('.nc-article-title-removed').live("click", function(){

        $(this).css({display: "none"});
        $(".nc-article-title").show();

        $("#title").val("")

        // check tinyMCE active or not
        var tinyMceActive = $(".tmce-active").length;

        if(tinyMceActive){
            tinyMCE.get('content').setContent("");
        }else{
            $("#content").val("");
        }

        $("#nc-image-set-content .inside").html("");
        $("#nc-image-set-div").slideUp();

        return false;
    });

    /*
     * insert post category
     */
    $("#save-post").click(function(){

    });
    /*
     Image scripts
     */
    /**
     * insert to post : image
     */

    $(".nc-insert-to-post").live("click", function(e){

        e.preventDefault();

        var width = $(this).closest(".image-tooltip").find(".post_width").val();
        var height = $(this).closest(".image-tooltip").find(".post_height").val();

        var actual_width = $(this).closest(".image-tooltip").find(".post_width").attr("actual_value");
        var actual_height = $(this).closest(".image-tooltip").find(".post_height").attr("actual_value");

        var default_width = $(this).closest(".image-tooltip").find(".post_width").attr("default_value");
        var default_height = $(this).closest(".image-tooltip").find(".post_height").attr("default_value");


        if( width == actual_width )
            width = default_width;

        if( height == actual_height )
            height = default_height;
        var img_url = $(this).closest(".image-tooltip").find(".nc-large-image-url").attr("large_url");
        var caption = $(this).closest(".image-tooltip").find(".image-caption").val();
        var source = $(this).closest(".image-tooltip").find(".nc-image-source").html();

        var caption_html = '[caption align="alignnone" width="'+width+'" height="'+height+'" ]'+
                                '<a href="'+img_url+'">' +
                                    '<img class="size-medium" title="'+caption+'" src="'+img_url+'?width='+width+'&height='+height+'" alt="'+caption+'" width="'+width+'" height="'+height+'" />' +
                                '</a>'+ caption + ' <strong style="font-style: italic;display: block">'+ source + '</strong>' +
                            '[/caption]';

        // check tinyMCE active or not
        var tinyMceActive = $(".tmce-active").length;

        if(tinyMceActive){
            var ed = tinyMCE.getInstanceById('content');
            ed.focus();
            ed.selection.setContent(caption_html);
            $("#content-html").trigger("click");
            $("#content-tmce").trigger("click");

        }else{
            $("#content").insertAtCaret(caption_html)

        }

    });

    /**
     * insert image as feature image
     */

    $(".nc-add-feature-image").live( "click", function(){


        var query_data =  {};

        var p_id = $("#post_ID").val();

        query_data['p_id'] = p_id;
        query_data['url'] = $(this).attr("url");
        query_data['caption'] = $(this).closest(".image-tooltip").find(".image-caption").val();
        query_data['action'] = "ncajax-add-image"

        var self = $(this);
        var index = $(this).attr("index");

        if(index == -1){
            $(".nc-image-set-loading").css("visibility", "visible");
        }else{
            $("#nc-large-image-"+index).css("opacity" , "0.1" );
            $("#nc-add-iamge-loading-"+index).css("visibility", "visible");
        }

        $.ajax({
            url: NcAjax.ajaxurl,
            type:'POST',
            data:query_data,
            success:function (response) {
                if(index == -1){
                    $(".nc-image-set-loading").css("visibility", "hidden");
                }else{
                    $("#nc-large-image-"+self.attr("index")).css("opacity" , "1" );

                    $("#nc-add-iamge-loading-"+self.attr("index")).css("visibility", "hidden");
                }
                // add as feature image
                $("#postimagediv .inside").html(response)


            },
            error:function () {
                if(index == -1){
                    $(".nc-image-set-loading").css("visibility", "hidden");
                }else{
                    $("#nc-add-iamge-loading-"+self.attr("index")).css("visibility", "hidden");
                }

            }
        });
		return false;
    } );


    /**
     * remove the feature image
     */

    $("#nc-remove-post-thumbnail").live("click", function(){

        var query_data =  {};
        var p_id = $("#post_ID").val();
        query_data['p_id'] = p_id;
        query_data['action'] = "ncajax-remove-feature-image"

        $.ajax({
            url: NcAjax.ajaxurl,
            type:'POST',
            data:query_data,
            success:function (response) {
                $("#postimagediv .inside").html(response)
            },
            timeout:90000,
            error:function () {

            }
        });


    });

    // select myFeeds for query

    $("#myFeeds").live("change", function(){
        var val = $(this).val();
        if(val){
            myfeeds_page = 1;
            nc_plugin.searchMyFeeds();
        }
    });


    // add image_set div after post text area for insert set images
    var image_set_html = '<div id="nc-image-set-div" class="postbox"></div>';
    $("#normal-sortables").prepend(image_set_html);

    /**
     * insert auto publish  post image sets
     * if nc_image_set post meta exist
     */

    if(getURLParameter("post") != "null"){


        var query_data =  {};
        var post_id = getURLParameter("post");

        query_data['post_id'] = post_id;
        query_data['action'] = "ncajax-add-article-image-set"

        // add image set
        $.ajax({
            url: NcAjax.ajaxurl,
            type:'POST' ,
            data:query_data ,
            success:function (response) {
                if( response != 0){

                    var image_set_html = response;
                    $("#nc-image-set-content .inside").html(image_set_html);
                    $(".nc_hidden_image_sets").css("display", "block");
                    $("#nc-image-set-div").slideUp  ();
                    $("#nc-image-set-div").html($("#nc-image-set-content").html());
                    $("#nc-image-set-div").slideDown();

                    // loop over all elements creating a tooltip based on their data-tooltip-id attribute
                    $('.image-set-thumbnail-box0 li').each(function() {
                        var selector = '#' + $(this).data('tooltip-id');
                        Tipped.create(this, $(selector)[0],{
                            hook:   'lefttop'
                        });
                    });
                }
            },
            timeout:90000,
            error:function () {

            }
        });

        // add post author when edit


    }
    // load the tab
    $.NC_APP("ul.tabs").tabs("div.panes > div");

    // remove the category from list
    $(".nc-cat-list span a.nc-new-category").live("click",function(e){
        $(this).parent().remove();
    });

})(jQuery);


function getURLParameter(name) {
    return decodeURI(
        (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]
    );
}
