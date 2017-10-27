  var store=new Object();
  store.get=function(item){
      item=window.localStorage.getItem(item);
      if(item==undefined || item==null){
          return '';
      }else{
          return item;
      }
  }

  store.put=function(item,data){
      window.localStorage.setItem(item, data);
  }

  store.clear=function(){
    window.localStorage.clear();
  }

  var popmenu=new Object();

  popmenu.show=function(title,inHtml,t){
    if(t!=undefined){
      offs=t.offset();
      ttop=parseFloat(offs.top);
    }else{
      ttop=40;
    }
    sheight=parseFloat(screen.height);
    overlay.show();
    html='<h5>'+title+'</h5>';
    html+=inHtml;
    $('body').append('<div class="popmenu overlay-remove" data-title="'+title+'">'+html+'</div>');
    pm=$('.popmenu');
    pmHeight=parseFloat(pm.height());
    totalOffset=ttop+pm.height()+60;
    if(totalOffset>sheight){
      ttop=sheight-(pm.height()+90);
    }
    pm.css({top:ttop});
  }


  popmenu.showx=function(title,t){
  key=t.data('key');
    if(t!=undefined){
      offs=t.offset();
      ttop=parseFloat(offs.top);
      sheight=parseFloat(screen.height);
    }
    overlay.show();
    html='<h5>'+title+'</h5>';
    html+='<a href="#fave" data-ref="1">Set as shortcut 1</a>';
    html+='<a href="#fave" data-ref="2">Set as shortcut 2</a>';
    html+='<a href="#fave" data-ref="3">Set as shortcut 3</a>';
    html+='<div class="popmenu-divide"></div>';
    html+='<a href="#delete">Delete this Item<a/>';
    $('body').append('<div class="popmenu overlay-remove act-profile-tools" data-title="'+title+'" data-key="'+key+'">'+html+'</div>');
    pm=$('.popmenu');
    pmHeight=parseFloat(pm.height());
    totalOffset=ttop+pm.height()+60;
    if(totalOffset>sheight){
      ttop=sheight-(pm.height()+90);
    }
    pm.css({top:ttop});
  }

  popmenu.hide=function(){
    $('.popmenu').remove();
    overlay.hide();
  }

  var popupbook = new Object();

  popupbook.show=function(ptext,buttons){
      midpop=$('.midpopbook');
      midpop.css({top:-80});
      overlay.show();
      var op='<p>'+ptext+'</p>';
      if(buttons!='' && buttons!=undefined){
        op+='<div class="midpopbook-buttons">';
        var b=buttons.split(',');
        for(idx in b){
          vv=b[idx].split('|');
          ccl='';
          if(vv[1]!=undefined){
            ccl=vv[1];
          }
          op+='<a href="#index.html" class="popupbook-act-button '+ccl+'">'+vv[0]+'</a>';
        }
        op+='</div>';
      }
      midpop.html(op);
      midpop.show();
      bh=$('body').height();
      poph=midpop.height();
      ttop=(bh/2)-(poph/2);
      //midpop.css({top:ttop});
      midpop.css({top:'50px'});

      $('body').on('click','.popupbook-act-button',function(){
        overlay.hide();
        return false;
      });

  }



  popupbook.hide=function(){
    overlay.hide();
    $('.midpopbook').hide();


  }

  var popupcall = new Object();

  popupcall.show=function(ptext,buttons){
      midpop=$('.midpopbook');
      midpop.css({top:-80});
      overlay.show();
      var op='<p>'+ptext+'</p>';
      if(buttons!='' && buttons!=undefined){
          op+='<div class="midpopbook-buttons">';
          var b=buttons.split(',');
          for(idx in b){
              vv=b[idx].split('|');
              ccl='';
              if(vv[1]!=undefined){
                  ccl=vv[1];
              }
              op+='<a href="#index.html" class="popupcall-act-button '+ccl+'">'+vv[0]+'</a>';
          }
          op+='</div>';
      }
      midpop.html(op);
      midpop.show();
      bh=$('body').height();
      poph=midpop.height();
      ttop=(bh/2)-(poph/2);
      //midpop.css({top:ttop});
      midpop.css({top:'210px'});

      $('body').on('click','.popupcall-act-button',function(){
          overlay.hide();
          return false;
      });

  }



  popupcall.hide=function(){
      overlay.hide();
      $('.midpopcall').hide();


  }

var popupcuisine = new Object();

  popupcuisine.show=function(ptext,buttons){
      midpop=$('.midpopbook');
      midpop.css({top:-80});
      overlay.show();
      var op='<p>'+ptext+'</p>';
      if(buttons!='' && buttons!=undefined){
          op+='<div class="midpopbook-buttons">';
          var b=buttons.split(',');
          for(idx in b){
              vv=b[idx].split('|');
              ccl='';
              if(vv[1]!=undefined){
                  ccl=vv[1];
              }
              op+='<a href="#index.html" class="popupcuisine-act-button '+ccl+'">'+vv[0]+'</a>';
          }
          op+='</div>';
      }
      midpop.html(op);
      midpop.show();
      bh=$('body').height();
      poph=midpop.height();
      ttop=(bh/2)-(poph/2);
      //midpop.css({top:ttop});
      midpop.css({top:'50px'});

      $('body').on('click','.popupcuisine-act-button',function(){
          overlay.hide();
          return false;
      });

  }



  popupcuisine.hide=function(){
      overlay.hide();
      $('.midpopbook').hide();


  }





  var overlay = new Object();

    overlay.show=function(addClass){

      $('.overlay').addClass('on').show();
      if(addClass!=undefined){
        $('.overlay').addClass(addClass);
      }

    }

    overlay.hide=function(){
      $('.overlay').removeClass('on').hide();
      $('.overlay-hide').hide('fast');
      $('.overlay-remove').remove();

    }


  var settings=new Object();

  settings.load=function(){
		url=api.url('settings','');
		$.get(url,function(raw){
      try{
        //settings.data=JSON.parse(raw);
        settings.data=safeJson.parse(raw);
        settings.xdata=settings.data;

      }catch(err){
      }
		});
  }

  settings.list=function(valid,target){
    if(target==undefined){target='settings'}
    cprof=myProfile.person.settings;
      sets=settings.data;
      op='';
      types={};
      ordered={};
      for(idx in sets){
        thisCat=idx.substr(0,1);
        if(valid.search(thisCat)!=-1){
          active='notactive';
          if(cprof.search(sets[idx]['short_code'])!=-1){
            active='active';
          }
          ll=list.item(sets[idx]['name'],sets[idx]['short_code'],active);
          if(types[sets[idx]['type']]==undefined){
              types[sets[idx]['type']]='';
              ordered[thisCat]={};
              ordered[thisCat]['html']='';
          }
              ordered[thisCat]['html']+=ll;
              ordered[thisCat]['name']=sets[idx]['type'];


          //types[sets[idx]['type']]+=ll;

        }
      }
      vList=valid.split(',');
      for(xx in vList){
        obj=ordered[vList[xx]];
        pp='<h5 class="act-settings-section">'+obj['name']+' <span class="icon icon-down"></span></h5>';
        if(obj['html']!=undefined){
          pp+=list.start();
          pp+=obj['html'];
          pp+=list.stop();

        }
        op+=pp;

      }
      //orderMe={};
      //for(xx in types){
        //pp='<h5 class="act-settings-section">'+xx+' <span class="icon icon-down"></span></h5>';
        //pp+=list.start();
        //pp+=types[xx];
        //pp+=list.stop();
        //orderMe[xx]=pp;
        //op+=pp;
      //}




      $('#'+target).html(op);
      $('.alist').find('.toggle').parent().hide().removeClass('notactive');
      $('.alist').find('.toggle.active').parent().show();

  }

  settings.showAllergens=function(){
      cprof=myProfile.person.settings;
      sets=settings.xdata;
      var plist=cprof.split(',');
      op='';
      ac=0;
      for(idx in plist){
        if(plist[idx]!=undefined){
          if(plist[idx].slice(0,1)=='A'){
            rbc=rulesByCode[plist[idx]];
            if(rbc!=undefined){
              op+=rbc+',';
              ac++;
            }

          }
        }
      }
      if(ac>1){
          opp='<h6>Your Allergens</h6>';
      }else{
        opp='<h6>Your Allergen</h6>';
      }
      op = op.slice(0, -1);

      if(op!=''){
          $('.allergenarea').html(opp+op);
      }else{
        $('.allergenarea').html('');
      }

  }

  settings.getList=function(){
    valid='A,R,B';
    cprof=myProfile.person.settings;
      sets=settings.xdata;
      op='';
      types={};
      for(idx in settings.xdata){
        thisCat=idx.substr(0,1);
        if(valid.search(thisCat)!=-1){
            active='notactive';
            if(cprof.search(sets[idx]['short_code'])!=-1){
              active='active';
              ll=list.item(sets[idx]['name'],sets[idx]['short_code'],active);
              if(types[sets[idx]['type']]==undefined){
                  types[sets[idx]['type']]='';
              }
              types[sets[idx]['type']]+=ll;

            }

        }
      }
      for(xx in types){
        op+='<h5 class="act-settings-section">'+xx+' <span class="icon icon-down"></span></h5>';
        op+=list.start();
        op+=types[xx];
        op+=list.stop();
      }
      return op;
  }

  var safeJson = new Object();

  safeJson.parse=function(inRaw){
    try{
      ret=JSON.parse(inRaw);
    }catch(err){
      ret={};
    }
    return ret;
  }


  settings.setActive=function(){

    cc='';
    $('.toggle.active').each(function(){
      cc+=$(this).attr('id')+',';
    });
    settings.current=cc;
    myProfile.person.settings=cc;

    settings.showAllergens();

  }

  settings.getActive=function(){
    return settings.current;
  }


  list=new Object();

  list.start=function(){
    return '<ul class="table-view">';
  }

  list.stop=function(){
    return '</ul>';
  }

  list.item=function(intext,id,active){
    if(active==undefined){active=''}
    ret='<li class="table-view-cell alist '+active+'">';
    ret+=intext;
    ret+=toggle.show(active,id);
    ret+='</li>';
    return ret;
  }

  toggle=new Object();

  toggle.show=function(active,id){
    ret='<div id="'+id+'" class="toggle '+active+'">';
      ret+='<div class="toggle-handle"></div>';
    ret+='</div>';
    return ret;

  }

  cache = new Object();

  cache.loadProductsOLDDD=function(){
    msg.show('Loading products...',10000,true);
    cache.products={};
    if(selectedDB=='demo'){
      db='prodcache';
      $('.profile-title').parent().removeClass('db-line');
      dbatabaseText='Using the Demo database';
    }else{
      db='prodcachenew';
      $('.profile-title').parent().addClass('db-line');
      dbatabaseText='Using the BrandBank database';
    }
    url=api.url(db,0);
    $('#debug').load(url,function(){
      cache.products=JSON.parse($(this).html());
      store.put('cache:products',$(this).html());
      msg.show('Products loaded <b>'+dbatabaseText+'</b>',3000);
      $('.targ-dbtype').html(dbatabaseText);
    });
  }

  cache.loadProducts=function(){
    msg.show('Loading products...',10000,true);
    cache.products={};
    if(selectedDB=='demo'){
      db='prodcache';
      $('.profile-title').parent().removeClass('db-line');
      dbatabaseText='Using the Demo database';
    }else{
      db='prodcachenew';
      $('.profile-title').parent().addClass('db-line');
      dbatabaseText='Using the BrandBank database';
    }

    api.call('prodcachenew','',function(data){
      store.put('cache:products',data);
      cache.products=JSON.parse(data);
      msg.show('Products loaded <b>'+dbatabaseText+'</b>',3000);
    });
  }








  msg=new Object();

  msg.timer=false;

  msg.show=function(msgText,time,hasSpinner){
      if(msg.timer){
        clearTimeout(msg.timer);
      }
      if((time==undefined) ? 1000 : time);
      if((hasSpinner==undefined) ? true : hasSpinner);
      m=$('.message');

      spinner='';
      if(hasSpinner){
        spinner='<div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>';
      }



      html='<span class="icon icon-info"></span>';
      m.html(html+' '+msgText+spinner);
      m.css({top:'0px'});
      msg.timer=setTimeout(function(){msg.hide()},time);
  }

  msg.hide=function(){
      clearTimeout(msg.timer);
      $('.message').css({top:'-200px'})
  }

  glob=new Object();

rules=new Object();
rules['result']=[];
rules.check=function(ingredients,test){
  for (var i in test) {
    tt=test[i];
    excludes=tt.exclude;
    res=rules.compare(tt.exclude,ingredients,tt.name);
  }
}


  rules.reset=function(){
    rules['result']=[];
  }

  rules.addResult=function(title,detail){
    if(rules['result'][title]==undefined){
        rules['result'][title]='';
    }
    rules['result'][title]+=detail+',';
  }

  rules.compare=function(inList,searchList,title){
    ret='';
    inList=inList.toLowerCase();
    searchList=searchList.toLowerCase();
    ll=inList.split(',');
    for(item in ll){
      ss=ll[item];
      if(searchList.indexOf(ss)!=-1 && ss!=''){
        rules.addResult(title,ss);
        ret+=ss+',';
      }
    }
    return ret;
  }


  newrules=new Object();
  newrules.text='';
  newrules.allergyCount=0;
  newrules.items={};

  newrules.getAll=function(){
    rr=store.get('RULES:');
    return safeJson.parse(rr);
  }

  newrules.checkItem=function(itemIngredients){
    var ca=myProfile.person.settings;
    newrules.text='';
    newrules.allergyCount=0;
    newrules.items={};
    checkList=ca.split(',');
    for(idx in checkList){
        shortCode=checkList[idx];
        if(shortCode!=''){
            compList=settings.xdata[checkList[idx]];
              if(compList.exclude!=undefined){
                eList=compList.exclude;
                cc=newrules.compare(eList,itemIngredients,compList.type+' '+compList.name);
                if(compList.type="allergens"){
                    newrules.allergyCount+=cc;
                }
              }
        }
    }
    newtext='';
    for(xxc in newrules.items){
        if(newrules.items[xxc]!=''){
          //newtext+=xxc+' contains <br/><b>'+newrules.items[xxc]+'</b><br/><br/>';
          newtext+='<span class="mm m-4"></span> <b>'+xxc+'</b><br/>';
        }
    }

    return newtext;//

  }
  newrules.compare=function(inList,searchList,title){
    ret='';
    cc=0;
    if(newrules.items[title]==undefined){newrules.items[title]=''}
    inList=inList.toLowerCase();
    searchList=searchList.toLowerCase();
    ll=inList.split(',');
    for(item in ll){
      ss=ll[item];
      if(searchList.indexOf(ss)!=-1 && ss!=''){
        newrules.text+=title+' <b>'+ss+'</b><br/>';

        newrules.items[title]+=ss+',';
        cc++;
      }
    }
    ss=newrules.items[title];
    newrules.items[title]=ss.slice(0,-1);
    return cc;
  }


  person=new Object();
  person.load=function(pref){
    data=store.get("users:"+pref);
    if(data==null){
      url=api.url('getuser',pref);
      $('#debug').load(url,function(){
        store.put("users:"+pref, $(this).html());
        person.show($(this).html());
      });
    }else{
      person.show(data);
    }
  }

  person.data={};

  person.show = function(data){
  	//person.data=JSON.parse(data);
    person.data=safeJson.parse(data);
    op='<h4>'+person.data.firstnames+' '+person.data.surname+'</h4>';
    $('#person').html(op);
    store.put('user:',data);
    store.put('user:ref',person.data.ref);
    store.put('user:name',person.data.firstnames+' '+person.data.surname);
  }



  ev=new Object();

  ev.registry={};


  ev.click=function(data,callBack){
      ev.registry[data]=callBack;
  }






  splitter=new Object();
  splitter.get=function(intext){
    data={};
    l1=intext.split('##');
    for(item in l1){
      l2=l1[item];
      l3=l2.split('|');
      data[item]=l3;
    }
    return data;
  }

  card=new Object();

  card.show=function(title,id,html){
    op='<h4 class="indent">'+title+'</h4>';
    op+='<div class="card pad5 noborder" id="'+id+'">';
    op+=html;
    op+='</div>';
    return op;
  }



  feedbackObj=new Object();

  feedbackObj.show=function(){
    $('.accountpop').css({left:'-400px'});
    $('.feedback-list').fadeTo('fast',0.3);
    api.call('feedbacklist','',function(raw){
      var data=safeJson.parse(raw);
      op='<h3>My Feedback</h3>';
      for(idx in data){
        var xx=data[idx];
        op+='<div class="xitem">';
          op+='<div>';
          op+='<div class="fhalf"><i>'+xx.ref+'</i> <small>'+xx.msgdate+'</small></div>';
          op+='<div class="fhalf tright time"><span class="hidetoggle">'+xx.os+'</span> <span class="hide lgrey">'+xx.osversion+' '+xx.model+' '+xx.maker+'</span></div>';
          op+='</div>';
          op+='<div class="msg">'+xx.message+'</div>';
          if(xx.response!=''){
              op+='<div class="resptime">'+xx.resptime+'</div>';
              op+='<div class="resp">'+xx.response+'</div>';
          }
        op+='</div>';
      }
      $('.feedback-list').html(op).fadeTo('fast',1);
    });

  }

  page=new Object();


  page.set=function(page){
    $('.ctpanel').addClass('hide');
    $('#ct-'+page).removeClass('hide');


  }




  page.route=function(pageName,t,extra){
    $('.profile-top').addClass('hide');
    $('.recalc-ball').addClass('recalc-ball-fade');
    $('.accountpop').css({left:'-60%'});
    overlay.hide();
    extra=(extra==undefined)?'':extra;
    $('.targ-back-item').addClass('hide');
      $('.content').scrollTop(0);
    $('.bpop').remove();
    $('.bottom-menu a').removeClass('selected');
    $(this).addClass('selected');
    $('.option-clear-scan').addClass('hide');
    $('.xoption-account').removeClass('hide');
    $('.option-scan-results').removeClass('hide');
    $('.active-bar').stop().hide('fast');
    page.set(pageName);
    console.log(pageName);
    $('.scan-results-button').css({position:'relative'});
    $('#fixed-menu').hide();
    switch(pageName){
      case('myshares'):
        console.log('My Share');
        $('.share-list').html('Loading...');
        api.call('sharelist','',function(data){
            $('.share-list').html(data);
            //$('.timeago').timeago();
        });


        break;
      case('downloads'):
      settings.list('M,C,D,L','download-settings');
        break;
      case('index'):
        break;
      case('retail'):
        appdata.setEateryUnderline();
        break;
        // if(extra=='eating'){
        //     op=eating.space();
        // }else{
        //   op=retail.space();
        // }
        // $('#retail-space').html(op);
        // break;
      case('scanner'):

        $('.profile-top').removeClass('hide');
        $('.option-clear-scan').removeClass('hide');
        $('.option-scan-results').addClass('hide');
        $('.active-bar').stop().show('fast');
        $('.scan-results-button').addClass('hide');
        if(extra=='rescan'){
            barcode.rerun();
        }


        break;
        case('scanner1'):
          break;
        case('newterms'):
          appdata.showTerms('.terms-innerx');
          break;
        case('communities'):
          appdata.showCategories('.targ-comm-list');
          break;
        case('myxyz'):
          $('.scan-results-button').css({position:'fixed',width:'98%',zIndex:200 });
          //embedMenu.show();
          embedMenu.show();
          $('.content').scrollTop();
          firstActive=$('.mnu-toggle.active:first');
          if(firstActive.html()!=undefined){
            var ofs=firstActive.offset();
            $('.content').scrollTop(ofs.top-130);
          }
          break;
      case('feedback'):
        feedbackObj.show();
        overlay.hide();
        break;
      case('allergensx'):
        appdata.showAllergens('.allergens-innerx');
        break;
      case('intolerances'):
        appdata.showIntolerances();
        appdata.showLikes();
      appdata.likeCount();
      appdata.setUnderline();
        break;
      case('settings'):
      $('.active-bar').stop().show('fast');
      appdata.showAllergens();
      appdata.showTerms();
      if(extra=='route'){
        $('#targ-download-list').html(appdata.showDownloads());
        $('.act-settings-tabs a').removeClass('selected');
        $('.presets').addClass('selected');
        $('.subtab').addClass('hide');
        $('#subtab-downloads').removeClass('hide');
      }else{
        $('.act-settings-tabs a').removeClass('selected');
        $('.stab-allergens').addClass('selected');
        $('.subtab').addClass('hide');
        $('#subtab-allergens').removeClass('hide');

      }
        break;
      case('person'):
        showUsers();
        break;
      case 'item':
        $('.targ-back-item').removeClass('hide');
        $('selected-item').removeClass('selected-item');
        t.addClass('selected-item');
        showItem(t.data('barcode'));
        break;
      case 'testing':
      debugStorage();
      showRules();

        break;
      case 'rules':
        showRules();
        break;
      case('search'):
        appdata.setEateryUnderline();
        break;
      case('eaterydetails'):
        $('#fixed-menu').show();
        break;
      case('profile'):
        $('.profile-top').removeClass('hide');
        $('.active-bar').stop().show('fast');
        //$('.scan-results-button').removeClass('hide');

      newslide.showAll();
      newslide.showHelp();
      if(t=='list' || extra=='list'){


        $('.subby').removeClass('selected');
        $('.subby-all').addClass('selected');
        $('#subtab-sliders').addClass('hide');
        $('#subtab-profiles').removeClass('hide');
        //var dl=appdata.showDownloads();


        var pp=appdata.showProfiles();
        $('.targ-download-list').html(pp);

      }
      //settings.getList();
        //pp=showProfileRules();
        //$('#profiles').html(pp);
        //ref=t.data('ref');
        //if(ref==undefined){
        //    ref=localStorage.getItem("user:ref");
        //}
        //person.load(ref);
        break;
      case 'debug':
        break;
    }

  }

  uid=new Object();


  uid.create= function (len, bits)
  {
      bits = bits || 36;
      var outStr = "", newStr;
      while (outStr.length < len)
      {
          newStr = Math.random().toString(bits).slice(2);
          outStr += newStr.slice(0, Math.min(newStr.length, (len - outStr.length)));
      }
      return outStr.toUpperCase();
  };

  util=new Object();


  util.titleCase=function(str){
      return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
  }
