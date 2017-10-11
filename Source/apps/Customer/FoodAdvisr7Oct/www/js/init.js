var resultDiv;
var myProfile={};
var activeProfile=-1;
var titlesStore;
var currentProductStore;
var sliders={};
var catText=[];
var connection='';
var tempCodes='';
var tempCodeMap='';

var currentQuicr='';

var loadedRules='';

var rulesByCode={};

var selectedDB='brandbank';
var dbatabaseText='';
var errorCount=0;


//var root="https://code.clevertech.tv/cxx/workspace/quicr/content/";


window.onerror = function(message, url, lineNumber) {
   urlArray=url.split('/');
   p1=urlArray.pop();
   p2=urlArray.pop();
   smallFile=p2+'/'+p1;
   obb={};
   obb['message']=message;
   obb['url']=url;
   obb['lineno']=lineNumber;
   parms=lineNumber+'/'+smallFile+'/'+message;
   api.call('jserror',parms,function(data){});
   $('.error-list').append('<div><b>'+lineNumber+'</b> '+smallFile+'<br/>'+message+' '+'</div>');
   errorCount++;
   if(appSettings.showErrors){
      $('.error-count').text(errorCount).show().addClass('new');
      setTimeout(function(){$('.error-count').removeClass('new')},200);
    }

   return false;
};




function bindAll(){
		var body=$('body');
    var xstart=0;
    var divX=0;

    embedMenu.bind();






    document.body.onclick = function(e){
      console.log(e);
        //wsonsole.log(e.srcElement.className);
        let nameStr=''+e.srcElement.className;
        if(nameStr.indexOf('cancel-clear')==-1){
            $('.scan-btn-startpos').removeClass('scan-btn-startpos');
            //$('.intro-button').fadeTo('fast',0,function(){
              //$(this).remove();
            //});
            $('.intro-button').addClass('hidden');
            //$('.targ-search-tools').addClass('hide');
        }
    }

    body.on('click','.act-test-load',function(){
      appdata.loadallergens();
      return false;
    });


    body.on('click','.act-intol-tab',function(){
      $('.act-intol-tab').removeClass('selected');
      $(this).addClass('selected');
      let mode=$(this).attr('href').substr(1);
      $('.intol-frame').addClass('hide').removeClass('search-target');
      $('.intolerances-'+mode).removeClass('hide').addClass('search-target');
      if(mode=='likes'){
          appdata.likeCount();
      }else{
        appdata.intoleranceCount();
      }
      appdata.setUnderline();
      return false;

    });


body.on('keyup','#intol-search',function(e){
  var keycode = event.keyCode || event.which;
  const t=$(this);
  if(t.val()!=''){
    $('.intol-search-clear').removeClass('hide');
  }else{
      $('.intol-search-clear').addClass('hide');
  }
  if(keycode==13){
        intolSearch(t.val().toLowerCase());
  }
  if(t.val()==''){
      intolSearchReset();
  }
});

body.on('click','.act-clear-search',function(){


  intolSearchReset();
  return false;
});


    body.on('focus','#intol-search',function(){
      $('.targ-search-tools').removeClass('hide');

    });

    body.on('blur','#intol-search',function(){
      $('.targ-search-tools').removeClass('hide');

    });

    body.on('click','.act-intol-search-reset',function(){
      intolSearchReset();
      return false;
    });


    function intolSearchReset(){
      $('#intol-search').val('');
      $('.intol-search-clear').addClass('hide');

      $('.search-target').find('h5').each(function(){
        const t=$(this);
        const p=t.parent();
        t.removeClass('expanded');
        p.removeClass('hide');
        p.find('li').removeClass('hide');
        p.find('li').removeClass('found');
        p.find('ul').addClass('hide');
      });
    }




    body.on('click','.act-intol-search',function(){
      const search=$('#intol-search').val().toLowerCase();
      intolSearchReset();
      intolSearch(search);
      return false;
    });


    function intolSearch(search){
      //$('.search-target').find('h5').removeClass('expanded');
      $('.search-target').find('li').each(function(){
        let t=$(this);
        t.addClass('search-me-count');
        if(t.text().toLowerCase().search(search)>-1){
          t.removeClass('hide');
          t.addClass('found');
        }else{
          t.addClass('hide');
          t.removeClass('found');
        }
      });

      $('.search-target').find('h5').each(function(){
        const tt=$(this);
        const pp=tt.parent();
        let actives=pp.find('.found');
        if(actives.length>0){
          tt.addClass('expanded');
          pp.find('ul').removeClass('hide');
        }else{
          pp.addClass('hide');

        }
      });

    }

    function intolSearchOld(search){
      $('.intol-frame').find('h5').removeClass('expanded');
      $('.intol-frame').find('li').each(function(){
        let t=$(this);
        t.addClass('search-me-count');
        if(t.text().toLowerCase().search(search)>-1){
          t.removeClass('hide');
          t.addClass('found');
        }else{
          t.addClass('hide');
          t.removeClass('found');
        }
      });
      $('.intol-frame').find('h5').each(function(){
        let nn=$(this).next();
        let ff=nn.find('.found');
        if(ff.length>0){
          nn.prev().removeClass('hide');
          nn.prev().trigger('click');
        }else{
          nn.prev().addClass('hide');
        }
        return false;
      });

    }






    body.on('click','.act-smenu-click',function(){
      barcode.get($(this).data('bcode'));
      return false;
    });

    body.on('click','.act-prof-subsub',function(){
      const t=$(this);
      t.toggleClass('open');
      t.next().toggle('fast');
    });


    body.on('click','.act-accept-terms',function(){
      $('.signup-terms').fadeTo('slow',0,function(){
        terms.trigger();
        $(this).remove();
      });
      store.put('user.terms','accepted');

      return false;
    });



    body.on('click','.comm-toggle',function(){
      console.log('comm-toggle');
      setTimeout(function(){
        console.log('test1');
        appdata.updateComms();
      },100);
      return false;
    });

    body.on('click','.act-set-handle',function(){
      msg.show('checking name',3000);
      var name=$('#food-handle').val();
      api.call('savehandle',name,function(data){
        var ret=data.split('|');
        if(ret[0]=='OK'){
            msg.show('Username set to '+ret[1],3000);
            store.put('user.handle',ret[1]);
            $('.share-main').removeClass('hide');
            $('.share-gethandle').addClass('hide');
            $('.share-handle-label').text(store.get('user.handle'));
        }else{
          msg.show('Sorry the username '+ret[1]+' Exists. Please choose another one',4000);
        }

      });
      return false;
    });

    body.on('click','.act-share-handle-change',function(){
      $('#food-handle').val(store.get('user.handle'));
      $('.share-gethandle').removeClass('hide');
      $('.share-main').addClass('hide');
      return false;
    });



    body.on('click','.act-save-share',function(){
      t=$(this);
      t.text('Sending ...');
      var postData={};
      postData['comments']=$('#share-comment').val();
      postData['groups']=$('#share-groups').text();
      postData['barcode']=t.data('barcode');
      postData['handle']=store.get('user.handle');
      msg.show('Saving your share',3000);
      api.call('share','',function(data){
        $('.act-save-share').text('Share');
        msg.show('Thank You!, Your share has been submitted for validation',3000);

      },postData);
      $('.detailpop').hide();
      return false;
    });

    body.on('click','.mode-switch-close',function(){
      $('.mode-switch-wrap').removeClass('mode-switch-wrap-full');
      return false;
    });

    body.on('click','.act-scan-results',function(){
      embedMenu.startCompare(false);
      page.route('scanner');
      barcode.rerun();
      return false;
    });

    body.on('click','.act-set-mode',function(){
      var t=$(this);
      var mode=t.data('mode');
      appStatus.setWorkingMode(mode,t);
      $('.overlay').trigger('click');
      return false;
    });

    body.on('click','.act-switch-modes',function(){
      if(appSettings.showEating){
        var ww=$('.mode-switch-wrap');
        ww.addClass('mode-switch-wrap-full');
        overlay.show('mode-switch-close');
    }
      return false;
    });

    body.on('click','.sq-img',function(){
        $(this).toggleClass('bigg');
          return false;
    });


    body.on('click','.act-intro-show',function(){
      $('.swiper-container').show('fast');
      $('.accountpop').css({left:'-400px'});
      overlay.hide();
      store.put('intro:hide','');
      t=$('.swiper-container');
      t.removeClass('hide').fadeTo('fast',1,function(){
        mySwiper = new Swiper ('.swiper-container', {
          direction: 'horizontal',
          loop: false,
        })
        mySwiper.slideTo(0, 200);

      });





      return false;
    });

    body.on('click','.act-intro-noshow',function(){
      $('.scan-btn').addClass('scan-btn-startpos');
      store.put('intro:hide','hide');
      t=$('.swiper-container');
      t.fadeTo('fast',0,function(){
        $(this).hide();
      });

      return false;
    });


    body.on('click','.act-intro-skip,.act-intro-go',function(){
      $('.scan-btn').addClass('scan-btn-startpos');
      t=$('.swiper-container');
      t.fadeTo('fast',0,function(){
        $(this).hide();
      });
      return false;
    });

    body.on('click','.act-intro-restart',function(){
      mySwiper.slideTo(0, 200);
      return false;
    });


    body.on('click','.act-com-share',function(){
      if(appdata.communities==''){
        msg.show('Please select some communites to share with',3000);
        page.route('communities');
      }else{
        var bcode=$(this).parent().data('barcode');
        var altcode=$(this).parent().data('altcode');
        console.log('*** *** ***');
        console.log(barcodeStore[bcode]);
        share.show(bcode,altcode);
      }
      return false;
    });

    body.on('touchend','.act-read-only',function(){
      var txt='A fixed profile can only be edited if you save under a new name. If you’d like to do this, click ‘Yes’ below and a new copy will be created in All Profiles. You can then edit that personalised version of the pre-built profile.';
      setTimeout(function(){popup.show(txt,'Yes|act-clone-prof,No|no')},0);
      return false;
    });

    body.on('click','.act-clone-prof',function(){
      msg.show('Copying profile',1000);
      appdata.makeCopy(appdata.state.profileNo);
      return false;
    });

    body.on('click','.act-profile-title',function(){

      page.route('profile');
      return false;
    });

    body.on('click','.act-profile-titlex',function(){
      var t=$(this);
      if(t.hasClass('ineditmode')){
        return;
      }
      t.addClass('ineditmode');

      var p=t.parent();

      var key=p.data('key');
      if(key<30){
        p.find('.toggle').hide('fast');
        title=t.text();
        ww=t.width()-20;
        t.html('<a href="#" class="minisave">Save</a><input type="text" data-key="'+key+'" class="act-name-change" value="'+title+'" style="width:'+ww+'px"/>');
        $('.act-name-change').focus();
      }else{
        msg.show('You cannot edit a fixed profile',2000);
      }
      return false;
    });

    body.on('blur','.act-name-change',function(){
      var t=$(this);
      var p=t.parent();
      var pp=p.parent();
      p.removeClass('ineditmode');
      pp.find('.toggle').show('fast');
      var title=t.val();
      if(title==''){
        title='...';
      }
      var key=t.data('key');
      t.remove();
      p.text(title);
      appdata.userProfiles[key]['name']=title;
      newslide.updateStore();
    });

    body.on('focus','#feedback-text',function(){
      $(this).css({height:'30vh'});
    });

    body.on('blur','#feedback-text',function(){
      $(this).css({height:'10vh'});
    });


    body.on('click','.act-logo-clicked',function(){
      t=$(this);
      var name=t.data('store');
      var txt='We expect to be able to include more grocery retailers soon.  Keep checking to find out which other supermarkets are joining the FoodAdvisr drive for transparency of food content for their customers.';
      if(name!=''){
          msg.show('You can now scan in '+name,3000);
      }else{
        msg.show(txt,3000);
      }


      return false;
    });

    body.on('click','.act-view-profile',function(){
      page.route('profile');
      return false;
    })

    body.on('click','.act-select-profile',function(){
      page.route('profile','list');
      return false;
    });

    body.on('click','.hidetoggle',function(){
      $(this).next().toggleClass('hide');
      return false;
    });

    body.on('focus','.act-login-field',function(){
      $('.login-logo').hide();

    });

    //body.on('blur','.act-login-field',function(){
  //    $('.login-logo').show();
    //});




    body.on('click','.act-toggle-barcodes',function(){
      $(this).next().toggle('fast');
      $('.main-menu-items').toggle('fast');
      return false;
    });


    body.on('click','.act-send-feedback',function(){
      $(this).text('Sending ...');
      var postData={};
      postData['feedback']=$('#feedback-text').val();
      postData['device']=appdata.device;
      postData['version']=appSettings.version;
      msg.show('Sending feedback',3000);
      //feedbackCache.add(postData);
      api.call('feedback','',function(data){feedBackReturn(data)},postData);
      //feedbackCache.showlist();
      return false;
    });

    function feedBackReturn(data){
      $('.act-send-feedback').text('Send Feedback');
      msg.show('Thank you for your feedback',2000);
      $('#feedback-text').val('');
      feedbackObj.show();
    }


    body.on('click','.menu-sub-item span',function(){
      $(this).next().next().toggle();
      return false;
    });


    body.on('click','.menu-title',function(){
      $(this).next().toggle();
      return false;
    });

    body.on('click','.act-set-offline',function(){
      msg.show('Setting off line mode',2000,false,true);
      connect.forceOffline=true;
      return false;
    });


    body.on('click','.act-route-downloads',function(){
      page.route('settings',$(this),'route');
      return false;
    });

    body.on('click','.act-click-hide',function(){
      $(this).fadeTo('fast',0,function(){
        $(this).hide();
      });
      return false;
    });

    body.on('click','.act-hide-welcome',function(){
      $('.signup-welcome').remove();
      appdata.showTerms('#signup-terms');
      appsignup.put('welcome');
      return false;
    });

    body.on('click','.act-next-terms',function(){
      $('.signup-terms').remove();
      appsignup.put('terms');
      appdata.showAllergens('#signup-allergens');
      return false;
    });

    body.on('click','.act-next-allergens',function(){
      appsignup.put('completed');
      $('.signup-panel').remove();
      page.route('profile');
      data.check();
      return false;
    });




    body.on('click','.act-re-quicr',function(){
      barcode.rerun();
      return false;
    });

    body.on('click','.act-view-preview',function(){
      t=$(this);
      bcode=t.data('barcode');
      proddata.currentItem=barcodeStore[bcode];
      overlay.show();
      $('.preview').show().html(engine.plotPreview(barcodeStore[bcode]));;
      return false;
    });

    body.on('click','.act-log-off',function(){
      api.jwt='';
      store.put('userdata.jwt',api.jwt);
      store.put('userdata.logintime',Date.now()-100000);
      location.reload();
      return false;
    });

    body.on('click','.act-del-profile',function(){
      store.clear();
      location.reload();
      return false;
    });

    var toggleStart=0;

    body.on('touchstart','.toggle-handlex',function(event){
      t=$(this);
      x=event.originalEvent.changedTouches[0]['clientX'];
      toggleStart=x;

    });


    body.on('touchend','.toggle-handlex',function(event){
      t=$(this);
      x=event.originalEvent.changedTouches[0]['clientX'];
      if(x>toggleStart){
        t.parent().addClass('active');
      }else{
        t.parent().removeClass('active');
      }
      embedMenu.checkToggle(t.parent());
      shortcuts.update();
      toggleSomething(t);


    });






    body.on('touchstart','.scanned',function(event){
      t=$(this);
      t.removeClass('transition1');
      ww=t.width();
      t.width(ww);
      p=$(this).offset();
      divX=p.left;
      e=event.originalEvent.changedTouches[0];
      xstart=event.originalEvent.changedTouches[0]['clientX'];

    });

    body.on('touchmove','.scanned',function(event){
      t=$(this);
      x=event.originalEvent.changedTouches[0]['clientX'];
      move=x-xstart;
      if(move<30){
        return;
      }
      $('.targ-title').text(move);
      if(move>130){
        t.addClass('faded');
      }else{
        t.removeClass('faded');
      }
      xx=divX+move;
      t.css({marginLeft:move});
    });

    body.on('touchend','.scanned',function(event){
        t=$(this);
        x=event.originalEvent.changedTouches[0]['clientX'];
        move=x-xstart;
        if(move>130){
          t.hide('fast',function(){
            bcode=t.data('barcode');
            delete barcodeStore[bcode];
            $(this).remove();
          })
        }
        t.addClass('transition1');
        t.css({marginLeft:0});
    });




    body.on('click','.act-change-password',function(){
      $('.loginpanel').addClass('hide');
      $('.form-change').removeClass('hide');
      $('.accountpop').css({left:'-60%'});
      overlay.hide();
      page.route('userreg');
      return false;
    });

    body.on('click','.accountpop',function(){
      $('.accountpop').css({left:'-60%'});
      overlay.hide();
      return false;
    });

    body.on('click','.error-list',function(){
      $(this).hide();
      return false;
    });

    body.on('click','.error-count',function(){
      $('.error-list').show();
      return false;
    });

    body.on('click','.detailpop a',function(){
      $('.accountpop').css({left:'-60%'});
      overlay.hide();
    });

    body.on('click','.act-close-account',function(){
        $('.accountpop').css({left:'-60%'});
    });

    body.on('click','.act-my-account',function(){
      overlay.show('act-close-account');
      $('.account-handle').text(store.get('user.handle'));
      $('.accountpop').css({left:0});
      //$('.account-email').text(userdata.email);
      return false;
    });

    body.on('click','.message',function(){
      $(this).hide();
      return false;
    });

    body.on('click','.act-view-detail',function(){
      t=$(this);
      bcode=t.data('barcode');
      console.log(barcodeStore[bcode]);
      engine.process(barcodeStore[bcode]);
      return false;
    });

    body.on('click','.act-view-detail-full',function(){
      t=$(this);
      bcode=t.data('barcode');
      barcode.getFromDBDetail(bcode);
      return false;
    });






    body.on('click','.act-hide-parent',function(){
      p=$(this).parent();
      p.fadeTo('fast',0,function(){
        $(this).hide();
      })
      return false;
    });

    body.on('click','.act-del-prof',function(){
      p=$(this).parent().parent();
      key=p.data('key');
      appdata.userProfiles[key]['name']='#DELETED#';
      newslide.updateStore();

      p.remove();
      activeProfile=appdata.findFirstActive();
      appdata.state.profileNo=activeProfile;
      //redrawShorts(activeProfile);
      //shortcuts.update()
      return false;
    });

    body.on('click','.act-profile-edit-start',function(){
      //page.route('profile','list');
      $('.subby-all').trigger('click');
      t=$(this);
      if(t.text()=='-'){
        t.text('Cancel edit').addClass('smaller');
        addEditButtons();
      }else{
          t.text('-').removeClass('smaller');
          removeEditButtons();
      }
      return false;
    });
//fire42 start
    function eaterySearch(val)
    {
      api.getEateries(function(data){
        var divItem = document.getElementById('loadeateries');
        var op ='';
        if(data.result.length > 0)
        {
          for(idx in data.result){
            if(data.result[idx].IsAssociated == 1)
            {
             op += '<div class="act-eatery">' +
                      '<input type=hidden id="eateryId" value="' + data.result[idx].id + '" />' + 
                      '<div class="eatery-columns">' +
                        '<div class="act-eatery-logo" ><img class="act-eatery-logopath" src="' + appSettings.mediaPath + data.result[idx].LogoPath + '"></img></div>' +
                        '<div class="act-eatery-name">'+ data.result[idx].BusinessName + '<br/>'+ '<div class="act-action-div"><div class="act-eatery-distance">'+ data.result[idx].distance+'m'+ '</div>'+'<div class="act-eatery-image"> <img class="act-eatery-image" src="img/foodadvisr-green.png"/></div></div>' +'</div>' +
                      '</div>' +
                      '<div class="eatery-clear"></div>' +
                    '</div>';
            }
            else
            {
              op += '<div class="in-act-eatery">' +
                      '<input type=hidden id="eateryId" value="' + data.result[idx].id + '" />' + 
                      '<input type=hidden id="eateryName" value="' + data.result[idx].BusinessName + '" />' + 
                      '<div class="eatery-columns">' +
                        '<div class="in-act-eatery-logo" ><img class="act-eatery-logopath" src="' + appSettings.mediaPath + data.result[idx].LogoPath + '"></img></div>' + //<img src="' + appSettings.mediaPath + data.result[idx].LogoPath + '" width=75 height=75 alt=""></img>
                        '<div class="in-act-eatery-name">'+ data.result[idx].BusinessName + '<br/>' +/* (data.result[idx].Address==null?'':data.result[idx].Address)+*/'<div class="act-action-div"><div class="act-eatery-distance">'+ data.result[idx].distance+'m'+ '</div>'+'<div class="act-eatery-image"> <img class="act-eatery-image" src="img/foodadvisr-green.png"/></div></div>' +'</div>' + '</div>' +
                      '</div>' +
                      '<div class="eatery-clear"></div>' +
                    '</div>';
            }
              //op += '<div class="in-act-eatery"><input type=hidden id="eateryId" value="' + data.result[idx].id + '" /><div id="eateryName">'+ data.result[idx].BusinessName +'</div></div>';
          }
        }
        divItem.innerHTML =op;
      })
    }
    
    function eaterySearchReset()
    {
        //$('#loadeateries').innerHTML = '';
        var divItem = document.getElementById('loadeateries');
        divItem.innerHTML ='';
    }

    body.on('click','.act-eatery',function(){
      var eateryId=$(this).find('#eateryId').val();
      api.getAddClickAfterAssociated(eateryId,function(data){
      });

    });
    
    body.on('click','.in-act-eatery',function(){
      var eateryId=$(this).find('#eateryId').val();
      var eateryName=$(this).find('#eateryName').val();
      var message = eateryName + " is not yet a member of the " + appSettings.orgName + " community. <br> When you tap 'invite' we will send a message to this business inviting them to join the " + appSettings.orgName + " community so that you can see their full and updated menus.";
      popup.show(message,'Invite|in-act-eatery-invite,Cancel');
      api.getAddClickBeforeAssociated(eateryId,function(data){
      });
    });

    body.on('click','.in-act-eatery-invite',function(){
      
    });

    body.on('keyup','#eatery-search',function(e){
      var keycode = event.keyCode || event.which;
      const t=$(this);
      if(t.val()!=''){
        $('.eatery-search-clear').removeClass('hide');
      }else{
          $('.eatery-search-clear').addClass('hide');
      }
      if(keycode==13){
          eaterySearch(t.val().toLowerCase());
      }
      if(t.val()==''){
        $('.eatery-search-clear').addClass('hide');
        eaterySearchReset();
      }
    });

    body.on('click','.eatery-search-clear',function(){
      $('#eatery-search').val('');
      $('.eatery-search-clear').addClass('hide');
      eaterySearchReset();
      return false;
    });

//fire42 end


    function addEditButtons(){
      $('.act-download-menu').each(function(){
        t=$(this);
        t.find('.targ-add-del').each(function(){
          tt=$(this);
          tt.html('<span class="icon icon-close act-del-prof"></span>');
        });
      });

    }

    function removeEditButtons(){
      $('.act-download-menu').each(function(){
        t=$(this);
        t.find('.targ-add-del').each(function(){
          tt=$(this);
          tt.html('');
        });
      });

    }


    body.on('click','.targ-fave-select',function(event){
      if($(this).hasClass('faded')){
        msg.show('Locked');
        event.stopImmediatePropagation();
      }else{
        console.log(appdata.userProfiles);
        profileTools.clear();
        profileTools.select($(this));
        setTimeout(function(){shortcuts.update()},50);
      }


    });


    body.on('click','.act-create-new',function(){
      page.route('profile','list');
      var newProfile={name:'New profile',calories:0,totalfat:0,saturatedfat:0,salt:0,sugar:0,protein:0,carbs:0,fibre:0,cholesterol:0,ingredients:''};
      appdata.insert(newProfile);
      appdata.state.profileName=newProfile.title;
      appdata.state.profileNo=1;
      newslide.updateStore();
      //$('.media').css({outline:'1px dotted red'});
      $('#targ-profiles-list').html(appdata.showProfiles());
      first=$('.act-download-menu:first');
      first.find('.toggle').trigger('click');
      first.find('.act-profile-title').trigger('click');

      return false;
    });

    body.on('click','.act-download-selected',function(){
      t=$(this);
      ref=t.data('ref');
      data=appdata.profiles[ref];
      title=t.text();
      description=t.data('desc');
      if(data.ingredients!=undefined){
        ingredients=data.ingredients.replaceAll(',',', ');
      }else{
          ingredients='';
      }
      html='<p>'+description+'</p>';
      html+='<div class="popmenu-more">';
        html+='<h6>Intolernaces</h6>';
        html+='<p>'+ingredients+'</p>';
        html+='<h6>Sliders</h6>';
        html+=appdata.showProfileSlides(data);
        html+='<div class="filler"></div>';
      html+='</div>';
      //html+='<a href="" class="btn-add-preset act-profile-more">More...</a>';
      html+='<a href="" class="btn-add-preset act-profile-add" data-ref="'+ref+'">Add Profile</a>';
      popmenu.show(title,html);
      return false;
    });

    body.on('click','.act-profile-more',function(){
      $('.popmenu-more').toggle('fast');
      return false;
    });

    body.on('click','.act-profile-add',function(){
      t=$(this);
      ref=t.data('ref');
      free=appdata.findFreeProfile();
      appdata.copyProfile(ref,free);
      newslide.saveAll();
      $('#targ-profiles-list').html(appdata.showProfiles());
      msg.show('Profile added',2000);
      overlay.hide();
      page.route('profile');
      return false;
    });


    body.on('click','.targ-intol-item',function(){
      //alert(1);
      console.log('Intol toggle');
      setTimeout(function(){appdata.saveIntolerancePrefs()},80);
      return false;
    });

    body.on('click','.targ-like-item',function(){
      setTimeout(function(){appdata.saveLikePrefs()},80);
      return false;
    });

    body.on('click','.act-barcode-list',function(){
      bcode=$(this).data('barcode');
      barcode.get(bcode);
      return false;
    });

    body.on('click','.act-barcode-test',function(){
      barcode.get('5410438036482');
      return false;
    });

    body.on('click','.act-barcode-random',function(){
      barcode.get('random');
      return false;
    });


    body.on('click','.act-barcode-test1',function(){
      barcode.get('5060026890042');
      return false;
    });


    body.on('blur','.act-comp-edit-profile',function(){
      t=$(this);
      title=t.val();
      p=t.parent();
      p.html('<h3>'+title+'</h3>');
      p.addClass('targ-profile-title');
      appdata.userProfiles[appdata.state.profileNo]['name']=title;
      newslide.updateStore();
      appdata.showShortcuts();
      //appdata.showProfiles();
      $('#targ-profiles-list').html(appdata.showProfiles());
    });

    body.on('click','.targ-profile-title',function(){
      t=$(this);
      t.removeClass('targ-profile-title');
      title=t.text();
      t.html('<input type="text" value="'+title+'" id="profile-title-editor" class="act-comp-edit-profile"/>');
      $('#profile-title-editor').focus();
    });

    body.on('click','.act-profile-tools a',function(){
      t=$(this);
      p=t.parent();
      key=p.data('key');
      mode=t.attr('href');
      switch(mode){
        case '#fave':
            ref=t.data('ref');
            title=p.data('title');
            $('.fave-item-'+ref).text(title).attr('href','#'+key);
            appdata.shortcuts[ref]['name']=title;
            appdata.shortcuts[ref]['ref']=key;
            appdata.saveShortcuts();
            newslide.updateStore();
            $('#targ-profiles-list').html(appdata.showProfiles());
        break;
        case '#delete':
          appdata.userProfiles[key]['name']='#DELETED#';
          newslide.updateStore();
          $('#targ-profiles-list').html(appdata.showProfiles());
        break;
      }
      popmenu.hide();
      return false;
    });

    body.on('click','.act-debug-cleardb',function(){
      store.clear();
      msg.show('Database cleared please restart the app',3000);
      return false;
    });

    body.on('click','.act-profile-selectx',function(){
      profileTools.select($(this));
      return false;
    });


    body.on('touchstart mousedowns','.newslider',function(){
      p=$(this).parent();
      p.find('small').addClass('bigger');
    });

    body.on('input','.newslider',function(){
      t=$(this);
      newslide.update(t);
    });

    body.on('touchend mouseup','.newslider',function(){
        //$('.recalc-ball').removeClass('recalc-ball-fade');
        newslide.saveAll();
        var t=$(this);
        var name=t.data('name');
        var data=newslide.getSlideData(name);
        var pip=data.pip;
        var rawVal=t.val();
        var slideVal=rawVal/pip;
        if(slideVal>data.above){
          if(data.label=='fibre' || data.label=='protein'){
              msg.show('Warning below recommended amount',2000);
          }else{
              msg.show('Warning above recommended amount',2000);
          }

        }
        p=$(this).parent();
        p.find('small').removeClass('bigger');
    });

    body.on('click','.act-select-all',function(){
      toggleSomething($(this));
    });

    function toggleSomething(t){
      p=t.parent().parent();
      if(t.hasClass('active')){
          p.find('.toggle').removeClass('active');
          t.addClass('active');
      }else{
        p.find('.toggle').addClass('active');
        t.removeClass('active');
      }
    }


    body.on('click','.act-profile-short a',function(){
      t=$(this);
      if(t.attr('href')=='#-1'){
        return;
      }
      var forceRoute=false;
      if(t.hasClass('selected')){
        forceRoute=true;
      }
      $('.act-profile-short a.selected').removeClass('selected');
      t.addClass('selected');
      profileId=t.attr('href').substr(1);
      redrawShorts(profileId);
      if(forceRoute){
        page.route('profile');
      }
      return false;
    });

    function redrawShorts(profileId){
      appdata.state.profileNo=profileId;
      if(appdata.userProfiles[profileId]!=undefined){
        name=appdata.userProfiles[profileId]['name'];
        $('.targ-profile-title').html('<h3>'+name+'</h3>');
        newslide.drawSliders();
        appdata.showIntolerances();
        barcode.rerun(false);
      }

    }

      body.on('click','.act-settings-tabs a',function(){
        $('.act-settings-tabs a').removeClass('selected');
        t=$(this);
        mode=t.attr('href');
        t.addClass('selected');
        $('.settings .subtab').addClass('hide');
        $(mode).removeClass('hide');
        switch(mode){
          case('#subtab-allergens'):
            appdata.showAllergens();
            break;
            case('#subtab-intolerances'):
              appdata.showIntolerances();
              break;

          case('#subtab-terms'):
            appdata.showTerms('.terms-innerx');
          break;
          case('#subtab-downloads'):
          $('#targ-download-list').html(appdata.showDownloads());
          break;
        }
        return false;
      });

      body.on('click','.act-profile-tabs a',function(){
        $('.act-profile-tabs a.selected').removeClass('selected');
        t=$(this);
        mode=t.attr('href');
        t.addClass('selected');
        $('.profiletabs .subtab').addClass('hide');
        $(mode).removeClass('hide');
        switch(mode){
          case('#subtab-sliders'):
            newslide.drawSliders();
          break;
          case('#subtab-profiles'):
            $('#targ-profiles-list').html(appdata.showProfiles());
          break;

        }
        return false;
      });





        body.on('click','.act-login-reset',function(){
          var t=$(this);
          t.text('Sending Reset...');
          wrap=$('.form-reset');
          email=wrap.find('#email').val();
          api.call('reset',email,function(data){
            $('.act-login-reset').text('Reset Password');
            msg.show('A password reset email has been sent to your email address.',4000);
          });
          return false;
        });

        body.on('click','.act-login-change',function(){
          wrap=$('.form-change');
          oldpass=wrap.find('#oldpassword').val();
          newpass=wrap.find('#newpassword').val();
          parms=oldpass+'/'+newpass;
          api.call('change',parms,function(data){
            $('.targ-pass-change').html(data);
          });
          return false;
        });


        var signupParms='';


        body.on('click','.act-login-signup',function(){
          wrap=$('.form-signup');
          email=wrap.find('#email').val();
          userdata.email=email;
          pass=wrap.find('#password').val();
          checkpass=wrap.find('#passwordconf').val();
          signupParms=email+'/'+pass;
          if(checkPass(pass,checkpass)){
            api.call('signup',signupParms,function(data){
              p=data.split(':');
              cmd=p[0];
              jwt=p[1];
              if(cmd=='2created'){
                msg.show('User created - logging on',2000);
                store.clear();
                api.call('login',signupParms,function(data){
                    signupResponse(data);
                });
              }else{
                msg.show('Sorry this user exists',4000,false,true);
              }
            });
          }
          return false;
        });

        function checkPass(pass,passcheck){
          if(pass!=passcheck){
            msg.show('Passwords do not match',3000,false,true);
            return false;
          }else{
            if(pass.length<8){
              msg.show('Please enter a password of at least 8 chrs',3000,false,true);
              return false;
            }

          }
          return true;
        }

        body.on('click','.act-login-login',function(){
          $(this).text('Logging in..');
          wrap=$('.form-login');
          email=wrap.find('#email').val();
          userdata.email=email;
          store.put('userdata.email',email);
          $('.account-email').text(email);
          pass=wrap.find('#password').val();
          var parms=email+'/'+pass+'/'+appSettings.version+'/'+appdata.device;
          api.call('login',parms,function(data){
              loginResponse(data);
          });
          return false;
        });

        function signupResponse(data){
          var dd=data.split('|');
          var cmd=dd[0];
          var jwt=dd[1];
          if(cmd=='loggedin'){
            api.jwt=jwt;
            store.put('userdata.jwt',api.jwt);
            msg.show('Logged in ',2000);
            $('.loggedin').removeClass('loggedin');
            processLogIn();
          }else{
            msg.show('Invalid email or password please retry',2000);
          }

        }



        function loginResponse(data){
          var dd=data.split('|');
          var cmd=dd[0];
          var jwt=dd[1];
          if(cmd=='loggedin'){
            api.jwt=jwt;
            store.put('userdata.jwt',api.jwt);
            store.put('userdata.logintime',Date.now());
            msg.show('Logged in ',2000);
            $('.loggedin').removeClass('loggedin');
            processLogIn();
            skinObj.loadSkin();
          }else{
            $('.act-login-login').text('Login');
            msg.show('Invalid email or password please retry',2000);
          }

        }




        body.on('click','.act-login-item',function(){
          $('.loginpanel').addClass('hide');
          t=$(this);
          cmd=t.attr('href');
          switch(cmd){
            case '#signup':
              $('.form-signup').removeClass('hide');
              break;
              case '#reset':
                $('.form-reset').removeClass('hide');
                break;
              case '#signin':
                $('.form-login').removeClass('hide');
                break;
              case '#change':
                  $('.form-change').removeClass('hide');
                  break;


          }

          return false;
        });



	body.on('click','.act-load-allergens',function(){
		$('#test4').html('Loading...');
		api.call('allergens','',function(data){
			allergenData=safeJson.parse(data);
			op='';
    op+=picker.start('Allergies');
    op+=picker.startList();
			for(idx in allergenData.A){
				op+=picker.add(allergenData.A[idx]['title'],true);
			}


			op+=picker.stopList();
			op+=picker.close();



			$('#test4').html(op);
		});
		return false;
	});



    body.on('click','.help-overlay,.help-overlay-small',function(){
    	$(this).fadeTo('fast',0,function(){
    		$(this).css({top:'10000px'});
    	});
    	return false;
    });

    body.on('click','.act-help',function(){
      $('.accountpop').css({left:'-60%'});
      overlay.hide();
      $('.help-text:visible').each(function(){
        ttt=$(this);
        helpref=ttt.data('href');
      });
    	ho=$('.help-overlay');
      hi=$('.help-inner');
    	ho.css({top:'0px'});
      if(helpref!=undefined){
          console.log('Help Ref '+helpref );
          hi.load('help-'+helpref+'.html');
      }else {
        msg.show(helpref+='+' ,2000);
          hi.html(helpText);
      }

    	ho.fadeTo('fast',1);
    	return false;
    });


    body.on('click','.act-login-change',function(){
      wrap=$('.form-change');
      oldpass=wrap.find('#oldpassword').val();
      newpass=wrap.find('#newpassword').val();
      newpasscheck=wrap.find('#newpasswordconf').val();
      parms=oldpass+'/'+newpass;
      if(checkPass(newpass,newpasscheck)){
        api.call('change',parms,function(data){
          msg.show('Password changed',3000);
        });
      }
      return false;
    });






    body.on('click','.act-login-item',function(){
      $('.loginpanel').addClass('hide');
      t=$(this);
      cmd=t.attr('href');
      switch(cmd){
        case '#signup':
          $('.form-signup').removeClass('hide');
          break;
          case '#reset':
            $('.form-reset').removeClass('hide');
            break;
          case '#signin':
            $('.form-login').removeClass('hide');
            break;
          case '#change':
              $('.form-change').removeClass('hide');
              break;


      }

      return false;
    });



    body.on('click','.act-start-facebook',function(){
      startFacebook();
      return false;
    });



    body.on('click','.act-test1',function(){
      popup.show('This is some test text for the popup.','Yes please,Nope');
      return false;
    });

    body.on('click','.overlay',function(){
      $(this).hide();
      $('.overlay-hide').hide();
      $('.overlay-remove').remove();
      $('.overlay-add-hide').addClass('hide');
      return false;
    });

    body.on('click','.act-login-settings',function(){
      pass=$(this).prev().val();
      if(pass=='quicr'){
        $('.settings').removeClass('hide');
        $('.set-pass-input').hide();

      }else{
        popup.show('Incorrect password please try again','Ok');
      }
      return false;
    });

		body.on('click','.act-reload-products',function(){

			cache.loadProducts();
			return false;
		});

		body.on('click','.act-info-pop',function(){
			$('.bpop').remove();
			if($(this).hasClass('counter-1')){
				return false;
			}
			p=$(this).parent();
			$('body').prepend('<div class="bpop">pop</div>');
			alerts.replaceAll('allergens','<i>Allergensxx</i>');
			title=p.find('.prod-title').text();
			levels=p.find('.prod-levels').html();
			op='<h4>'+title+'</h4>';
			op+=alerts;
      op+='<br/>';
			op+=levels;
			$('.bpop').html(op);
			$('body').on('click',function(){
				$('.bpop').remove();
			});
			return false;
		});

		body.on('click','.bpop',function(){
				$('.bpop').remove();
				return false;
		});



		body.on('click','.act-reload-all',function(){
			init();
			return false;
		});

		body.on('click','.act-settings-section',function(){
			t=$(this);
			if(t.hasClass('expanded')){
				t.removeClass('expanded');
        t.next().addClass('hide');
			}else{
				t.addClass('expanded');
        t.next().removeClass('hide');
			}
		});


		$('body').on('click','.toggle',function(){
      t=$(this);
      console.log('toggled');
      //$('.recalc-ball').removeClass('recalc-ball-fade');
			t.toggleClass('active');
      //if(!t.hasClass('targ-intol-item' && !t.hasClass('.act-fave-select'))){
      if(t.hasClass('allergen-toggle')){
          allergens.save(t);
      }
		});


  body.on('click','.act-menu',function(){
    var t=$(this);
    var extra=t.data('extra');
    if(extra==''){
      extra='rescan';
    }
		pageName=t.attr('href');
		pageName=pageName.substr(1);
		page.route(pageName,$(this),extra);
		return false;//
	});



	body.on('click','.act-sort',function(){
		tinysort('.scanned','b.counter');
		return false;
	});




	body.on('click','.act-comp',function(){

		$('.comp-item').each(function(){
			t=$(this);
			level=parseFloat(t.text());
			item=t.data('ref');
			slider=$('.ss-'+item.replace(':','_'));
			sliderVal=slider.val();
			if(level>sliderVal){
				mode='red';
			}else{
				split=sliderVal/2;
				if(level>split){
					mode='amber';
				}else{
					mode='green';
				}
			}
			t.after(' <small>'+sliderVal+' '+slider.attr('max')+' '+mode+'</small>');
		});
		return false;
	});



  body.on('click','.act-new-scan',function(){
    page.route('scanner',$(this));
		barcode.startScan();
		return false;
	});

  body.on('click','.act-test2',function(){
    $('.scanned').remove();
    $('.start-hide').show();
      page.set('scanner');
      replayQuicr();
    return false;
  });


	body.on('click','.act-start-scan',function(){
    if(!terms.check('.act-start-scan')){
      return false;
    }
    $(this).removeClass('scan-btn-startpos');
    var t=$(this);
    if(t.data('extra')=='eating'){

      page.route('myxyz');

    }else{
      var listItems=$('.op1');
      if(listItems.length>9){
        msg.show('You have the maximum items in your list. Please remove one or click Nw Scan to start another comparison.',4000,true);
        return false;;
      }
      if(!$(this).hasClass('no-puff')){
          $(this).addClass('explode');
      }
  		barcode.startScan();


    }
		return false;
	});



	$('.act-example-prod').on('click',function(){
		grabProduct($(this).data('code'));
		return false;
	});

  body.on('click','.act-start-new',function(){
    popup.show('Do you wish to start a new Scan? This will clear your current list.','Yes|act-new-list,No');
  });

  body.on('click','.act-new-list',function(){
    currentQuicr='';
    $('.scanned').remove();
    $('.start-hide').show();
    barcode.clear();
    barcode.startScan();

    return false;
  });


  function rescans(){
    $('.scanned').remove();
    $('.start-hide').show();
      page.set('scanner');
      replayQuicr();

  }

	body.on('click','.act-quicr',function(){
		$('.start-hide').hide();
    rescans();
		cv=$('.counter.reveal');
		if(cv.length>0){
				quicr();
		}else{
      popup.show('Do you wish to start a new Quicr?. This will clear your current list?','Yes|act-new-list,No');
		}

		return false;
	});





	body.on('click','.act-submenu',function(){
		$('#list').html('');
		return false;
	});

	body.on('click','.act-scanperson',function(){
		personScan();
		return false;
	});


	body.on('click','.prof-range',function(){
		t=$(this);
		title=t.prev().text();
		sh=$('.slider-hint');
		ofs=t.offset();
		if(ofs.top<170){
			xx=ofs.top+40;
			sh.css({top:xx,opacity:1});
		}else{
			sh.css({top:0,opacity:1});
		}
		sh.find('h2').text(title);
		hint=t.data('hint');
		if(hint===undefined || hint==='undefined'){hint='';}
		sh.find('p').text(hint);
	});

	function slideDesc(value,max){
		thirds=max/3;
		if(value>=(thirds*2)){
				ret='Higher than average';
		}else{
			if(value>=thirds){
				ret='Average';
			}else{
					ret='Below average';
			}
		}
		return ret;
	}


  function startScanx(){
    barcode.startScan();
  }



  String.prototype.replaceAll = function(search, replacement) {
      var target = this;
      return target.replace(new RegExp(search, 'g'), replacement);
  };


}
