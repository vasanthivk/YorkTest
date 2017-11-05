
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

api.getEateries=function(latitude, longitude,searchval,cuisines_ids,lifestyle_choices_ids, callBack)
{
    //postData = '{"latitude":"51.51634", "longitude": "-0.145576"}';    gettop10eateries    geteateries
    postData = '{"latitude":"'+ latitude +'", "longitude": "' + longitude + '","cuisines_ids":"'+cuisines_ids+'", "lifestyle_choices_ids":"'+lifestyle_choices_ids+'"}';
    $.post(api.eateiesroot+'geteateries',postData,function(data){
      callBack(data);
    }).fail(function(error){
      closeLoading();
      msg.show('Network error.',1000,false,true);
    });
}

api.getCuisines=function(callBack)
{
    
    $.get(api.eateiesroot+'getcuisines',function(data){
      
      callBack(data);
      
    }).fail(function(error){
      msg.show('Network error.',1000,false,true);
    });
}

api.getLifeStyleChoices=function(callBack)
{
    
    $.get(api.eateiesroot+'getlifestylechoices',function(data){
      
      callBack(data);
      
    }).fail(function(error){
      msg.show('Network error.',1000,false,true);
    });
}

api.getUserFavouriteEateries=function(callBack)
{
    postData = '{"userid": ' + userdata.userid + '}';
    $.post(api.eateiesroot+'getfavouriteeateries',postData,function(data){
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

api.getEateryDetails=function(eateryid, callBack)
{
    postData = '{"id":"'+ eateryid+ '"}';
    $.post(api.eateiesroot+'geteaterydetailsbyid',postData,function(data){
      callBack(data);
    }).fail(function(error){
      msg.show('Network error.',1000,false,true);
    });
}

api.getDishDetails=function(dishid, callBack)
{
    postData = '{"id":"'+ dishid+ '"}';
    $.post(api.eateiesroot+'getdishdetailsbyid',postData,function(data){
      callBack(data);
    }).fail(function(error){
      msg.show('Network error.',1000,false,true);
    });
}

api.getFavouriteEateries=function(callBack)
{
    //postData = '{"userid":"'+ userdata.email + '"}';
    postData = '{"userid":"1"}';
    $.post(api.eateiesroot+'getfavouriteeateries',postData,function(data){
      callBack(data);
    }).fail(function(error){
      msg.show('Network error.',1000,false,true);
    });
}

api.addToFavouriteEatery=function(eateryid,callBack)
{
    //postData = '{"userid":"'+ userdata.email + '","eatery_id" : "' + eateryid + '"}';
    postData = '{"userid":"1","eatery_id" : "' + eateryid + '"}';
    $.post(api.eateiesroot+'addtofavouriteeatery',postData,function(data){
      callBack(data);
    }).fail(function(error){
      msg.show('Network error.',1000,false,true);
    });
}

api.removeFromFavouriteEatery=function(eateryid,callBack)
{
    //postData = '{"userid":"'+ userdata.email + '","eatery_id" : "' + eateryid + '"}';
    postData = '{"userid":"1","eatery_id" : "' + eateryid + '"}';
    $.post(api.eateiesroot+'removefromfavouriteeatery',postData,function(data){
      callBack(data);
    }).fail(function(error){
      msg.show('Network error.',1000,false,true);
    });
}

api.addFeedbackEateries=function(eateryId,rating,message,callBack)
{
    postData = '{"userid": "' + userdata.userid + '","eatery_id":"'+ eateryId +'","rating":"'+ rating + '","message":"'+message + '","email":"'+userdata.email+'","msgdate":'+'"'+Date.now()+'"'+',"response":'+'""'+',"resptime":'+'""'+',"version":'+'"'+device.version+'"'+',"device":'+'"'+device.getInfo()+'"'+',"os":'+'"'+device.platform+'"'+',"osversion":'+'""'+',"model":'+'"'+device.model+'"'+',"maker":'+'"'+device.manufacturer+'"'+'}';
    $.post(api.eateiesroot+'addfeedbackeateries',postData,function(data){
        callBack(data);
    }).fail(function(error){
        msg.show('Network error.',1000,false,true);
    });
}
