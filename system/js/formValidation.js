function chkForm () {

 noError = true;
 errorMsg = "dieses Feld darf nicht leer sein";
 errorMsg1 = "Passwort muss mindestens 6 Zeichen besitzen";

for (var i =  1; i <= 5; i++) {
  //reset all errors
  document.getElementById([i]).innerHTML = "";
   if( document.getElementById("input"+[i]).value == "")
    {
      document.getElementById([i]).innerHTML = errorMsg;
      noError = false;
    }
}
  //if email input is filled check for @ symbol
  if (document.registerForm.email.value.indexOf("@") == -1 && document.getElementById([3]).innerHTML == "")
  {
    document.getElementById("3").innerHTML ="Keine gültige E-Mail-Adresse!";
    noError = false;
  }

for (var i =  4; i <= 5; i++) {

   if( document.getElementById("input"+[i]).value.length < 6 && document.getElementById([4]).innerHTML == "" && document.getElementById([5]).innerHTML == "")
    {
      document.getElementById([i]).innerHTML = errorMsg1;
      noError = false;
    }
}

if(document.registerForm.pw_control.value != document.registerForm.pw.value)
{
  document.getElementById("5").innerHTML = "Passwörter stimmen nicht überein!";
  noError = false;
}

return noError;
}
