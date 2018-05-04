$(document).ready(function (){
    $("#registerForm").submit(function(event) {

      /* stop form from submitting normally */
      event.preventDefault();

      /* get the action attribute from the <form action=""> element */
      var $form = $( this ),
          url = $form.attr( 'action' );

      /* Send the data using post with element id name and name2*/
      var posting = $.post( url, { username: $('#username').val(), pass: $('#password').val(), mail: $('#mail').val() } );

      /* Alerts the results */
      posting.done(function( data ) {
        alert(data);
      });
     });
});
