var engine=new Object();


engine.sliders=function(){
  engine.currentItem=proddata.currentItem;
  engine.currentProfile=appdata.userProfiles[appdata.state.profileNo];
  engine.totalScore=0;
  engine.maxColor=0;
  engine.allergens=0;
  engine.intolerances=0;
  engine.special=0;

}

engine.sliderStream=function(){
    var tp=appdata.userProfiles[appdata.state.profileNo];
    op='';
    op+=tp.calories+'|';
    op+=tp.totalfat+'|';
    op+=tp.saturatedfat+'|';
    op+=tp.salt+'|';
    op+=tp.sugar+'|';
    op+=tp.protein+'|';
    op+=tp.fibre+'|';
    op+=tp.cholesterol;
    return op;
}


engine.drawPrecis=function(data,showPrecis){
  console.log(data);
  var xclass='';
  msg.hide();
  if(data==undefined){
    return;
  }
  showPrecis=(showPrecis==undefined)?true:showPrecis;
  engine.checkAllergens();
  engine.checkIntolerances();
  engine.scanProfileIngedients(appdata.userProfiles[appdata.state.profileNo]['ingredients']);
var extra='';
var may='';

var allExtra='';
  var xmay=engine.checkAllergensGeneric(data.maycontain);
  if(xmay!=''){
    console.log('FLAG May');
    may='may';
    allExtra='may';
    engine.totalScore+=40;
  }
  var op='';
  if(engine.allergens>0){
    console.log('FLAG Allergen');
    extra='alert';
    allExtra='alert';
    xclass='alert';
    engine.totalScore+=50;
  }else{
    if(engine.intolerances>0 || engine.special>0){
      console.log('FLAG Intol');
      extra='exclamation';
      if(may!=''){
          console.log('FLAG override May');
          allExtra='may';
      }else{
        allExtra='exclamation';
      }

      xclass='intol-item';
      engine.totalScore+=40;
      if(xmay!=''){
            allExtra='may';
      }
    }
  }
  if(((data.calories==0 && data.totalfat==0) || data.ingredients=='') && appStatus.mode=='shopping'){
    engine.totalScore=-1;
    engine.maxColor=99;

  }
  var contains='';
  op+='<div class="scanned '+xclass+'" data-barcode="'+data.barcode+'" data-altcode="'+data.barcode_alt+'">'; // alert class can be added
  op+='<div class="sortval">'+engine.totalScore+'</div>';
    op+='<div class="com-share act-com-share">Share</div>';
    //op+='<div class="act-view-preview counter pipbar'+engine.maxColor+'" data-barcode="'+data.barcode+'">'+engine.totalScore+'</div>';
console.log(allExtra);
    if(allExtra!=''){
      op+='<div class="act-view-preview counter" data-mode="'+allExtra+'" data-barcode="'+data.barcode+'">';
      switch(allExtra){
        case 'alert':
          op+='<img src="./img/sq-allergen.svg" alt=""/>';
        break;
        case 'exclamation':
          op+='<img src="./img/sq-intol.svg" alt=""/>';
        break;
        case 'may':
          op+='<img src="./img/sq-may.svg" alt=""/>';
        break;
        default:

        op+='<div class="act-view-preview counter pipbar'+engine.maxColor+'" data-barcode="'+data.barcode+'"></div>';

        break;
      }
      op+='</div>';
    }else{
      op+='<div class="act-view-preview counter pipbar'+engine.maxColor+'" data-barcode="'+data.barcode+'"></div>';
    }


    op+='<div class="op1">';
      op+='<a href="#item" data-barcode="'+data.barcode+'" class="act-view-detail">';
            op+=engine.prodImage(data.barcode_alt,data);
      op+='</a>';
      op+='<span>';
  		  op+='<span class="prod-title-precis noselect">'+data.title+'</span><br/>';
  		  op+='<span class="prod-alerts">'+contains+'</span>';
        if(data.fromcache!=undefined){
          op+='.';
        }
        op+='<br/>';
      op+='</span>';
    op+='</div>';
  op+='</div>';
  var opList=$('#list');
  if(!opList.next().hasClass('hide')){
    opList.next().addClass('hide');
  }
  opList.prepend(op);
  if(showPrecis){
      page.route('scanner');
  }
  tinysort('.scanned','.sortval');
}



engine.prodImage=function(bcode,jsonData){
      s3Path=appSettings.s3Path;
      src=s3Path+engine.barcodeHash(bcode)+'.jpg';
      if(jsonData.fromMenu!=undefined){
          return '<img class="prod-precis-img" src="img/dlicious.jpg"/>';
      }else{
          return '<img class="prod-precis-img" src="'+src+'" alt="" />';
      }

}

engine.barcodeHash=function(bcode){
  return CryptoJS.MD5(bcode+appSettings.hash);
}


engine.score=function(item){
    var ret=0;
    var currentProfile=appdata.userProfiles[appdata.state.profileNo];
    if(proddata.currentItem!=undefined){
      var currentItem=engine.currentItem=proddata.currentItem;
      var currentItemVal=parseFloat(currentItem[item]);
      if(currentItemVal==0){
        return 0;
      }
      var slider=appdata.slider[item];
      var sliderPos=parseFloat(currentProfile[item]);
      var pip=100/slider.max;
      var below=pip*slider.below;
      var mid=pip*slider.mid;
      var above=pip*slider.above;
      var max=pip*slider.max;
      var rangeAbove=slider.max-sliderPos;
      var perc1=rangeAbove/100;
      var newBelow=sliderPos+(below*perc1);
      var newMid=sliderPos+(mid*perc1);
      var newAbove=sliderPos+(above*perc1);
      var newMax=sliderPos+(max*perc1);
      if(currentItemVal>=newAbove){
        ret= 3;
      }else{
        if(currentItemVal>=newMid){
          ret= 2;
        }else{
            if(currentItemVal>=newBelow){
              ret= 1;
            }
        }
      }
      if(ret>engine.maxColor){
        engine.maxColor=ret;
      }
      return ret;
    }
    return -1;
}

engine.fullScore=function(){
  engine.totalScore=0;
  engine.maxColor=0;
  engine.totalScore+=engine.score('calories');
  engine.totalScore+=engine.score('totalfat');
  engine.totalScore+=engine.score('saturatedfat');
  engine.totalScore+=engine.score('salt');
  engine.totalScore+=engine.score('sugar');
  engine.totalScore+=engine.score('protein');
  engine.totalScore+=engine.score('carbs');
  engine.totalScore+=engine.score('fibre');
  engine.totalScore+=engine.score('cholesterol');
  tt=engine.totalScore+'.'+engine.currentItem.calories;
  return tt;
}



engine.plotAll=function(){
  var currentProfile=appdata.userProfiles[appdata.state.profileNo];
  engine.totalScore=0;
  op=engine.plot('calories');
  op+=engine.plot('totalfat');
  op+=engine.plot('saturatedfat');
  op+=engine.plot('salt');
  op+=engine.plot('sugar');
  op+=engine.plot('protein');
  op+=engine.plot('carbs');
  op+=engine.plot('fibre');
  op+=engine.plot('cholesterol');
  //op+='<div class="total-score">'+engine.totalScore+'</div>';
  return op;
}

engine.plotPreview=function(){
  var currentProfile=appdata.userProfiles[appdata.state.profileNo];
  engine.totalScore=0;
  op='<h5>'+proddata.currentItem.title+'</h5>';
  op+=engine.extras();
  op+='<h5>NUTRITION</h5>';
  op+=engine.plot('calories',true);
  op+=engine.plot('totalfat',true);
  op+=engine.plot('saturatedfat',true);
  op+=engine.plot('salt',true);
  op+=engine.plot('sugar',true);
  op+=engine.plot('protein',true);
  op+=engine.plot('carbs',true);
  op+=engine.plot('fibre',true);
  op+=engine.plot('cholesterol',true);


  return op;

}

engine.extras=function(){
  op='<br/>';
  var allergens=engine.checkAllergens();
  if(allergens!=''){
    op+='<h5>ALLERGENS</h5>';
    op+='<p class="alergen-list">';
      op+=allergens;
    op+='</p>';
  }
  var intolerances=engine.checkIntolerances();
  if(intolerances!=''){
    op+='<h5>INTOLERANCES</h5>';
    op+='<p class="intol-list">';
      op+=intolerances;
    op+='</p>';
  }
  if(appdata.userProfiles[appdata.state.profileNo]['ingredients']!=undefined){
    var profileIngredients=engine.scanProfileIngedients(appdata.userProfiles[appdata.state.profileNo]['ingredients']);
    if(profileIngredients!=''){
      op+='<h5>PROFILE INGREDIENTS</h5>';
      op+='<p class="intol-list">';
        op+=profileIngredients;
      op+='</p>';
    }
  }
  return op;

}



engine.plot=function(item,miniplot){
  miniplot=(miniplot==undefined)?false:miniplot;
  var score=engine.score(item);
  var slider=appdata.slider[item];
  var currentItem=proddata.currentItem;
  if(currentItem[item]!=undefined){
    engine.totalScore+=score;

    var pip=100/slider.max;

    var currentProfile=appdata.userProfiles[appdata.state.profileNo];
    pslide=currentProfile[item];
    vpslide=pslide*pip;
    val=currentItem[item];
    ss=val*pip;
    if(ss>100){
      ss=100;
    }
    scaled=(ss)+'%';
    appdata.slider[item]['current']=scaled;
    xclass='standard';
    if(item=='fibre' || item=='protein'){
      xclass='reversed';
    }
    op='';
    if(!miniplot){
      //op='<div class="minilabel">'+slider.label+'<small>'+val+slider.unit+' '+score+'</small></div>';
      op='<div class="minilabel">'+slider.label+'<small>'+val+slider.unit+'</small></div>';
      op+='<div class="minibar">';
        op+='<div class="widebar pipbar'+score+' '+xclass+'" style="width:'+scaled+'"></div>';
        op+='<div class="widebar-line" style="left:'+vpslide+'%"></div>';
      op+='</div>';
    }else{
        if(score>0){
        op='<div class="blobwrap">';
          op+='<div class="pipbar'+score+' blob"></div>';
          op+='<div class="preview-label">'+slider.label+'<small>'+val+slider.unit+'</small></div>';

        op+='</div>';
      }
    }

    return op;

  }else{
    return '';
  }
}

engine.plotSlider=function(item,slideVal){
  var slider=appdata.slider[item];
  var pip=100/slider.max;
  vpslide=slideVal*pip;
  op='<div class="minilabel">'+slider.label+'<small>'+slideVal+slider.unit+'</small></div>';
  op+='<div class="minibar">';
    op+='<div class="widebar" style="width:'+vpslide+'%"></div>';
  op+='</div>';
  return op;
}



engine.ingredients=function(){
  if(proddata.currentItem!=undefined){
    ingredients=proddata.currentItem['ingredients'];
    ingredients=ingredients.toLowerCase();
    return ingredients;

  }else{
    return '';
  }
}

engine.checkIntolerances=function(){
    return engine.checkIntolerancesDetail(engine.ingredients());
}

engine.checkIntolerancesDetail=function(iList){
  var op='';
  engine.intolerances=0;
  var ingredients=iList;
  var intolList=appdata.user.intolerances;
  if(intolList!=undefined){
    var idx=0;
    for(idx in intolList){
      var tt=intolList[idx];
      xlist=tt.split(',');
      var ixx=0;
      var found='';
      for(ixx in xlist){
        if(xlist[ixx]!=''){
          if(ingredients.search(xlist[ixx])!=-1){
            engine.intolerances++;
            found+=xlist[ixx]+',';
          }
        }
      }
      if(found!=''){
        found=found.slice(0, -1);
        op+='<b>'+idx+'</b> (<small>'+found+'</small>)<br/>';
      }
    }
    return op;

  }

}


engine.checkAllergens=function(){
  engine.allergens=0;
  var op='';
    var myAllergensList=appdata.user.allergens.split(',');
    var idx=0;
    for(idx in myAllergensList){
      op+=engine.scanAllergen(appdata.allergens['A'][myAllergensList[idx]]);
    }
    return op;

}

engine.scanAllergen=function(allergenData){
  ingredients=engine.ingredients();

  found='';
  if(allergenData!=undefined){
    var cl=allergenData['breakdown'];
    var checkList=cl.split(',');
    var idx=0;
    for(idx in checkList){
      word=checkList[idx];
      if(word!=''){
        if(ingredients.search(word)!=-1){
          engine.allergens++;
          found+=word+',';
        }
      }
    }
    if(found!=''){
      found=found.slice(0, -1);
      return '<b>'+allergenData.title+'</b> (<small>'+found+'</small>) <br/>';
    }
  }
  return '';
}


engine.checkAllergensGeneric=function(listToCheck){
  //engine.allergens=0;
  var op='';
    var myAllergensList=appdata.user.allergens.split(',');
    var idx=0;
    for(idx in myAllergensList){
      op+=engine.scanAllergenGeneric(appdata.allergens['A'][myAllergensList[idx]],listToCheck);
    }
    return op;

}



engine.scanAllergenGeneric=function(allergenData,listToCheck){
  listToCheck=(listToCheck==undefined)?'':listToCheck;
  listToCheck=listToCheck.toLowerCase();
  found='';
  if(allergenData!=undefined){
    var cl=allergenData['breakdown'];
    var checkList=cl.split(',');
    var idx=0;
    for(idx in checkList){
      word=checkList[idx];
      if(word!=''){
        if(listToCheck.search(word)!=-1){
          //engine.allergens++;
          found+=word+',';
        }
      }
    }
    if(found!=''){
      found=found.slice(0, -1);
      return '<b>'+allergenData.title+'</b> (<small>'+found+'</small>) <br/>';
    }
  }
  return '';
}


engine.scanProfileIngedients=function(ingedientData){
  engine.special=0;
  ingredients=engine.ingredients();
  found='';
  if(ingedientData!=undefined){
    var checkList=ingedientData.split(',');
    var idx=0;
    for(idx in checkList){
      word=checkList[idx];
      if(word!=''){
        if(ingredients.search(word)!=-1){
          engine.special++;
          found+=word+',';
        }
      }
    }
    if(found!=''){
      found=found.slice(0, -1);
      return '<b>Profile Ingedients</b> (<small>'+found+'</small>) <br/>';
    }
  }
  return '';
}


engine.catchWeight=function(bcode){
  var ret='';
  bcode=bcode.toString();
  var code=bcode.substr(0,2);
  switch(code){
    case '02':
      var priceZone=bcode.substr(-6,5);
      var pence=priceZone.substr(-2);
      var pounds=priceZone.substr(0,3);
      ret='&pound;'+parseInt(pounds)+'.'+pence;
      break;
    default:

  }
  return ret;
}




engine.process=function(jsonData,showDetail){
  showDetail=(showDetail==undefined) ?true : showDetail;
  proddata.currentItem=jsonData;
  op='<div class="half">';
    op+='<div class="sq-img">';
    op+=engine.prodImage(jsonData.barcode_alt,jsonData);
    op+='</div>';
  op+='</div>';
  op+='<div class="half">';
    op+='<h5 class="prod-title">'+jsonData.title+'</h5>';
    op+='<b>'+engine.catchWeight(jsonData.barcode)+'</b>';
  op+='</div>';
  op+='<p>';
    op+=jsonData['desc'];
  op+='</p>';


  if((jsonData.calories==0 && jsonData.totalfat==0) || jsonData.ingredients==''){
    op+='<div class="data-warning">';
    op+='The full content details for this product are not available currently.  We have logged this item with our suppliers and will get this remedied as soon as possible.  Thank you.';
    op+='</div>';
  }



  op+='<h5>ALLERGENS</h5>';
  op+='<p class="alergen-list">';
    op+=engine.checkAllergens();
    var xallergens=engine.checkAllergensGeneric(jsonData.contains);
    if(xallergens!=''){
      op+='Contains <b>'+xallergens+'</b>';
    }

    var xmay=engine.checkAllergensGeneric(jsonData.maycontain);
    if(xmay!=''){
      op+='May Contain <b>'+xmay+'</b>';
    }



    //var xmay=embedMenu.checkAllergens(jsonData.maycontain);
    //    if(xmay!=''){
    //      op+='<br/>May contain <b>'+xmay+'</b>';
    //    }
    //var titleAllergens=embedMenu.checkAllergens(jsonData.title);
    //if(titleAllergens!=''){
    //  op+='<br/>Title contain <b>'+titleAllergens+'</b>';
  //  }



  op+='</p>';
  op+='<h5>INTOLERANCES</h5>';
  op+='<p class="intol-list">';
    op+=engine.checkIntolerances();
  op+='</p>';
  if(appdata.userProfiles[appdata.state.profileNo]['ingredients']!=undefined){
    op+='<h5>PROFILE INGREDIENTS</h5>';
    op+='<p class="intol-list">';
      op+=engine.scanProfileIngedients(appdata.userProfiles[appdata.state.profileNo]['ingredients']);
      op+=engine.scanProfileIngedients(jsonData.title);
    op+='</p>';
  }

  op+='<h5>INGREDIENTS</h5>';
  op+='<p>';
    op+=jsonData['ingredients'];
  op+='</p>';
  op+='<br/><br/>';
  op+=engine.plotAll();
  if(showDetail){
    $('.detail-targ').html(op);
    $('.detailpop').show().fadeTo('fast',1);
    //page.route('scanner');
    engine.sliders();
    engine.checkAllergens();

  }
}
