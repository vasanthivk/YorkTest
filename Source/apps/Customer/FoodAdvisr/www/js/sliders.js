
var newslide = new Object();

newslide.showSlide=function(slideName,reverse){
	reverse=false;
		slideMax=100;
		slideMin=0;
	var slideData=appdata.slider[slideName];
	var profileData=appdata.userProfiles[appdata.state.profileNo];
	val=profileData[slideName];
	val=(val==undefined)?0:val;
	var pip=100/slideData.max;
	slideData.pip=pip;
	var slideVal=pip*val;
	var below=slideData.below*pip;
	var mid=slideData.mid*pip;
	var above=slideData.above*pip;

	var current=newslide.get(slideName,'current');
	current=(current=='')?0:current;

	op='<div class="slide-rail">';
		//op+='<div class="slide-pip pip-below" style="left:'+below+'%"></div>';
		//op+='<div class="slide-pip pip-mid" style="left:'+mid+'%"></div>';
		//op+='<div class="slide-pip pip-above" style="left:'+above+'%"></div>';
		op+='<h6>'+slideData.label+' <small></small></h6>';
		op+='<input disabled=true data-name="'+slideName+'" data-pip="'+pip+'" class="newslider" id="slider-'+slideName.replace(' ','')+'" type="range" value="'+slideVal+'" min="'+slideMin+'" max="'+slideMax+'"/><br/>';
	op+='</div>';
	return op;
}

newslide.getSlideData=function(slideName){
	return appdata.slider[slideName];
}



newslide.saveAll=function(){
	$('.newslider').each(function(){
		t=$(this);
		name=t.data('name');
		pip=parseFloat(t.data('pip'));
		val=t.val();
		realVal=val/pip;
		appdata.userProfiles[appdata.state.profileNo][name]=realVal;
	});
	newslide.updateStore();
	test=JSON.parse(store.get('appdata.userProfiles'));
}

newslide.updateStore=function(){
		store.put('appdata.userProfiles',JSON.stringify(appdata.userProfiles));
}

newslide.get=function(slideName,item){
	try {
	    return appdata.slider[slideName][item];
	}
	catch(err) {
	    return '';
	}
}

newslide.update=function(t){
	var ppp=t.parent();
	var name=t.data('name');
	var max=newslide.get(name,'max');
	var pp=t.prev();
	var targ=pp.find('small');
	var pip=parseFloat(t.data('pip'));
	var slideVal=t.val();
  if(name=='fibre' || name=='protein'){
    slideVal=100-slideVal;
  }
	if(slideVal<0){
		slideVal=0-slideVal;
	}
	var realVal=slideVal/pip;
	realVal=realVal.toFixed(1);
	var color=newslide.color(name,realVal);
	var unit=newslide.get(name,'unit');
	targ.html(realVal+unit);
	targ.removeClass('pip-red pip-orange pip-yellow pip-green');
	targ.addClass(color);

	ppp.removeClass('redpipw orangepipw yellowpipw greenpipw');
	var shortc=color.substr(4);
	console.log(shortc);
	ppp.addClass(shortc+'pipw');




}


newslide.color=function(slideName,cval){
	if(slideName=='fibre' || slideName=='protein'){
		return newslide.colorReverse(slideName,cval);
	}else{
		dd=appdata.slider[slideName];
		if(cval>dd.above){
			return 'pip-red';//red
		}
		if(cval>dd.mid){
			return 'pip-orange';//orange
		}
		if(cval>dd.below){
			return 'pip-yellow';//yellow

		}
		return 'pip-green';//green

	}
}

newslide.colorReverse=function(slideName,cval){
	dd=appdata.slider[slideName];
	if(cval>dd.above){
		return 'pip-green';//red
	}
	if(cval>dd.mid){
		return 'pip-yellow';//orange
	}
	if(cval>dd.below){
		return 'pip-orange';//yellow

	}
	return 'pip-red';//green
}


newslide.show=function(title,max,start,low,high){
	low=(low==undefined) ? '' : low;
	high=(high==undefined) ? '' : high;
	op='<h6>'+title+'</h6>';
	op+='<input type="range" value="0" min="0" max="100"/><br/>';
	return op;
}

newslide.showAll=function(){
	op='';
	if(appdata.userProfiles[appdata.state.profileNo]!=undefined){
		name=appdata.userProfiles[appdata.state.profileNo]['name'];
	}else{
		name='Profile';
	}
	//op='<div class="targ-profile-title act-restore-title"><h3 class="">'+name+'</h3></div>';
	op+='<div class="tabs act-profile-tabs">';
			op+='<a href="#subtab-sliders" class="selected subby subby-profile hide">This Profile</a>';
			op+='<a href="#subtab-profiles" class="subby-all subby hide">Profile List</a>';
	op+='</div>';

	//op+='<div class="prof-tool-panel">';
//		op+='<a href="#" class="act-create-new btn-tool-left">+</a>';
//		op+='<a href="#" class="act-profile-edit-start btn-tool-right">-</a>';
//	op+='</div>';



	op+='<div class="subtabs profiletabs">';
		op+='<div class="subtab helpable" id="subtab-sliders">';
			op+='<div class="help-text" data-href="08">08 Help for slider sub panel</div>';
			op+='<div class="targ-slider-list"></div>';
		op+='</div>';
		op+='<div class="subtab helpable hide" id="subtab-profiles">';
		//op+='<div class="prof-tool-panel">';
			//op+='<a href="#" class="act-create-new btn-tool-left">+</a>';
			//op+='<a href="#" class="act-profile-edit-start btn-tool-right">-</a>';
		//op+='</div>';

			op+='<div class="help-text" data-href="09">09 Help for profiles subpanel</div>';
		//op+='<h3>Profiles</h3>';
			op+='<ul id="targ-profiles-list" class="table-view targ-download-list" id="targ-download-list"></ul>';
		op+='</div>';

	op+='</div>';
	$('.targ-myprofiles').html(op);
	newslide.drawSliders();


}

newslide.capitalise=function(string){
	    return string.charAt(0).toUpperCase() + string.slice(1);
}


newslide.drawSliders=function(){
	var key=appdata.state.profileNo;
	var profileData=appdata.userProfiles[key];
	op='<h6 class="h6-profile-head"><small>Profile Settings for</small><br/>'+profileData.name+'</h6>';
	op+='<div class="act-prof-subsub prof-subsub">Excluded Ingredients <i class="fa fa-chevron-down" aria-hidden="true"></i></div>';
	op+='<div class="prof-subsub-panel prof-subsub-ingredients">';
	op+='<p>These ingredients are excluded using this profile</p>'

	console.log(profileData);
	greyed='';
ingredients=appdata.userProfiles[appdata.state.profileNo]['ingredients'];
	if(ingredients!=undefined){
		var temp=ingredients.replace(/,/gi,', ');
		var expld=temp.split(',');
		var ingredients='';
		for(idx in expld){
			var item=newslide.capitalise(expld[idx]);
			ingredients+=item+'<br/>';
		}

		//op+='<div class="profile-ingredients"><h6>Ingredients excluded using <br/><b>'+profileData.name+'</b></h6>'+ingredients+'</div>';
		op+='<div class="profile-ingredients">'+ingredients+'</div>';

	}

	op+='</div>';
	op+='<div class="act-prof-subsub prof-subsub">Nutritional Settings <i class="fa fa-chevron-down" aria-hidden="true"></i></div>';
	op+='<div class="prof-subsub-panel prof-subsub-sliders">';


	op+='<div class="slider-outer-wrap '+greyed+'">';

	op+=newslide.showSlide('calories');
	op+=newslide.showSlide('totalfat');
	op+=newslide.showSlide('saturatedfat');
	op+=newslide.showSlide('salt');
	op+=newslide.showSlide('sugar');
	op+=newslide.showSlide('protein',true);
	op+=newslide.showSlide('carbs');
	op+=newslide.showSlide('fibre',true);
	op+=newslide.showSlide('cholesterol');

	op+='</div>';
	op+='</div>';
	$('.targ-slider-list').html(op);
	$('.newslider').each(function(){
		newslide.update($(this));
	});
}


newslide.showHelp=function(){
	if(store.get('appdata.user.firstslide')==''){
		store.put('appdata.user.firstslide','shown');
		help=$('.help-overlay-small');
		help.css({top:'20vh'});
		help.fadeTo('fast',1);

	}
}
