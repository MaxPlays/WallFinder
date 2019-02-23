$("#login-submit").click(function(){

  var user = $("#login-user").val();
  var pass = $("#login-password").val();
  var stay = $("#login-stay").prop("checked");

  $("#login-loading").show();
  $("#login-text").hide();

  var element = $(this).parent();

  $.post("php/login.php", {user: user, pass: pass, stay: stay}).done(function(data){
    if(data.includes("Incorrect")){
      $("#login-fail-text").html(data);
      $("#login-fail").modal("show");
    }else{
      if(data != "Success"){
        document.cookie = "rememberme=" + data;
      }
      location.reload();
    }
  }).always(function(){
    $("#login-loading").hide();
    $("#login-text").show();
  }).fail(function(){
    $("#login-fail").modal("show");
    $("#login-fail-text").html("An error occurred");
  });

});

$("#login-logout").click(function(){
  document.cookie = "rememberme=; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
});
