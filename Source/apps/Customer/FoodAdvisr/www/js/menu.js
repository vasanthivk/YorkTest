

var embedMenu = new Object();

embedMenu.loadx=function(){
  //var menuPath='http://52.56.130.182/cxx/workspace/quicr/content/menu/dlmenu.php';
  var menuPath='http://api.foodadvisr.com/menu/dlmenu.php';
var menuPath='./res/menu.html';
  $.get(menuPath,function(data){
      embedMenu.html=data;
      store.put('menu.data',embedMenu.html);
      embedMenu.draw();
  });
}

embedMenu.load = function(){
  api.call('menu','',function(data){
    embedMenu.html='<h4 style="margin-top:40px;text-align:center;font-size:2.0rem;color:#707070;">Coming Soon</h4>';
    store.put('menu.data',embedMenu.html);
    embedMenu.draw();
  });
}

embedMenu.html='';

embedMenu.show=function(){
  //embedMenu.html=store.get('menu.data');
  //if(embedMenu.html==''){
    embedMenu.html=embedMenu.load();
  //}else{
    //embedMenu.draw();
//  }
}

embedMenu.showSchool=function(){
  api.call('schoolmenu','',function(data){
    //embedMenu.html='<h4 style="margin-top:40px;text-align:center;font-size:2.0rem;color:#707070;">Coming Soon</h4>';
    embedMenu.html=data;
    store.put('menu.data',embedMenu.html);
    $('.ct-menu-target').html(embedMenu.html);
  });
}

embedMenu.draw=function(){
  if($('.ct-menu-target').html()==''){
    $('.ct-menu-target').html(embedMenu.html);
  }
}

embedMenu.bind=function(){
  var body=$('body');

  body.on('mouseup','.mnu-toggle',function(){
    return false;
  });

  body.on('click','.act-import-menu',function(){
    embedMenu.startCompare();
    return false;
  });

  embedMenu.startCompare=function(clearCodes){
    clearCodes=(clearCodes==undefined)?true:false;
    if(clearCodes){
      barcode.clear();
  }
    $('.mnu-toggle.active').each(function(){
      var t=$(this);
      pp=t.parent().parent();
      var startData=pp.data();
      var jsonData=new Object();
      jsonData.barcode=startData.ref;
      jsonData.title=pp.find('.mnu-item-title').text();
      jsonData.desc=pp.find('.mnu-item-desc').text();
      jsonData.ingredients='';
      jsonData.itemType='menu';
      jsonData.allergens=startData.contains;
      jsonData.calories=startData.calories;
      jsonData.maycontain=startData.may;
      jsonData.fromMenu=true;
      barcode.useData(jsonData,jsonData.barcode);
      lastCompare[startData.ref]=startData.ref;
      var allergenList=embedMenu.checkAllergens(startData.contains);
      var mayList=embedMenu.checkAllergens(startData.may);
    });

  }

  embedMenu.checkToggle=function(p){
    if(p.hasClass('mnu-toggle')){

    }
  }



  body.on('click','.qsr-menu h3',function(){
    $(this).next().next().toggleClass('active');
    embedMenu.countMenuItems();
    return false;
  });

  body.on('click','.act-mnu-clicked',function(){
    var t=$(this).parent();
    var tog=t.find('.toggle');
    tog.toggleClass('active');
    //embedMenu.startCompare();
    embedMenu.countMenuItems();
  });

  $('body').on('click','.mnu-sub-item',function(){
    t=$(this);
    parentRef=t.data('subref');
    p=$('.mnu-ref-'+parentRef);
      if(!t.hasClass('selected')){
        p.find('.toggle').addClass('active');
        t.addClass('selected');
      }else{
        t.removeClass('selected');
      }
      baseCalories=p.data('calories');
      addedCalories=0;
      $('.mnu-sub-ref-'+parentRef).each(function(){
        tt=$(this);
        if(tt.hasClass('selected')){
          addedCalories+=parseInt(tt.data('calories'));
        }
      });
      p.find('.mnu-targ-cal').text(baseCalories+addedCalories);
      return false;
    });
}

embedMenu.checkAllergens=function(inAllergens){
  if(inAllergens!=undefined){
    var myAllergensList=appdata.user.allergens.split(',');
    var op='';
    for(idx in myAllergensList){
        if(myAllergensList[idx]!=''){
          if(inAllergens.search(myAllergensList[idx])!=-1){
            op+=myAllergensList[idx]+',';
          }
        }
    }
    return op.slice(0, -1).trim();
  }else{
    return '';
  }
}

embedMenu.countMenuItems=function(){
    dd=$('.mnu-toggle.active');
    basket=$('.qsr-basket');
    basketInner=basket.find('span');
    var cc=dd.length;
    var inHtml='';
    if(cc>0){
        basket.show('fast');
        if(cc==1){
            basketInner.html('<b>1</b> Item selected');
        }else{
            basketInner.html('<b>'+cc+'</b> Items selected');
        }
    }else{
        basket.hide('fast');
    }
    //countToHeadings();

}
