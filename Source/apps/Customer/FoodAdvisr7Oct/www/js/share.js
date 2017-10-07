var share = new Object();


share.show=function(bcode,altcode){
  var data=barcodeStore[bcode];
  console.log(data);
  op='<div class="share-inner">';
    op+='<h4>Share this discovery</h4>';
    op+='<p class="share-handle-label act-share-handle-change"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;<span class="targ-share-handle"></span></p>'
    op+='<div class="share-gethandle">'
      op+='<p>What would you like your Foodadvisr name to be?</p>';
      op+='<input type="text" id="food-handle"/ placeholder="Enter you name or nickname">';
      op+='<a href="#" class="act-set-handle btn-create-new">Set Name</a>';
    op+='</div>';
    op+='<div class="share-main hide">';
    op+=engine.prodImage(altcode,data);


      op+='<h5>'+data.title+'</h5>';
      op+='<label>Share with Communities</label>';
      op+='<p id="share-groups">'+appdata.communities+'</p>';
      op+='<textarea id="share-comment" placeholder="Add any comments here..."></textarea>';
      op+='<a href="#" class="share-btn act-save-share" data-barcode="'+altcode+'">Share</a>';
    op+='</div>';
  op+='</div>';
  $('.detail-targ').html(op);
  if(store.get('user.handle')==''){
    $('.share-main').addClass('hide');
    $('.share-gethandle').removeClass('hide');
    $('.targ-share-handle').text('');
  }else{
    $('.share-main').removeClass('hide');
    $('.share-gethandle').addClass('hide');
    $('.targ-share-handle').text(store.get('user.handle'));
  }
  $('.detailpop').show().fadeTo('fast',1);
}

share.getHandle=function(){
  //used to get current user handle at startup ..keyed on their email address
  api.call('gethandle','',function(data){
    store.put('user.handle',data);
  });
}
