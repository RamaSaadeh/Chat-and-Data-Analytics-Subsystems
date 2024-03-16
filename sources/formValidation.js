// Password and Confirmed passwords validation
const pwd = document.querySelector(".formPassword");
const confirmPwd = document.querySelector(".confirmPassword");
const matchingTxt = document.querySelector(".matching-txt");
const form = document.querySelector(".form");
var insecurePwdAlert = document.getElementById("insecurePwdAlert");


function comparePwd() {
  console.log(confirmPwd.value);
  console.log(pwd.value);
  if (confirmPwd.value) {
    if (pwd.value != confirmPwd.value) {
      confirmPwd.classList.add("differentPassword");
      return false;
    } else {
      confirmPwd.classList.remove("differentPassword");
      return true;
    }
  }
}

//function to validate password
function validatePwd(){
//check if the password has at least 1 capital, number and special character and is at lest 8 characters long

}

//function checks that passwords match and that the password meets the requirements
//call this function onsubmit of register form
function compareValidatePwd(){
  if(comparePwd() && validatePwd()){
    return true;
  } else{
    return false;
  }
}

if (confirmPwd) {
  confirmPwd.addEventListener("keyup", () => {
    comparePwd();
  });
}

pwd.addEventListener("keydown", () => {
  comparePwd();
});
