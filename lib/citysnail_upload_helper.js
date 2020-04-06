console.log('running upload helper js')

var select_file = document.querySelector('#structure_file')
var locate_field = document.querySelector('#structure_path')
var button = (document.querySelector('#structure_button_unset'))?
  document.querySelector('#structure_button_unset') :
  document.querySelector('#structure_button')
var path_is_set = (locate_field.className.indexOf('slight') > -1) ? true : false;

button.addEventListener('click', (event) => {
  select_file.click();
})

locate_field.addEventListener('focus', function () {
  if (path_is_set) {
    this.style.opacity = 1;
  }
})
locate_field.addEventListener('blur', function () {
  if (path_is_set) {
    this.style.opacity = 0.33;
  }
})
