var proddata=new Object();

var appStatus=new Object();

appStatus.mode='shopping';

var proddata={};
proddata.lastBarcode='';

var lastPosition='nogps';

var barcodeStore=new Object();

var lastCompare=new Object();

var appdata=new Object();

var appsignup=new Object();

var userdata=new Object();
userdata.email='';
userdata.userid='';

var feedbackCache=new Object();
feedbackCache.list=[];

var barcodecache={};


feedbackCache.add=function(message){
  var fbl=feedbackCache.list;
  fbl.push(message);
}

feedbackCache.showlist=function(){
  for(idx in feedbackCache.list){
  }
}

feedbackCache.send=function(){

}


appStatus.setWorkingMode=function(mode,t,routePage){
  routePage=(routePage==undefined)? true : routePage;
  store.put('appStatus:workingMode',mode);
  var b4=$('.targ-bay4');
  var scan=$('.scan-btn');
  switch(mode){
    case 'shopping':
      b4.find('i').removeClass('fa-cutlery').addClass('fa-shopping-cart');
      b4.find('.tab-label').text('Shopping');
      scan.text('SCAN').data('extra','shopping');
      b4.data('extra','shopping');

      appStatus.mode='shopping';
      $('.scan-btn').removeClass('scan-btn-shop').html('SCAN');
      if(routePage){
          page.route('retail',t,'shopping');
      }
      break;
    case 'eating':
      b4.find('i').removeClass('fa-shopping-cart').addClass('fa-cutlery');
      b4.find('.tab-label').text('Eating');
      scan.text('MENU').data('extra','eating');
      b4.data('extra','eating');

      appStatus.mode='eating';
      $('.scan-btn').addClass('scan-btn-shop');
      $('.scan-btn').html('<i class="fa fa-map-o" aria-hidden="true"></i>MENU');
      if(routePage){
          page.route('retail',t,'eating');
      }
      break;
  }

}




appdata.device='';


appdata.loadBarcodeCache=function(cmd){
  cmd=(cmd==undefined)?'loadcache':cmd;
  if(connect.connected()){
    msg.show('Loading barcode data',9000,true);
    api.call(cmd,'',function(data){
      store.put('barcodecache',data);
      barcodecache=JSON.parse(data);
      msg.show('Loaded data '+Object.keys(barcodecache).length,1000);
    });
  }else{
    msg.show('No network',2000);
  }

}






appsignup.put=function(state){
  store.put('app.signup.state',state);
}

appsignup.get=function(){
  if(store.get('app.signup.state')==''){
    return 'new';
  }else{
    return store.get('app.signup.state');
  }
}

appsignup.showOld=function(){
  state=appsignup.get();
  switch(state){
    case 'new':

    appsignup.put('allergens');
    appdata.showTerms('#signup-terms');
    $('.signup-allergens').remove();



      break;
    case 'allergens':
    appdata.showAllergens('#signup-allergens');

      break;
    case 'completed':
    $('.signup-panel').remove();
      break;
  }
}

appsignup.show=function(){
  state=appsignup.get();
  state='completed';
  switch(state){
    case 'new':
    //appsignup.put('allergens');
    appdata.showTerms('#signup-terms');
    $('.signup-allergens').remove();
      break;
    case 'allergens':
      appdata.showAllergens('#signup-allergens');
      break;
    case 'completed':
    $('.signup-panel').remove();
      break;
  }
}



appdata.state={};
appdata.state.profileNo=1;
appdata.default={};

appdata.allergens={};
appdata.intolerances={};
appdata.intolerances.count=0;

appdata.userProfiles=[];
appdata.userProfiles[1]={name:'General',ingredients:'',calories:220,totalfat:16.5,saturatedfat:3.3,salt:1.9,sugar:9,protein:21,carbs:9.3,fibre:5,cholesterol:37};
appdata.userProfiles[2]={name:'#DELETED#'};
appdata.userProfiles[3]={name:'#DELETED#'};
appdata.userProfiles[4]={name:'#DELETED#'};
appdata.userProfiles[5]={name:'#DELETED#'};
appdata.userProfiles[6]={name:'#DELETED#'};
appdata.userProfiles[7]={name:'#DELETED#'};
appdata.userProfiles[8]={name:'#DELETED#'};
appdata.userProfiles[9]={name:'#DELETED#'};
appdata.userProfiles[10]={name:'#DELETED#'};
appdata.userProfiles[11]={name:'#DELETED#'};
appdata.userProfiles[12]={name:'#DELETED#'};
appdata.userProfiles[13]={name:'#DELETED#'};
appdata.userProfiles[14]={name:'#DELETED#'};
appdata.userProfiles[15]={name:'#DELETED#'};

appdata.default.userProfiles={name:'New profile',calories:0,totalfat:0,saturatedfat:0,salt:0,sugar:0,protein:0,carbs:0,fibre:0,cholesterol:0,ingredients:''};


appdata.shortcuts={};
appdata.shortcuts[1]={ref:1,name:'Profile 1'};
//appdata.shortcuts[2]={ref:2,name:'Profile 2'};
//appdata.shortcuts[3]={ref:3,name:'Profile 3'};

appdata.user={};
appdata.user.allergens='';
appdata.user.intolerances={};

appdata.slider={};
appdata.slider.calories={label:'calories',below:100,mid:200,above:300,max:450,start:0,unit:'Kcal'};
appdata.slider.totalfat={label:'total fat',below:3,mid:15,above:25,max:35,start:0,unit:'g'};
appdata.slider.saturatedfat={label:'saturated fat',below:1.5,mid:3,above:5,max:7,start:0,unit:'g'};
appdata.slider.salt={label:'salt',below:0.3,mid:0.9,above:1.5,max:4,start:0,unit:'g'};
appdata.slider.sugar={label:'sugar',below:5,mid:10,above:15,max:20,start:0,unit:'g'};
appdata.slider.protein={label:'protein',below:8,mid:21,above:35,max:40,start:0,unit:'g'};
appdata.slider.carbs={label:'carbs (Starch)',below:9,mid:12,above:15,max:20,start:0,unit:'g'};
appdata.slider.fibre={label:'fibre',below:3,mid:4.5,above:6,max:10,start:0,unit:'g'};
appdata.slider.cholesterol={label:'cholesterol',below:15,mid:35,above:60,max:80,start:0,unit:'mg'};


data=new Object();
data.check=function(){
  //check for missing data and insert defaults if required
  if(appdata.shortcuts[1]==undefined){
    data.resetShortcuts();
    appdata.saveShortcuts();
  }
}

data.resetShortcuts=function(){
  return;
  appdata.shortcuts[1]={ref:1,name:'General'};
  //appdata.shortcuts[2]={ref:2,name:'Profile 2'};
  //appdata.shortcuts[3]={ref:3,name:'Profile 3'};

}

appdata.findFirstActive=function(){
    var idx=0;
    for(idx in appdata.userProfiles){
        if(appdata.userProfiles[idx]['name']!='#DELETED#'){
          return idx;
        }
    }
    return -1;
}


appdata.findFreeProfile=function(){
    var idx=0;
    for(idx in appdata.userProfiles){
        if(appdata.userProfiles[idx]['name']=='#DELETED#'){
          return idx;
        }
    }
    popup.show('No free profiles. Please delete an existing profile','OK');
    return -1;
}

appdata.makeCopy=function(inRef){
    var newCopy = jQuery.extend(true, {}, appdata.userProfiles[inRef]);
    newCopy.preset=false;
    newCopy.name='My '+newCopy.name;
    var free=appdata.findFreeProfile();
    appdata.insert(newCopy);
    appdata.state.profileName=newCopy.title;
    appdata.state.profileNo=1;
    newslide.updateStore();
    page.route('profile','list');
    first=$('.act-download-menu:first');
    first.find('.toggle').trigger('click');
    first.find('.act-profile-title').trigger('click');
}

appdata.removeDeleted=function(){
  var tmp = jQuery.extend(true, {}, appdata.userProfiles);
  var cc=1;
  for(var tt=1;tt<=29;tt++){
    if(tmp[tt]!=undefined){
      if(tmp[tt]['name']!='#DELETED#' && tmp[tt]['name']!=undefined){
        var nw = jQuery.extend(true, {}, tmp[tt]);
        appdata.userProfiles[cc]=nw;
        cc++;
      }

    }
  }
}

appdata.insert=function(inData){
  appdata.removeDeleted();
  for(tt=29;tt>1;tt--){
      if(appdata.userProfiles[tt-1]==undefined){
        var newProfile={name:'#DELETED#',calories:0,totalfat:0,saturatedfat:0,salt:0,sugar:0,protein:0,carbs:0,fibre:0,cholesterol:0,ingredients:''};
        appdata.userProfiles[tt-1]=newProfile;
      }
      var copy = jQuery.extend(true, {}, appdata.userProfiles[tt-1]);
      appdata.userProfiles[tt]=copy;
  }
  appdata.userProfiles[1]=inData;

}

appdata.showShortcuts=function(){
  for(idx in appdata.shortcuts){
    g=appdata.shortcuts[idx];
    if(appdata.userProfiles[g.ref]!=undefined){
      prof=appdata.userProfiles[g.ref];
      if(prof.name!='#DELETED#'){
        $('.fave-item-'+idx).html('<div class="cc">'+prof.name+'</div>');
        $('.fave-item-'+idx).attr('href','#'+g.ref);
      }else{
        $('.fave-item-'+idx).html('<span></span>').attr('href','#-1');
      }

    }else{
        $('.fave-item-'+idx).html('<span></span>').attr('href','#-1');
    }
  }
}




appdata.saveShortcuts=function(){
  store.put('appdata.shortcuts',JSON.stringify(appdata.shortcuts));
  appdata.showShortcuts();
}

appdata.loadShortcuts=function(){
    appdata.shortcuts=safeJson.parse(store.get('appdata.shortcuts'));
    appdata.showShortcuts();
}

appdata.showProfiles=function(){
  appdata.setFixedProfiles();
  var op='';
  //op+='<a href="#" class="btn-profile-edit act-profile-edit-start" dtat-mode="notedit">Edit</a>';
  var idx;
  iconlist.setIcon('icon-compose');
  for(idx in appdata.userProfiles){
    pp=appdata.userProfiles[idx];
    if(pp.name!='#DELETED#'){
      pos=appdata.isShortcut(idx);
        op+=iconlist.item(pp.name,'act-download-menu',idx,pos);
    }

  }
  return op;
}

appdata.isShortcut=function(inRef){
  var idx1;
  for(idx1 in appdata.shortcuts){
    if(appdata.shortcuts[idx1]['ref']==inRef){
      return idx1;
    }
  }
  return -1;
}

appdata.loadallergens=function(){
  if(connect.connected()){
    api.call('allergens','',function(data){
      appdata.allergens=safeJson.parse(data);
      store.put('appdata.allergens',data);
    });
  }else{
      msg.show('loading from cache',2000);
      appdata.allergens=safeJson.parse(store.get('appdata.allergens'));
  }
}

appdata.loadprofiles=function(){
  api.call('profiles','',function(data){
    appdata.profiles=safeJson.parse(data);
    appdata.setFixedProfiles();
    profileTools.loadCurrent();
  });
}

appdata.saveprofiles=function(){

}

appdata.setFixedProfiles=function(){
  slot=1;
  for(idx in appdata.profiles){
    var tp=appdata.profiles[idx];
    tp.preset=true;
    appdata.userProfiles[slot]=tp;
    slot++;
  }
  console.log(appdata.userProfiles);

}

appdata.getProfileItem=function(item){
  try {
	    return appdata.userProfiles[appdata.state.profileNo][item];
	}
	catch(err) {
	    return '';
	}

}

appdata.showIngredients=function(){
  op='<h4>Excluded Ingredients</h4>';
  op+=appdata.getProfileItem('ingredients');
  $('#subtab-ingredients').html(op);
  return op;
}

appdata.communities='';

appdata.showCategories=function(inTarget){
  var op='';
  var comms=['Arthritis','Babies','Cancer','Coeliacs','Dementia','Diabetics','Family Friendly','Fine Dining','Fit or Getting Fitter','Food Allergies','Heart Disease','Silver Surfers','Toddlers'];
  op+=picker.start('','',true);
  op+=picker.startList();

  for(var idx in comms){
    //op+=comms[idx]+',';
    op+=picker.add(comms[idx],true,appdata.communities,'comm-toggle');
  }
  op+=picker.stopList();
  op+=picker.close();

  $(inTarget).html(op);
}

appdata.updateComms=function(){
  var comm='';
  $('.comm-toggle.active').each(function(){
    var p=$(this).parent();
    comm+=p.text()+',';
    console.log(p.text());
  });
  appdata.communities=comm.slice(0, -1);
  store.put('appdata.communities',appdata.communities);
}

appdata.loadCommunities=function(){
appdata.communities=store.get('appdata.communities');
}


appdata.showAllergens=function(inTarget){
  inTarget=(inTarget==undefined)?'#allergenlist':inTarget;
  op='';
  op+=picker.start('','',true);
  op+=picker.startList();
    for(idx in appdata.allergens.A){
      op+=picker.add(appdata.allergens.A[idx]['title'],true,appdata.user.allergens,'allergen-toggle');
    }
    op+=picker.stopList();
    op+=picker.close();
    $(inTarget).html(op);
}

appdata.showIntolerances=function(){
  op='';
    for(idx in appdata.allergens.I){
      var itemName=appdata.tryIntolerance(idx);
      op+=picker.startIntol(appdata.allergens.I[idx]['title']);
      bd=appdata.allergens.I[idx]['breakdown'];
      if(bd!=null){
        op+=picker.startList('hide');
        bdl=bd.split(',');
        op+=picker.addIntol('Select All',itemName,'targ-intol-item act-select-all');
        for(idx in bdl){
            op+=picker.addIntol(bdl[idx],itemName,'targ-intol-item');
        }
        op+=picker.stopList();
        op+=picker.close();

      }else{
          op+=picker.close();
      }
    }
    //$('#intolerancelist').html(op);
    $('.intolerances-innerx').html(op);



    appdata.intoleranceCount();
}

appdata.intoleranceCount=function(){
  $('.intolerance-lists').each(function(){
    var t=$(this);
    actives=t.find('.targ-intol-item.active');
    if(actives.length>0){
        t.addClass('hasitems');
    }else{
      t.removeClass('hasitems');
    }
  });

}

appdata.tryIntolerance=function(idx){
  try {
      return appdata.user.intolerances[idx];
  }
  catch(err) {
      return '';
  }
}

appdata.loadIntolerancePrefs=function(){
  if(store.get('appdata.user.intolerances')!=''){
      appdata.user.intolerances=JSON.parse(store.get('appdata.user.intolerances'));
  }else{
      appdata.user.intolerances={};
  }
}

appdata.saveIntolerancePrefs=function(){
  appdata.intoleranceCount();
  appdata.user.intolerances={};
  $('.intolerance-lists').each(function(){
    subList='';
    var t=$(this);
    listIdx=t.find('h5').text();
    t.find('.targ-intol-item.active').each(function(){
      var tt=$(this);
      subList+=tt.data('ref')+',';
    });
    if(subList!=''){
        appdata.user.intolerances[listIdx]=subList;
    }
  });
  store.put('appdata.user.intolerances',JSON.stringify(appdata.user.intolerances));
}



appdata.showTerms=function(inTarget,inText){
  var inText=(inText==undefined)?'':inText;
      $(inTarget).load('terms.html');
      $('.targ-terms-pre').html(inText);

}

appdata.showProfileSlides=function(data){
  op=engine.plotSlider('calories',data['calories']);
  op+=engine.plotSlider('totalfat',data['totalfat']);
  op+=engine.plotSlider('saturatedfat',data['saturatedfat']);
  op+=engine.plotSlider('salt',data['salt']);
  op+=engine.plotSlider('sugar',data['sugar']);
  op+=engine.plotSlider('protein',data['protein']);
  op+=engine.plotSlider('fibre',data['fibre']);
  op+=engine.plotSlider('cholesterol',data['cholesterol']);
  return op;
}

appdata.showDownloads=function(){
  op='';
  iconlist.setIcon('icon-plus');
  console.log(appdata.profiles);
  for(idx in appdata.profiles){
    item=appdata.profiles[idx];

    op+=iconlist.itemDownload(item,'act-download-selected');
  }
  return op;

}
