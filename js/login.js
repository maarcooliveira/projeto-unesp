FB.init({
      appId  : '165128130491436',
      status: true, cookie: true, xfbml: false
});

// statusFacebook();

function loginFacebook() {
      FB.login(function(response) {
         if (response.authResponse) {

          FB.api('/me', {fields: 'name,email,id' }, function(response) {
            console.log("from status");
            console.log(response);
            console.log(response.email);
          });

          window.location.href = "welcome.php";          
         }
      }, {scope: 'email' });
}

function statusFacebook() {
     FB.getLoginStatus(function(response) {

        if (response.status === 'connected') {

          FB.api('/me', {fields: 'name,email,id' }, function(response) {
            console.log("from status");
            console.log(response);
            console.log(response.email);
          });

          window.location.href = "welcome.php";
        } else if (response.status === 'not_authorized') {
            loginFacebook(); // nao autorizado, solicitar login
        } else {
            loginFacebook(); // nao autorizado, solicitar login
      }})
}


