'use strict'
console.log('got unset all script')
var ins = document.querySelectorAll("input.citysnail");
var drop_button = document.querySelector("#drop_button");
var drop_field = document.querySelector("#drop_field");
drop_field.value = "FALSE";
drop_button.addEventListener('click', function () {
  drop_field.value = "TRUE";
  for (let i = 0; i < ins.length; i++) {
    ins[i].value = "";
    submit.click();
  }
});
