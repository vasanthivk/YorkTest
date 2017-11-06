
String.prototype.replaceAll=function(t,r){return this.split(t).join(r);};
var barcode = new Object();

barcode.pLeft=function(nr, n, str){
    return Array(n-String(nr).length+1).join(str||'0')+nr;
}

barcode.startScan=function(){
  $('.scan-btn-startpos').removeClass('scan-btn-startpos');
	cordova.plugins.barcodeScanner.scan(
      function (result) {
				setTimeout(function(){$('.scan-btn').removeClass('explode');overlay.hide();},50);
				if(!result.cancelled){
          if(result.text.substr(0,4)=='cmd:'){
              msg.show('Changing to custom Mode',1000);
              var skins=result.text.substr(4);
              skinObj.explode(skins);
          }else{
              setTimeout(function(){barcode.get(result.text)},70);
          }
				}
      },
      function (error) {
					setTimeout(function(){popup.show('Scanning failed '+error,'Ok')},200);
      }
	);
}

barcode.get=function(inCode){
  var listItems=$('.op1');
  console.log(listItems.length);
  if(listItems.length>9){
    msg.show('You have the maximum items in your list. Please remove one or click Nw Scan to start another comparison.',4000,true);
    return false;;
  }

  if(inCode=='' || inCode==undefined){
    return;
  }
	if(barcode.checkSpecial(inCode)){
		return;
	}
	msg.show('Finding barcode '+inCode,2000,true);
  lastCompare[inCode]=inCode;
  proddata.lastBarcode=inCode;
	navigator.geolocation.getCurrentPosition(gpsonSuccess, gpsonError);
  if(barcodecache[inCode]==undefined){
    barcode.getFromDB(inCode);

  }else{
    jsonData=barcodecache[inCode];
		jsonData.fromcache='yes';
    barcode.useData(jsonData,inCode);
  }
}

barcode.getFromDB=function(inCode,showDetail){
  var sliderVals=engine.sliderStream();
  parms=inCode+'/'+lastPosition+'/'+sliderVals+'/'+appdata.state.profileNo;
  api.call('barcode',parms,function(data){
      data=data.trim();
      if(data!='failed'){
        data=data.replace(/#/gi,'<b>');
        data=data.replace(/@/gi,'</b>');
        jsonData=JSON.parse(data);
        jsonData.useAWS=true;
        barcodecache[inCode]=jsonData;
        barcode.useData(jsonData,inCode);
        if((jsonData.calories==0 && jsonData.totalfat==0) || jsonData.ingredients==''){
        }
        if(jsonData.ingredients==''){
        }
      }else{
          msg.show("Sorry, this product isn't in our database.  We'll try to get it included in the future.  Thank you!",3000,false,true);
      }
  });
}

barcode.getFromDBDetail=function(inCode){
  var sliderVals=engine.sliderStream();
  parms=inCode+'/'+lastPosition+'/'+sliderVals+'/'+appdata.state.profileNo;
  console.log(parms);
  api.call('barcode',parms,function(data){
      data=data.trim();
      if(data!='failed'){
        data=data.replace(/#/gi,'<b>');
        data=data.replace(/@/gi,'</b>');
        jsonData=JSON.parse(data);
        jsonData.useAWS=true;
        barcodecache[inCode]=jsonData;
        engine.process(jsonData);
      }else{
          msg.show("Sorry, this product isn't in our database.  We'll try to get it included in the future.  Thank you!",3000,false,true);
      }
  });
}


barcode.checkSpecial=function(bcode){
	switch(bcode){
		case 'dlmenu':
			embedMenu.load();
			return true;
			break;
	}
	return false;

}

barcode.useData=function(jsonData,inCode){
  proddata.currentItem=jsonData;
  barcodeStore[inCode]=jsonData;
  engine.fullScore(jsonData);
  engine.drawPrecis(jsonData);

}

barcode.clear=function(){
  console.log('... Barcode.clear called...');
  lastCompare=new Object();
}

barcode.rerun=function(showPrecis){
    console.log('** START RE RUN ***');
    console.log(lastCompare);
    showPrecis=(showPrecis==undefined)?true:showPrecis;
    $('#list').html('');
    var idx=0;
    for(idx in lastCompare){
      jsonData=barcodeStore[lastCompare[idx]];
      proddata.currentItem=jsonData;
      engine.fullScore(jsonData);
      engine.drawPrecis(jsonData,showPrecis);
    }
}
