$(document).ready(function (){
    $("#loginForm").submit(function(event) {

      event.preventDefault();
      var $form = $( this ), url = $form.attr( 'action' );

      var posting = $.post( url, { logusername: $('#username').val(), logpass: $('#password').val() }, "json" );
      /* Alerts the results */
      posting.done(function( data ) {
          if(data.success === true || data.success === "true"){  
              localStorage.login = true;
              localStorage.level = data.data.level;
              localStorage.username = data.data.username;
              window.location.href = "index.php?rt=rooms";
              
          }
          else{
              alert(data.message);
              localStorage.login = false;
              localStorage.removeItem("level");
              localStorage.removeItem("username");
              location.reload();
          }
      });
     });
});
