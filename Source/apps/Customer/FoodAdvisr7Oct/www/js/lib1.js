var retail=new Object();

retail.space=function(){
	var items=['waitrose','sainsburys','coop','asda','morrisons','','',''];
	var op='<div class="help-text" data-href="21">21</div>';
	op+='<div class="logo-list">';
		for(idx in items){
			op+='<div class="logo-item act-logo-clicked" data-store="'+util.titleCase(items[idx])+'">';
			op+='<div class="logo-item-content">';
			if(items[idx]!=''){
				op+='<img src="simg/'+items[idx]+'.png" alt=""/>';
			}else{
				op+='';
			}
			op+='</div>';
			op+='</div>';
		}
	op+='</div>';
	return op;
}

var eating=new Object();

eating.space=function(){
	//var items=['dlicious','aspers','bellaitalia','papajohns','premierinn','timhortons','revolution','yosushi'];
	var items=['','','','','','','',''];
	var op='<div class="help-text" data-href="22">22</div>';
	op+='<div class="logo-list">';
		for(idx in items){
			op+='<div class="logo-item act-logo-clicked" data-store="'+util.titleCase(items[idx])+'">';
			if(items[idx]!=''){
				op+='<img src="rimg/'+items[idx]+'.png" alt=""/>';
			}else{
				op+='<img src="img/comingsoon.svg" alt=""/ class="comingsoon">';
			}
			op+='</div>';
		}
	op+='</div>';
	return op;

}

var terms=new Object();

terms.target='';

terms.check=function(target){
	if(store.get('user.terms')!='accepted'){
		$('.accountpop').css({left:'-60%'});
		overlay.hide();
		var tt=$('.signup-terms');
		tt.removeClass('hide');
		const termsText="Sorry for pausing your first use of FoodAdvisr but we need you to confirm our T&c’s first. Once you have accepted you will returned to this screen and we will not ask for this again.<br/><br/>";

		appdata.showTerms('#signup-terms',termsText);
		terms.target=target;
		return false;
	}else{
		return true;
	}
}

terms.trigger=function(){
		$(terms.target).trigger('click');
}



var shortcuts = new Object();

shortcuts.clear=function(){
	for(var idx in appdata.shortcuts){
			appdata.shortcuts[idx]['ref']=-1;
			appdata.shortcuts[idx]['name']='...';
	}
}

shortcuts.update=function(){
	shortcuts.clear();
	var shortIdx=1;
	var toggleList=$('.targ-fave-select.active');
	toggleList.each(function(){
		t=$(this);
		ref=t.data('ref');
		name=t.parent().text();
		if(appdata.shortcuts[shortIdx]==undefined){
				appdata.shortcuts[shortIdx]={};
		}
		appdata.shortcuts[shortIdx]['ref']=ref;
		appdata.shortcuts[shortIdx]['name']=name;
		shortIdx++;
	});
	if(shortIdx>=4){
		$('.targ-fave-select').addClass('faded');
		$('.targ-fave-select.active').removeClass('faded');
	}else{
			$('.targ-fave-select').removeClass('faded');
	}
	appdata.showShortcuts();
	appdata.saveShortcuts();
}

var profileTools=new Object();

profileTools.clear=function(){
		$('.targ-fave-select.active').removeClass('active');
}

profileTools.update=function(){

}

profileTools.saveCurrent=function(){

}

profileTools.loadCurrent=function(){
		var dd=store.get('appdata.state');
		if(dd!=''){
			appdata.state=JSON.parse(dd);
		}else{
			profileTools.set(appSettings.defaultProfile);
		}
		$('.active-profile').text(appdata.state.profileName);
		$('.active-profile-btn').text(appdata.state.profileName);
		//$('.targ-profile-title').html('<h3>'+appdata.state.profileName+'</h3>');
}

profileTools.select=function(s){
	t=s.parent();
	profileId=t.data('key');
	profileTools.set(profileId);
}

profileTools.set=function(profileId){
	appdata.state.profileNo=profileId;
	name=appdata.userProfiles[profileId]['name'];
	appdata.state.profileNo=profileId;
	appdata.state.profileName=name;
	store.put('appdata.state',JSON.stringify(appdata.state));
	$('.active-profile').text(name);
	newslide.drawSliders();
	console.log(appdata.userProfiles);

}







var allergens=new Object();

allergens.save=function(t){
	if(t==undefined){
			targ=$('#allergenlist:visible');
	}else{
			targ=t.parent().parent();
	}
	op='';

	targ.find('.toggle').each(function(){
		t=$(this);
		if(t.hasClass('active')){
			op+=t.data('ref')+',';
		}
	});
	appdata.user.allergens=op;
	store.put('appdata.user.allergens',op);
}




var picker=new Object();

picker.start=function(title,xclass,noTitle){
	noTitle=(noTitle==undefined)?false:noTitle;
	op= '<div class="settings">';
	if(!noTitle){
		op+='<h5 class="act-settings-section">'+title+'<span class="icon icon-down"></span></h5>';
	}

	return op;
}

picker.startIntol=function(title,intolerance){
	op= '<div class="intolerance-lists">';
	op+='<h5 class="act-settings-section" data-intolerance="'+intolerance+'">'+title+'<i class="fa fa-chevron-down" aria-hidden="true"></i></h5>';

	return op;
}


picker.startList=function(xclass){
	xclass=(xclass==undefined)? '' : xclass;
	return '<ul class="table-view '+xclass+'">';
}

picker.stopList=function(){
	return '</ul>';
}

picker.add=function(title,status,targetList,xclass){
	if(targetList==undefined){
		targetList='';
	}
	targetList=','+targetList+',';
	xclass=(xclass==undefined)? '' : xclass;
	tclass='';
	if(targetList.indexOf(','+title)!=-1){
		tclass='active';
	}
	tclass+=' '+xclass;
	op='<li class="table-view-cell alist">';
	op+=title;
	op+='<div class="toggle '+tclass+'" data-ref="'+title+'">';
	op+='<div class="toggle-handle"></div>';
	op+='</div>';
	op+='</li>';
	return op;
}

picker.addIntol=function(title,targetList,xclass){
	if(targetList==undefined){
		targetList='';
	}
	targetList=','+targetList+',';
	xclass=(xclass==undefined)? '' : xclass;
	tclass='';
	if(targetList.indexOf(','+title)!=-1){
		tclass='active';
	}
	tclass+=' '+xclass;
	op='<li class="table-view-cell alist">';
	op+=title;
	op+='<div class="toggle '+tclass+'" data-ref="'+title+'">';
	op+='<div class="toggle-handle"></div>';
	op+='</div>';
	op+='</li>';
	return op;
}

picker.close=function(){
	return '</div>';
}

var connect=new Object();

connect.forceOffline=false;

connect.connected=function(){
	var networkState = navigator.connection.type;
	if(networkState==Connection.NONE){
		return false;
	}else{
		if(connect.forceOffline){
			return false;
		}else{
			return true;
		}
	}

}

connect.message=function(state){
	var states = {};
	states[Connection.UNKNOWN]  = 'Unknown connection';
	states[Connection.ETHERNET] = 'Ethernet connection';
	states[Connection.WIFI]     = 'WiFi connection';
	states[Connection.CELL_2G]  = 'Cell 2G connection';
	states[Connection.CELL_3G]  = 'Cell 3G connection';
	states[Connection.CELL_4G]  = 'Cell 4G connection';
	states[Connection.CELL]     = 'Cell generic connection';
	states[Connection.NONE]     = 'No network connection';
		return states[state];
}

iconlist = new Object();

iconlist.icon='icon-compose';

iconlist.setIcon=function(icon){
	iconlist.icon=icon;
}

iconlist.item=function(title,xclass,key,pos){
	var pclass='';
	xclass=(xclass==undefined)? '' : xclass;
	pos=(pos==undefined)? '' : pos;
	op='<li class="table-view-cell media '+xclass+'" data-key="'+key+'">';
			if(key<30){
				op+='<span class="media-object pull-left targ-add-del"></span>';
			}
			op+='<div class="media-body act-profile-title">';
				op+=title;
				//op+=' <small>'+key+'</small>';
			op+='</div>';
			if(pos!=-1){
				pclass='active';
			}
		op+='<div class="toggle targ-fave-select '+pclass+'" data-ref="'+key+'">';
			op+='<div class="toggle-handle"></div>';
		op+='</div>';
	op+='</li>';
	return op;
}

iconlist.itemDownload=function(item,xclass){
	title=item.name;
	description=item.description;
	xclass=(xclass==undefined)? '' : xclass;
	//pos=(pos==undefined)? '' : pos;
	op='<li class="table-view-cell media '+xclass+'" data-ref="'+item.ref+'" data-desc="'+description+'">';
			op+='<span class="media-object pull-left icon '+iconlist.icon+'"></span>';
			op+='<div class="media-body">';
				op+=title;
			op+='</div>';
	op+='</li>';
	return op;
}
