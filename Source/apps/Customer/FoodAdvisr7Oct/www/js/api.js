
api=new Object();
api.jwt='notset';
api.root=appSettings.apiPath;
api.eateiesroot=appSettings.apiEateriesPath;

api.url=function(action,parms){
  url=api.root+'/api/'+action;
  userRef=store.get('user:ref');
  if(userRef=='' || userRef==undefined){userRef='nouser'};
  url+='/'+userRef;
  url+='/'+parms;
  return url;
}

api.call=function(action,parms,callBack,postData){
  postData=(postData==undefined)?{}:postData;
  parms=(parms=='')?'nodata':parms;
  var jwt=store.get('userdata.jwt');
  jwt=(jwt=='')?'nokey':jwt;
  parms+='/'+api.cacheBust();
  parms+='/'+lastPosition
  url='/'+action;
  url+='/'+jwt;
  url+='/'+parms;
  url=api.addChecksum(url);
  url=api.root+url;
  fullAction=action+'/'+jwt+'/'+parms;

  if(connect.connected()){
      try{
        $.post(url,postData,function(data){
          callBack(data);
        }).fail(function(error){
          msg.show('Network error.',1000,false,true);
        });

      }catch(error){
        msg.show('Network error',1000,false,true);
      }
  }else{

    msg.show('No network',1000,false,true);
  }

}

api.addChecksum=function(inString){
  fullString= inString+'/||'+CryptoJS.MD5(inString+appSettings.hash);
  return fullString;
}

api.cacheBust=function(){
  var d=String(Math.random()*100000);
  d=d.substr(0,5);
  return d;

}

api.getEateries=function(callBack)
{
    postData = '{"latitude":"51.51634", "longitude": "-0.145576"}';
    $.post(api.eateiesroot+'gettop10hotels',postData,function(data){
      callBack(data);
    }).fail(function(error){
      msg.show('Network error.',1000,false,true);
    });
}

api.getAddClickBeforeAssociated=function(eateryId,callBack)
{
    postData = '{"id": ' + eateryId + '}';
    $.post(api.eateiesroot+'addclickbeforeassociated',postData,function(data){
      callBack(data);
    }).fail(function(error){
      msg.show('Network error.',1000,false,true);
    });
}

api.getAddClickAfterAssociated=function(eateryId,callBack)
{
    postData = '{"id": ' + eateryId + '}';
    $.post(api.eateiesroot+'addclickafterassociated',postData,function(data){
      callBack(data);
    }).fail(function(error){
      msg.show('Network error.',1000,false,true);
    });
}

