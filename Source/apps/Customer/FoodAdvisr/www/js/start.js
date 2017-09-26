document.addEventListener("deviceready", init, false);
var mySwiper={};


function init() {
	$('.account-email').text(store.get('userdata.email'));
	$('.account-handle').text(store.get('user.handle'));
	$(function() {
    FastClick.attach(document.body);
	});
	StatusBar.hide();
	wd=window.device;
	appdata.device=wd.platform+':'+wd.version+':'+wd.model+':'+wd.manufacturer;
	$('.show-version').html(appSettings.version);




myProfile.person={};

myProfile.person.settings=1;

	if(store.get('userdata.userid')==''){
		const userid=uid.create(30);
		userdata.userid=userid;
		userdata.email=userid;
		store.put('userdata.userid',userid);
		store.put('userdata.email',userid);
	}else{
			userdata.userid=store.get('userdata.userid');
			userdata.email=userdata.userid;
	}


	var parms=userdata.email+'/'+appSettings.version+'/'+appdata.device;
	api.call('getjwt',parms,function(data){
		api.jwt=data;
		store.put('userdata.jwt',api.jwt);
		store.put('userdata.logintime',Date.now());
		msg.show('Logged in ',2000);
		$('.loggedin').removeClass('loggedin');
		processLogIn();
		skinObj.loadSkin();
	});





	//if(store.get('userdata.jwt')!='' && store.get('userdata.jwt')!=undefined){
	//	secondsDiff=(Date.now()-parseFloat(store.get('userdata.logintime')))/(60*60);
	//	daysDiff=secondsDiff/86400;
	//	if(daysDiff<20){
	//		processLogIn();
	//		$('.loggedin').removeClass('loggedin');
	//	}else{
	//		msg.show('error 001');
	//	}
	//}else{
	//	msg.show('error 002');
	//}




	$('.startupwhite').fadeTo('slow',0,function(){
		$(this).remove();
	});
	$(".navLink").on('click', function (e) {
	    e.preventDefault();
			window.open($(this).data('url'), '_blank', 'location=yes');
	});



skinObj.loadSkin();
	appdata.user.allergens=store.get('appdata.user.allergens');
	navigator.geolocation.getCurrentPosition(gpsonSuccess, gpsonError);
	appdata.loadallergens();
	appdata.loadprofiles();
	appdata.loadCommunities();




	if(appSettings.showEating){
		$('.targ-mode-option').removeClass('hide');
		appStatus.setWorkingMode(store.get('appStatus:workingMode'),false,false);
	}


	share.getHandle();





	//appdata.loadBarcodeCache('loadcachetest');

	//embedMenu.load();


  bindAll();
	//var media = new Media('buzz.wav', mediaSuccess, mediaError);
	//media.play();
	//setTimeout(function(){media.pause()},50);
	$('.fave-default').trigger('click');



	var currentSkin=store.get('currentSkin');
	if(currentSkin==''){
		currentSkin=appSettings.defaultSkin;
		store.put('currentSkin',currentSkin);
	}


	skinObj.explode(currentSkin);



}//end of init function

mediaSuccess=function(data){
}

mediaError=function(data){
}

gpsonSuccess=function(data){
		coords=data.coords;
		lastPosition=coords.latitude+':'+coords.longitude;
}

gpsonError=function(data){
}

function processLogIn(){

	if(store.get('appdata.userProfiles')!=''){
		userProf=store.get('appdata.userProfiles');
		profData=JSON.parse(store.get('appdata.userProfiles'));
		appdata.userProfiles=profData;
	}else{
		appdata.userProfiles={};
		appdata.userProfiles[1]={name:'General1',calories:0,totalfat:0,saturatedfat:0,salt:0,sugar:0,protein:0,carbs:0,fibre:0,cholesterol:0,ingredients:''};
		for(tt=2;tt<=15;tt++){
				appdata.userProfiles[tt]={name:'#DELETED#'};
		}
		appdata.shortcuts={};
		appdata.shortcuts[1]={ref:1,name:'General'};
		appdata.state.profileNo=1;
		appdata.state.profileName='General';
	}
		appdata.loadShortcuts();
		appdata.loadIntolerancePrefs();
		//if(appsignup.get()=='completed'){
				appsignup.show();
				appdata.showShortcuts();
		//}else{
				//$('.signup-signin').slideUp('fast');
		//}
		share.getHandle();


}
