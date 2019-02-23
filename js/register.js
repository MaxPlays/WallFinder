$("#register-submit").click(function(){
  clearAlerts();
  var email = $("#register-email").val();
  var user = $("#register-user").val();
  var pass = $("#register-password").val();

  var element = $(this).parent().parent().children()[1];
  if(!validateEmail(email)){
    createAlert(element, "The email address you entered is not valid")
    return;
  }
  if(user.length < 3){
    createAlert(element, "The username must be at least 3 characters long")
    return;
  }
  if(user.length > 20){
    createAlert(element, "The username must be shorter than 20 characters")
    return;
  }
  if(!validateUser(user)){
    createAlert(element, "The username can only contain letters and numbers")
    return;
  }
  if(pass != $("#register-password-repeat").val()){
    createAlert(element, "The passwords are not equal")
    return;
  }
  if(pass.length < 6){
    createAlert(element, "The password must be at least 6 characters long")
    return;
  }
  if(pass.length > 25){
    createAlert(element, "The password can only be 25 characters long")
    return;
  }

  $("#register-loading").show();
  $("#register-text").hide();

  $.post("php/register.php", {email: email, user: user, pass: pass}).done(function(data){
    if(data == "Success"){
      $("#register-modal").modal("hide");
      $("#register-success").modal("show");
    }else{
      createAlert(element, data);
    }
  }).always(function(){
    $("#register-loading").hide();
    $("#register-text").show();
  }).fail(function(){
    createAlert(element, "An error occurred");
  });

});
