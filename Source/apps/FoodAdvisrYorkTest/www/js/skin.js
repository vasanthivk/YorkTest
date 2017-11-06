skinObj=new Object();



skinObj.explode=function(inSkin){
  store.put('currentSkin',inSkin);
  var skinItems=inSkin.split('|');
  var c1=skinItems[1];
  var c2=skinItems[2];
  var title=skinItems[0];
  var logo=skinItems[3];
  skinObj.setSkin(c1,c2,title,logo);
}

skinObj.loadSkin=function(){
  api.call('getskin','',function(data){
    console.log(data);
    if(data==''){
        skinObj.explode(appSettings.defaultSkin);
    }else{
      skinObj.explode(data);
    }
  });

}

skinObj.setSkin=function(c1,c2,title,logo){
  $('.targ-title-change').text(title);
  $('.targ-title-replace').text(title);
  $.get('css/skin1.css',function(data){
    var data=data.replaceAll('757378',c2);
    skinObj.setCss('skin1a',data);
  });
  $.get('css/skin2.css',function(data){

    var data= data.replaceAll('00B2A9',c1);
    skinObj.setCss('skin2a',data);
  });
  $('.targ-logo-replace').attr('src',appSettings.logoPath+logo);


}

skinObj.setCss=function(fileName,data){
  $('<style>')
              .attr('type', 'text/css')
              .attr('ref', 'stylesheet')
              .attr('id', fileName)
              .text(data)
              .appendTo('head');

}
