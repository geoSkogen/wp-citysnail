var select_file = document.querySelector('#structure_file')
var locate_field = document.querySelector('#structure_path')

select_file.addEventListener('change', (event) => {
  var path = this.value;
  console.log("I'm your huckleberry: " + path)
})
