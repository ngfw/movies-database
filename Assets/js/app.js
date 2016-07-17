/*! jQuery Ajax Queue v0.1.2pre | (c) 2013 Corey Frang | Licensed MIT */
(function(e){var r=e({});e.ajaxQueue=function(n){function t(r){u=e.ajax(n),u.done(a.resolve).fail(a.reject).then(r,r)}var u,a=e.Deferred(),i=a.promise();return r.queue(t),i.abort=function(o){if(u)return u.abort(o);var c=r.queue(),f=e.inArray(t,c);return f>-1&&c.splice(f,1),a.rejectWith(n.context||n,[i,o,""]),i},i}})(jQuery);


jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + $(window).scrollTop()) + "px");
    this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
    return this;
}
jQuery.fn.exist = function(){
  return jQuery(this).length > 0;
}

var delay = (function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};


$(function(){

  var app = {
      mobile: false,
      menuOpen: false,
      menuWrapper: function(){
          return $(".menu-wrapper");
      },
      init: function(){
          app.info("Initialing Application");
          app.mobile = app.checkMobile();
          if(!app.mobile){
              app.openPartMenu();
          }
          app.events();
          app.layoutFix();
          app.moviePageHeader();
          app.zoombox();
          app.tabs();
          app.getPageView();
          app.languageListener();
          app.initSearch();
          app.initSurpriseButton();
      },
      languageListener:function(){
        $(".setLanguage").on('click', function(e){
          var lang = $(this).data("language");
          Cookies.set('lang', lang, { expires: 60*60*24*365 });
          $("body").fadeOut("fast", function(){
            return;
          });
        });
      },
      initSearch:function(){
        $("#search").submit(function( e ) {
          e.preventDefault();
          var val = $(".search-field").val();
          window.location.href = baseURL + requestedLanguage + "/search/" + val;
          return false;
        });
      },
      initSurpriseButton:function(){
        $(".surprise").on('click', function(){
          var url = $(this).data("url");
          window.location.href = url; 
        });
      },
      getPageView: function(){
        if( $(".pageView").exist() ){
          var id = $(".pageView").data("id");
          if($.isNumeric(id)){
            $.ajax({
              type: "POST",
              url: baseURL+requestedLanguage+"/ajax",
              data: { page: id, get: "views" }
            }).done(function( count ) {
              $(".pageView").html("").html(count);
            });
          }
        }
      },
      log:function($str){
        if(typeof console != "undefined") {
          console.log($str);
        }
      },
      debug:function($str){
        if(typeof console != "undefined") {
          console.debug($str);
        }
      },
      info:function($str){
        if(typeof console != "undefined") {
          console.info($str);
        }
      },
      warn:function($str){
        if(typeof console != "undefined") {
          console.warn($str);
        }
      },
      error:function($str){
        if(typeof console != "undefined") {
          console.error($str);
        }
      },
      layoutFix:function(){
        $(".container-item").hover(function() {
            setTimeout(function() {
                $(".container-item").css("z-index","99")
            }, 200);
        });
        
        setTimeout(function(){
            $(".popout-menu").show();
            $(".item-overlay").show();
        },50);
      },
      moviePageHeader:function(){
        if ( $( ".movieheader" ).length ) {
          $(".movieheader").backstretch($(".movieheader").data("image"), {centeredY:false});
          $(window).on("backstretch.after", function (e, instance, index) {
              $(".backstretchLoader").fadeOut("fast",function(){
                $(this).remove();       
                var img = $(".backstretch").find("img");
                img.animate({
                    "top":"-=50"
                },3000);
              });
          });
        }
      },
      zoombox:function(){
        $('a.zoombox').zoombox();
        $(".allPosters").on("click", function(){
            $(".mainPosterLink").trigger("click");
            return false;
        });
      },
      tabs:function(){
        $('#movieTab, #ProfileTab').on("click", "a", function (e) {
          $(this).tab('show');
          return false;
        });

      },
      checkMobile: function(){
          var check = false;
          (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
          return check; 
      },
      openPartMenu: function(){
          app.menuWrapper().addClass("open-part");
      },
      events: function(){
          $("[rel='tooltip']").tooltip();
          app.menuWrapper().hover(
            function () {
              $(this).addClass('open-all');
              app.menuOpen = true;
            },
            function () {
              $(this).removeClass('open-all');
              app.menuOpen = false;
            }
          );
          
          $("#menu").on("click", ".menuIcon", function(){
              if(app.menuOpen){
                  app.menuWrapper().removeClass('open-all');
                  app.menuOpen = false;
              }else{
                  app.menuWrapper().addClass('open-all');
                  app.menuOpen = true;
              }
          });
      }
  }
    
  var socialButtons = {
    init: function(){
      app.info("Initialing Social Buttons");
      if( $('.twitterShare').exist() ){
        socialButtons.twitter();
      }else{
        app.warn("twitter button could not be initialized ");
      }
      if( $('.facebookShare').exist() ){
        socialButtons.facebook();
      }else{
        app.warn("facebook share button could not be initialized ");
      }
      if( $('.gplusShare').exist() ){
        socialButtons.google();
      }else{
        app.warn("G plus button could not be initialized ");
      }
      if( $('.pinitShare').exist() ){
        socialButtons.pinterest();
      }else{
        app.warn("pinit button could not be initialized ");
      }
      socialButtons.posterShareButtons();
    },
    twitter: function(){
      $(".twitterShare").on("click", function(){
        var text = $(this).data("text");
        window.open(
          'http://twitter.com/share?text='+encodeURIComponent(text), 
          'twitter-share-dialog', 
          'width=600,height=260').focus();
        return false;
      });
    },
    facebook:function(){
      $(".facebookShare").on("click", function(){
        var url = $(this).data("url");
        window.open(
          'https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent(url), 
          'facebook-share-dialog', 
          'width=626,height=436').focus(); 
        return false;
      });
    },
    google:function(){
      $(".gplusShare").on("click", function(){
        var url = $(this).data("url");
        window.open(
          'https://plus.google.com/share?url='+encodeURIComponent(url), 
          'facebook-share-dialog', 
          'scrollbars=yes,width=490,height=500').focus();
        return false;
      });
    },
    pinterest:function(){
      $(".pinitShare").on("click", function(){
        var url = $(this).data("url");
        var media = $(this).data("media");
        var description = $(this).data("description");
        window.open(
          'http://www.pinterest.com/pin/create/button/?url='+encodeURIComponent(url)+'&media='+encodeURIComponent(media)+'&description='+encodeURIComponent(description), 
          'pinit-share-dialog', 
          'width=750,height=316').focus();
        return false;
      });
    },
    posterShareButtons:function(){
      $(".share-btn").mouseenter(function() {
        var key = $(this).data("key");
        setTimeout(function() {
            $(".popout"+key).addClass("visible")
        }, 200);
      });
      $(".share-btn").mouseleave(function() {
          var key = $(this).data("key");
          setTimeout(function() {
              $(".item-menu").removeClass("visible")
          }, 200);
      });
    }
  };

  var facebook = {
    accessToken:false,
    avatarURL:false,
    me:false,
    userid: false,
    userNotLoggedIn: false,
    favorite:1, 
    wanttowatch:1, 
    watched:1,
    init:function(){
      app.info("Initialing Facebook integration");
      if(facebook.enabled()){
        FB.init({
          appId: FB_APP_ID,
          channelUrl: channelUrl,
          status: true,
          cookie: true,
          xfbml: true
        });
        facebook.addLoginListener();
        facebook.addLogoutListener();
        
        FB.getLoginStatus(function (response) {
          if (response.status === 'connected') {
            uid = response.authResponse.userID;
            facebook.accessToken = response.authResponse.accessToken;
            facebook.avatarURL = "https://graph.facebook.com/" + uid + "/picture";
            facebook.registerUser();
            facebook.showUserHeader();
            facebook.showUserButtons();
            facebook.userButtonsListener();
            facebook.getProfilePage();
            facebook.getProfileSettings();
          } else {
            facebook.userNotLoggedIn=true;
            facebook.showUserLogin();
            facebook.getProfilePage();
            facebook.denyProfileSettings();
          }
        });
      }else{
        facebook.getProfilePage();
        facebook.denyProfileSettings();
        app.warn("Facebook intergation is disabled, FB_APP_ID is not found");
      }
    },
    enabled:function(){
      if(typeof FB_APP_ID != 'undefined' && FB_APP_ID.length >0){
        return true;
      }else{
        return false;
      }
    },
    getProfileSettings:function(){
      if( $(".profileSettingsLoader").exist() ){
        var profileid = $(".profileSettingsLoader").data("user");
        app.info("Getting Profile Settings");
        $.ajax({
          type: "POST",
          dataType: "json",
          url: baseURL+requestedLanguage+"/ajax",
          data: { profileid: profileid, accessToken: facebook.accessToken, get: "profilePageSettings" }
        }).done(function( response ) {
          $(".profileSettingsLoader").hide();
          if(response.status == "ok"){
            $(".settingsResult").removeClass("hide");
            if(response.privacy == "1"){
              $('.privacy').prop('checked', true);
            }
            facebook.addPrivacyListener();
          }else{
            $(".settingsResult").remove();
            facebook.denyProfileSettings();
          }
        });
      }
    },
    addPrivacyListener: function(){
      $('.privacy').change(function() {
        var loaderImage = $("<img />").attr("src", "Assets/img/loading2.gif").addClass("loaderImage").appendTo(".checkbox");
        var profileid = $(".profileSettingsLoader").data("user");
        if($('.privacy').prop('checked')){
          var privacyValue = 1;
        }else{
          var privacyValue = 0;
        }
        $.ajax({
          type: "POST",
          dataType: "json",
          url: baseURL+requestedLanguage+"/ajax",
          data: { profileid: profileid, accessToken: facebook.accessToken, privacy: privacyValue, get: "changePrivacy" }
        }).done(function( response ) {
          $(".loaderImage").remove();
          /*if(response.status == "ok"){
          }else{
          }*/
        });
      });
    },
    denyProfileSettings:function(){
      $(".profileDenied").removeClass("hide");
    },
    getProfilePage:function(){
      if( $(".checkUserPermission").exist() ){
        var profileid = $(".checkUserPermission").data("user");
        app.info("Getting Profile Page");
        if(facebook.enabled()){
          var counter=0;
          var checkFB = setInterval(function(){ 
            if(counter >= 100){
              clearInterval(checkFB);
              app.error("Failed to find userid");
            }
            if(facebook.accessToken || facebook.userNotLoggedIn){
              clearInterval(checkFB);
              facebook.profilePageAjaxLoader(profileid);
            }
          }, 100);
        }else{
          facebook.profilePageAjaxLoader(profileid);
        }
      }
    },
    profilePageAjaxLoader: function(profileid){
      $.ajax({
        type: "POST",
        dataType: "json",
        url: baseURL+requestedLanguage+"/ajax",
        data: { profileid: profileid, accessToken: facebook.accessToken, get: "profilePage", favorite: facebook.favorite, wanttowatch: facebook.wanttowatch, watched: facebook.watched }
      }).done(function( response ) {
        if(response.status == "ok"){
          facebook.addProfilesMovies(response.data);
          $(".checkUserPermission").hide(); 
          $(".profileTabContent").removeClass("hide");
          $(".profileTabNav").removeClass("hide");
        }else if(response.status == "done"){
          app.warn("Loading is done (public)");
        }else{
          facebook.denyUserProfile();
        }
      });
    },
    addProfilesMovies:function(data){
      var obj_length = Object.size(data);
      $.each(data, function( index, value ) {
        switch(index){
          case "favorite":
            $.each(value, function( k, v ){
              var link = $("<a />").attr("href", "javascript:void(0)").addClass("preloadMovie").addClass("preloadedMovie").addClass("thumbnail").addClass("with-caption").data("movieid", v.MovieID).data("type", index);
              var loaderImage = $("<img />").attr("src", "Assets/img/loading2.gif").appendTo(link);
              $("#favorite").append(link);
            });
            if(value.length == Number_Of_Profile_Movies){
              $("<div />").addClass("clearfix").html("<br />").appendTo($("#favorite"));
              $("<a />").addClass("btn").addClass("btn-default").addClass("loadMoreProfile").attr("href", "javascript:void(0);").data("type", "favorite").text(loadmoretext).appendTo($("#favorite"));
            }
            if(value.length == 0 && obj_length>2){
              $("<div />").addClass("clearfix").html('<br />'+noResults).appendTo($("#favorite"));
            }
          break;
          case "wanttowatch":
            $.each(value, function( k, v ){
              var link = $("<a />").attr("href", "javascript:void(0)").addClass("preloadMovie").addClass("preloadedMovie").addClass("thumbnail").addClass("with-caption").data("movieid", v.MovieID).data("type", index);
              var loaderImage = $("<img />").attr("src", "Assets/img/loading2.gif").appendTo(link);
              $("#wanttowatch").append(link);
            });
            if(value.length == Number_Of_Profile_Movies){
              $("<div />").addClass("clearfix").html("<br />").appendTo($("#wanttowatch"));
              $("<a />").addClass("btn").addClass("btn-default").addClass("loadMoreProfile").attr("href", "javascript:void(0);").data("type", "wanttowatch").text(loadmoretext).appendTo($("#wanttowatch"));
            }
            if(value.length == 0 && obj_length>2){
              $(".wanttowatchTabHeader").hide();
            }
          break;
          case "watched":
            $.each(value, function( k, v ){
              var link = $("<a />").attr("href", "javascript:void(0)").addClass("preloadMovie").addClass("preloadedMovie").addClass("thumbnail").addClass("with-caption").data("movieid", v.MovieID).data("type", index);
              var loaderImage = $("<img />").attr("src", "Assets/img/loading2.gif").appendTo(link);
              $("#watched").append(link);
            });
            if(value.length == Number_Of_Profile_Movies){
              $("<div />").addClass("clearfix").html("<br />").appendTo($("#watched"));
              $("<a />").addClass("btn").addClass("btn-default").addClass("loadMoreProfile").attr("href", "javascript:void(0);").data("type", "watched").text(loadmoretext).appendTo($("#watched"));
            }
            if(value.length == 0 && obj_length>2){
              $(".watchedTabHeader").hide();
            }
          break;
        }
      });
      facebook.checkAwaitingMovieAndLoad();
      facebook.loadMoreListener();
    },
    checkAwaitingMovieAndLoad:function(){
      $(".preloadMovie").each(function( index, element ){
          var $this = $(this);
          var movieID = $this.data("movieid");
          $.ajaxQueue({
            type: "POST",
            dataType: "json",
            url: baseURL+requestedLanguage+"/ajax",
            data: { get: "movie", id: movieID}
          }).done(function( response ) {
            $this.removeClass("preloadMovie");
            $this.find("img").attr("src", response.poster);
            $("<p />").html(response.title).appendTo($this);
            var url = baseURL+requestedLanguage+"/movie/"+response.id+"/"+encodeURIComponent(response.title).replace(/%20/g,"+");
            $this.attr("href", url);
            
            if(facebook.me && facebook.userid == $(".checkUserPermission").data("user")){
              var removeButton = $("<a />").attr("href", "javascript:void(0);")
              .addClass("profilePageMovieRemove").data("action","remove").data("type",$this.data("type"))
              .data("mid",response.id).html("&nbsp;").appendTo($this);
              facebook.profilePageRemoveButtonListener();
            }
          });
      });
    },
    loadMoreListener:function(){
      $(".loadMoreProfile").unbind('click').on("click", function(){
        var type = $(this).data("type");
        var profileid = $(".checkUserPermission").data("user");
        $this = $(this);
        switch(type){
          case "favorite":
            facebook.favorite++;
            var loadData = { profileid: profileid, accessToken: facebook.accessToken, get: "profilePage", favorite:facebook.favorite };
          break;
          case "wanttowatch":
            facebook.wanttowatch++;
            var loadData = { profileid: profileid, accessToken: facebook.accessToken, get: "profilePage", wanttowatch:facebook.wanttowatch };
          break;
          case "watched":
            facebook.watched++;
            var loadData = { profileid: profileid, accessToken: facebook.accessToken, get: "profilePage", watched:facebook.watched };
          break;
        }
        $this.addClass("disabled").html("");
        $("<img />").attr("src", "Assets/img/loading2.gif").appendTo($this);
        $.ajaxQueue({
          type: "POST",
          dataType: "json",
          url: baseURL+requestedLanguage+"/ajax",
          data: loadData
        }).done(function( response ) {
          $this.remove();
          if(response.status == "ok"){
            facebook.addProfilesMovies(response.data);
            $(".checkUserPermission").hide(); 
            $(".profileTabContent").removeClass("hide");
            $(".profileTabNav").removeClass("hide");
          }else if(response.status == "done"){
            app.warn("Loading is done (public)");
          }else{
            facebook.denyUserProfile();
          }
        });
      });
    },
    profilePageRemoveButtonListener:function(){
      $(".profilePageMovieRemove").unbind('click').on("click", function(){
        var $this = $(this);
        $this.parent().fadeTo("fast" , 0.5, function(){
          var type = $this.data('type');
          var action = $this.data('action');
          var mid = $this.data('mid');
          $.ajax({
            type: "POST",
            dataType: "json",
            url: baseURL+requestedLanguage+"/ajax",
            data: { accessToken: facebook.accessToken, id: mid, type: type, action: action, get:"userButtonClick" }
          }).done(function( response ) {
            if(response.status == "ok"){
              $this.parent().fadeOut("slow", function(){
                $(this).remove();
              });
            }
          });
        });
      });
    },
    denyUserProfile:function(){
      $(".checkUserPermission").hide();
      $(".profileDenied").removeClass("hide");
    },
    addLoginListener:function(){
      $(".fblogin").on("click", function () {
        FB.login(function (response) {
          if (response.authResponse) {
            location.reload();
          } else {
            //Login is cancelled 
          }
        }, {scope: 'email,user_likes,publish_stream'});
        return false;
      }); 
    },
    addLogoutListener:function(){
      $(".fblogout").on("click", function () {
        FB.logout(function (response) {
          location.reload();
        });
        return false;
      });
    },
    registerUser:function(){
      FB.api('/me', function (response) {
        facebook.me = response;
        $(".facebookName").append('<span class="custom-xs-hide">'+facebook.me.name+'</span>');
        $.ajax({
          type: "POST",
          url: baseURL+requestedLanguage+"/ajax",
          data: { accessToken: facebook.accessToken, get: "register" }
        }).done(function( user ) {
          if(user != "0"){
            facebook.userid = user;
          }
        });
      });
    },
    showUserHeader:function(){
      var counter=0;
      var checkFB = setInterval(function(){ 
        if(counter >= 100){
          clearInterval(checkFB);
          app.error("Failed to find userid");
        }
        if(facebook.userid){
          clearInterval(checkFB);
          $(".fbloginWrapper").remove();
          $(".fblogoutwrapper").find("img").attr("src", facebook.avatarURL);
          var encodedName = encodeURIComponent(facebook.me.name).replace(/%20/g,"+");
          $(".profileLink").attr("href", baseURL+requestedLanguage+"/user/"+facebook.userid+"/"+encodedName);
          $(".profileSettings").attr("href", baseURL+requestedLanguage+"/user/"+facebook.userid+"/"+encodedName+"/page/settings");
          $(".fblogoutwrapper").removeClass("hide");
          //$(".fbmessage").hide();
         }
        counter++;
      },100);
    },
    showUserLogin:function(){
      $(".fbloginWrapper").removeClass("hide");
      $(".fb-error-msg").removeClass("hide");
      $(".userbuttons-pre-loader").hide();
    },
    showUserButtons:function(){
      var counter=0;
      var checkFB = setInterval(function(){ 
        if(counter >= 100){
          clearInterval(checkFB);
          app.error("Failed to find Facebook user");
        }
        if(facebook.me){
          clearInterval(checkFB);
          if(typeof mid != 'undefined'){
            $.ajax({
              type: "POST",
              dataType: "json",
              url: baseURL+requestedLanguage+"/ajax",
              data: { accessToken: facebook.accessToken, id: mid, get: "userButtons" }
            }).done(function( response ) {
                $(".userbuttons-pre-loader").hide();
                $(".FavoriteWrapper, .FavoriteDivider").removeClass("hide");
                if(response.Favorite){
                  $(".FavoriteWrapper").find(".micon-plus").hide();
                  $(".FavoriteWrapper").find("a").data("action","remove");
                }else{
                  $(".FavoriteWrapper").find(".micon-munis").hide();
                }

                $(".WantToWatchWrapper, .WantToWatchDivider").removeClass("hide");
                if(response.WantToWatch){
                  $(".WantToWatchWrapper").find(".micon-plus").hide();
                  $(".WantToWatchWrapper").find("a").data("action","remove");
                }else{
                  $(".WantToWatchWrapper").find(".micon-munis").hide();
                }

                $(".WatchedWrapper, .WatchedDivider").removeClass("hide");
                if(response.Watched){
                  $(".WatchedWrapper").find(".micon-plus").hide();
                  $(".WatchedWrapper").find("a").data("action","remove");
                }else{
                  $(".WatchedWrapper").find(".micon-munis").hide();
                }
            });
          }
        }
        counter++;
      },100);
    },
    userButtonsListener:function(){
      $(".userbuttons").on('click', function(){
        var type = $(this).data('type');
        var action = $(this).data('action');
        var $this = $(this);
        $this.addClass("disabled");
        $.ajax({
          type: "POST",
          dataType: "json",
          url: baseURL+requestedLanguage+"/ajax",
          data: { accessToken: facebook.accessToken, id: mid, type: type, action: action, get: "userButtonClick" }
        }).done(function( response ) {
          if(response.status == "ok"){
            $this.removeClass("disabled");
            $this.find(".micon-munis").toggle();
            $this.find(".micon-plus").toggle();
          }
        });
      });
    }

  };
  
  app.init();
  socialButtons.init();
  facebook.init();

});
