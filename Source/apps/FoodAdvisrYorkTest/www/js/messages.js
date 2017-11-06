var messagesClass=new Object();

messagesClass.store={};


messagesClass.fetch=function(){
  if(connect.connected()){
    api.call('messages','',function(data){
      console.log(data);
      messagesClass.store=JSON.parse(data);
      $('.disclaimer').html(messagesClass.store['disclaimer']);
      store.put('messagesClass.store',data);
      console.log(messagesClass.store);
    });
  }else{
    let ss=store.get('messagesClass.store');
    messagesClass.store=JSON.parse(ss);
  }

}

messagesClass.getMessage=function(title){
  let ss=store.get('messagesClass.store');
  messagesClass.store=JSON.parse(ss);  console.log('*** getMessage ***');

  console.log(messagesClass);
  console.log(messagesClass.store);
  console.log(this.store);
  console.log(messagesClass.store[title]);
  if(messagesClass.store[title]!=undefined){
    return messagesClass.store[title]['message'];
  }else{
    return '';
  }
}
