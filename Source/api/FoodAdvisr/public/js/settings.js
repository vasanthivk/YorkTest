var site_settings = '<div class="ts-button">'
        +'<span class="fa fa-cogs fa-spin"></span>'
    +'</div>'
    +'<div class="ts-body">'
	    +'<div class="ts-title">Themes</div>'
        +'<div class="ts-themes">'
            +'<a href="#" data-theme="css/theme-default.css"><img src="../img/themes/default.jpg"/></a>'            
            +'<a href="#" data-theme="css/theme-brown.css"><img src="../img/themes/brown.jpg"/></a>'
            +'<a href="#" class="active" data-theme="css/theme-blue.css"><img src="../img/themes/blue.jpg"/></a>'                        
            +'<a href="#" data-theme="css/theme-white.css"><img src="../img/themes/light.jpg"/></a>'            
            +'<a href="#" data-theme="css/theme-black.css"><img src="../img/themes/black.jpg"/></a>'
        +'</div>'
		+'<div class="ts-title">Layout</div>'
        +'<div class="ts-row">'
            +'<label class="check"><input type="radio" class="iradio" name="st_layout_boxed" value="0" checked/> Full Width</label>'
        +'</div>'
        +'<div class="ts-row">'
            +'<label class="check"><input type="radio" class="iradio" name="st_layout_boxed" value="1"/> Boxed</label>'
        +'</div>'
        +'<div class="ts-title">Options</div>'
        +'<div class="ts-row">'
            +'<label class="check"><input type="checkbox" class="icheckbox" name="st_head_fixed" value="1"/> Fixed Header</label>'
        +'</div>'
        +'<div class="ts-row">'
            +'<label class="check"><input type="checkbox" class="icheckbox" name="st_sb_fixed" value="1" checked/> Fixed Sidebar</label>'
        +'</div>'
        +'<div class="ts-row">'
            +'<label class="check"><input type="checkbox" class="icheckbox" name="st_sb_scroll" value="1"/> Scroll Sidebar</label>'
        +'</div>'
        +'<div class="ts-row">'
            +'<label class="check"><input type="checkbox" class="icheckbox" name="st_sb_right" value="1"/> Right Sidebar</label>'
        +'</div>'
        +'<div class="ts-row">'
            +'<label class="check"><input type="checkbox" class="icheckbox" name="st_sb_custom" value="1"/> Custom Navigation</label>'
        +'</div>'
        +'<div class="ts-row">'
            +'<label class="check"><input type="checkbox" class="icheckbox" name="st_sb_toggled" value="1"/> Toggled Navigation</label>'
        +'</div>'
        
        
    +'</div>';
    
var settings_block = document.createElement('div');
    settings_block.className = "theme-settings";
    settings_block.innerHTML = site_settings;
    document.body.appendChild(settings_block);

$(document).ready(function(){

    /* Default settings */
    var theme_settings = {
        st_head_fixed: 0,
        st_sb_fixed: 1,
        st_sb_scroll: 1,
        st_sb_right: 0,
        st_sb_custom: 0,
        st_sb_toggled: 0,
        st_layout_boxed: 0
    };
    /* End Default settings */
    localStorage.setItem("theme_settings", false);
    set_settings(theme_settings,localStorage.getItem("theme_settings"));    
    
    $(".theme-settings input").on("ifClicked",function(){
        
        var input   = $(this);

        if(input.attr("name") != 'st_layout_boxed'){
                
            if(!input.prop("checked")){
                localStorage.setItem("st_layout_boxed", input.val());
            }else{    
                localStorage.setItem("st_layout_boxed", 0);         
            }
            
        }else{
             localStorage.setItem("st_layout_boxed", input.val());   
        }
      theme_settings[input.attr("name")] = localStorage.getItem("st_layout_boxed");


        /* Rules */
        if(input.attr("name") === 'st_sb_fixed'){
            if(theme_settings.st_sb_fixed == 1){
                localStorage.setItem("st_sb_fixed", 1);
                          }else{
                localStorage.setItem("st_sb_fixed", 0);
            }
              theme_settings.st_sb_scroll = localStorage.getItem("st_sb_fixed");

        }
        
        if(input.attr("name") === 'st_sb_scroll'){
            if(theme_settings.st_sb_scroll == 1 && theme_settings.st_layout_boxed == 0){
                localStorage.setItem("st_sb_scroll", 1);
            }else if(theme_settings.st_sb_scroll == 1 && theme_settings.st_layout_boxed == 1){
                localStorage.setItem("st_sb_scroll", -1);
            }else if(theme_settings.st_sb_scroll == 0 && theme_settings.st_layout_boxed == 1){
                localStorage.setItem("st_sb_scroll", -1);
            }else{
                 localStorage.setItem("st_sb_scroll", 0);

            }
             theme_settings.st_sb_fixed = localStorage.getItem("st_sb_scroll");
        }
        
        if(input.attr("name") === 'st_layout_boxed'){
            if(theme_settings.st_layout_boxed == 1){ 
                localStorage.setItem("st_head_fixed", -1);               
                localStorage.setItem("st_sb_fixed", -1);
                localStorage.setItem("st_sb_scroll", 1);
            }else{
                localStorage.setItem("st_head_fixed", 0); 
                localStorage.setItem("st_sb_fixed", 1); 
                localStorage.setItem("st_sb_scroll", 1); 
            }
            theme_settings.st_head_fixed    = localStorage.getItem("st_head_fixed");
            theme_settings.st_sb_fixed      = localStorage.getItem("st_sb_fixed");
            theme_settings.st_sb_scroll     = localStorage.getItem("st_sb_scroll");
        }
        /* End Rules */
        localStorage.setItem("theme_settings", input.attr("name"));
        set_settings(theme_settings,localStorage.getItem("theme_settings"));
    });
    
    /* Change Theme */
    $(".ts-themes a").click(function(){
        localStorage.setItem("ts-themes", "active");
        $(".ts-themes a").removeClass(localStorage.getItem("ts-themes"));
        $(this).addClass(localStorage.getItem("ts-themes"));
        localStorage.setItem("href", "theme");
        $("#theme").attr("href",$(this).data(localStorage.getItem("href")));
        return false;
    });
    /* END Change Theme */
    
    /* Open/Hide Settings */
    $(".ts-button").on("click",function(){
        localStorage.setItem("theme-settings", "active");
        $(".theme-settings").toggleClass(localStorage.getItem("theme-settings"));
    });
    /* End open/hide settings */
});

function set_settings(theme_settings,option){
    
    /* Start Header Fixed */
    if(theme_settings.st_head_fixed == 1)
    {
        localStorage.setItem("page-container", "page-navigation-top-fixed");
        $(".page-container").addClass(localStorage.getItem("page-container"));
    }
    else
    {
        localStorage.setItem("page-container", "page-navigation-top-fixed");
        $(".page-container").removeClass(localStorage.getItem("page-container"));    
    }
    /* END Header Fixed */
    
    /* Start Sidebar Fixed */
    if(theme_settings.st_sb_fixed == 1){  
        localStorage.setItem("page-sidebar", "page-container");      
        $(".page-sidebar").addClass(localStorage.getItem("page-container"));
    }else
        localStorage.setItem("page-sidebar", "page-container");       
        $(".page-sidebar").removeClass(localStorage.getItem("page-container"));
    /* END Sidebar Fixed */
    
    /* Start Sidebar Fixed */
    if(theme_settings.st_sb_scroll == 1){          
        $(".page-sidebar").addClass("scroll").mCustomScrollbar("update");        
    }else
        $(".page-sidebar").removeClass("scroll").css("height","").mCustomScrollbar("disable",true);
    
    /* END Sidebar Fixed */
    
    /* Start Right Sidebar */
    /********************************************************/
    if(theme_settings.st_sb_right == 1)        
        $(".page-container").addClass("page-mode-rtl");
    else        
        $(".page-container").removeClass("page-mode-rtl");
    /* END Right Sidebar */
    
    /* End call resize window */

/* Start Custom Sidebar */
    if(theme_settings.st_sb_custom == 1)        
        $(".page-sidebar .x-navigation").addClass("x-navigation-custom");
    else        
        $(".page-sidebar .x-navigation").removeClass("x-navigation-custom");
    /* END Custom Sidebar */
    
    
    /* Start Layout Boxed */
    if(theme_settings.st_layout_boxed == 1)
        $("body").addClass("page-container-boxed");
    else
        $("body").removeClass("page-container-boxed");
    /* END Layout Boxed */
    
    /* Set states for options */
    if(option === false || option === 'st_layout_boxed' || option === 'st_sb_fixed' || option === 'st_sb_scroll'){        
        for(option in theme_settings){
            set_settings_checkbox(option,theme_settings[option]);
        }
    }
    /* End states for options */
    
    /* Call resize window */
    $(window).resize();
    
}

function set_settings_checkbox(name,value){
    
    if(name == 'st_layout_boxed'){    
        
        $(".theme-settings").find("input[name="+name+"]").prop("checked",false).parent("div").removeClass("checked");
        
        var input = $(".theme-settings").find("input[name="+name+"][value="+value+"]");
                
        input.prop("checked",true);
        input.parent("div").addClass("checked");        
        
    }else{
        
        var input = $(".theme-settings").find("input[name="+name+"]");
        
        input.prop("disabled",false);            
        input.parent("div").removeClass("disabled").parent(".check").removeClass("disabled");        
        
        if(value === 1){
            input.prop("checked",true);
            input.parent("div").addClass("checked");
        }
        if(value === 0){
            input.prop("checked",false);            
            input.parent("div").removeClass("checked");            
        }
        if(value === -1){
            input.prop("checked",false);            
            input.parent("div").removeClass("checked");
            input.prop("disabled",true);            
            input.parent("div").addClass("disabled").parent(".check").addClass("disabled");
        }        
                
    }
}