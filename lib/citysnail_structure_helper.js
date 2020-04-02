console.log('structure-helper-script')
var monster_slugs = []
var monster_rows = document.querySelectorAll('monster_row')
var monster_inputs = document.querySelectorAll('monster_key')
var monster_drops = document.querySelectorAll('drop_me')

for (var i = 0; i < monster_rows.length; i++) {
  monster_drops[i].addEventListener('click', () => {
    var index = i
    drop_monster_row(index)
  })
  monster_slugs = monster_rows[i].querySelectorAll('td')
  monster_slugs.forEach( (slug) => {
    slug.addEventListener('click', () => {
      toggle_element(monster_keys[i])
    })
  })
}

function drop_monster_row(index) {
  console.log('drop row ' + index.toString())
}

function toggle_element(tag) {
  console.log('tag data toggle ' + tag.getAttribute('data-toggle'))
}
