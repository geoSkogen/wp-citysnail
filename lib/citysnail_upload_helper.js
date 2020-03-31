var select_file = document.querySelector('#structure_file')
var locate_field = document.querySelector('#structure_path')
var button = (document.querySelector('#structure_button_unset'))?
  document.querySelector('#structure_button_unset') :
  document.querySelector('#structure_button')
  
button.addEventListener('click', (event) => {
  select_file.click();
})

select_file.addEventListener('change', (event) => {
  var path = this.value;
  console.log("I'm your huckleberry: " + path)
})
