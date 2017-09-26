


var body=$('body');


function startLogin(){
  body.on('click','.act-login-signup',function(){
    obb={};
    obb['email']=$('#email:visible').val();
    obb['password']=$('#password:visible').val();
    obb['password1']=$('#password:visible').val();
    url=api.url('signup',obb['email']+'/'+obb['password']);
    $.post(url,function(data){alert(data)});
    return false;
  });

}
