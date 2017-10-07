function startFacebook(){
    openFB.init('1852454208358003');
    //login();
}

function login() {
        openFB.login('email',
                function() {
                    alert('Facebook login succeeded');
                },
                function() {
                    alert('Facebook login failed');
                });
    }

    function getInfo() {
          openFB.api({
              path: '/me',
              success: function(data) {
                  console.log(JSON.stringify(data));
                  document.getElementById("userName").innerHTML = data.name;
              },
              error: errorHandler});
      }

      function share() {
          openFB.api({
              method: 'POST',
              path: '/me/feed',
              params: {
                  message: 'Testing Facebook APIs'
              },
              success: function(data) {
                  alert('the item was posted on Facebook');
              },
              error: errorHandler});
      }

function startFacebookFull(){
  CordovaFacebook.login({
     permissions: ['email', 'user_likes'],
     onSuccess: function(result) {
       alert(2);
        if(result.declined.length > 0) {
           alert("The User declined something!");
        }
        /* ... */
     },
     onFailure: function(result) {
       alert(3);
       console.log('Facebook failure');
       console.log(result);
        if(result.cancelled) {
           alert("The user doesn't like my app");
        } else if(result.error) {
           alert("There was an error:" + result.errorLocalized);
        }
     }
  });
}
