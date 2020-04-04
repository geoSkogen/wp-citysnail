console.log('structure-helper-script')
var monster_slugs = []
var monster_rows = document.querySelectorAll('.monster_row')
var monster_inputs = document.querySelectorAll('.monster_key')
var monster_fields = document.querySelectorAll('.monster_field')
var monster_drops = document.querySelectorAll('.drop_me')
var index = -1

var click_tasks = {
  'drop' : function (element,index) {
    console.log('drop row ' + index.toString() )
  },
  'toggle' : function (element,index) {
     var slug = 'invis'
     var toggle_data = monster_inputs[index].getAttribute('data-toggle').split(',')
     console.log('current toggle')
     console.log(toggle_data)
     //monster_inputs[index].style.display = toggle_data[0]
     if (toggle_data[0]===slug) {
       console.log('found leading ' + slug)
       monster_inputs[index].className += ' ' + slug
       console.log(monster_inputs[index].className)
     } else {
       console.log('leading ' + slug + ' not found')
       monster_inputs[index].className = monster_inputs[index].className.replace(' ' + slug,'')
       console.log(monster_inputs[index].className)
     }
     toggle_data.reverse()
     console.log('new toggle')
     console.log(toggle_data)
     monster_inputs[index].setAttribute('data-toggle',toggle_data.join(','))
     monster_inputs[index].querySelector('input').focus()
  },
  'no_toggle' : function (element,index) {
    element.parentElement.parentElement.setAttribute('data-toggle',list_str)
    element.parentElement.parentElement.className = this.className.replace(' invis','')
  }
}

for (var i = 0; i < monster_rows.length; i++) {
  index = i
  assign_clicks('drop',monster_drops[i],index)
  monster_slugs = monster_rows[i].querySelectorAll('td')
  monster_slugs.forEach( (slug) => {
    if (slug.className.indexOf('drop_me') === -1) {
      assign_clicks('toggle',slug, index)
    }
  })
  assign_focus('block,invis',monster_fields[i],index)
  assign_clicks('no_toggle',monster_fields[i],index)
}

function assign_clicks(task,element,index) {
  element.addEventListener('click', () => {
    console.log('got ' + task + ' click ' + index.toString() )
    click_tasks[task](element,index)
  })
}

function assign_focus(list_str,element,index) {
  element.addEventListener('focus', function () {
    this.setAttribute('data-toggle',list_str)
    this.className = this.className.replace(' invis','')
  })
}
