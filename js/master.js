var id = 0;

function createAlert(element, text, timed){
  var v = '<div class="alert alert-danger alert-dismissible fade show" role="alert" id="alert-' + id + '">' + text + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
  var i = id;
  if(timed){
    setTimeout(function(){
      $("#alert-" + i).remove();
    }, 5000);
  }
  id++;
  $(element).prepend(v);
}

function createAlertHeader(element, header, text, timed){
  createAlert(element, "<strong>" + header + "</strong> " + text, timed);
}

function clearAlerts(){
  $(".alert-danger").remove();
}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function validateUser(user){
  var re = /^[A-Za-z0-9]+$/;
  return re.test(String(user).toLowerCase());
}
