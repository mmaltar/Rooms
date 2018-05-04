$(document).ready(function (){
    //micanje podataka iz localStorage kada korisnik klikne za logout
    $(".logout a").click(function(event) {
              localStorage.login = false;
              localStorage.removeItem("level");
              localStorage.removeItem("username");
      
     });
});