console.log('structure-helper-script')
var monster_slugs = []
var monster_rows = document.querySelectorAll('.monster_row')
var monster_keys = document.querySelectorAll('.monster_key')
var monster_fields = document.querySelectorAll('.monster_field')
var monster_drops = document.querySelectorAll('.drop_me')
var index = -1

var click_tasks = {
  toggle_str : 'invis',
  'drop' : function (element,index) {
    console.log('drop row ' + index.toString() )
  },
  'toggle' : function (element,index) {
     var slug = this.toggle_str
     var toggle_data = monster_keys[index].getAttribute('data-toggle').split(',')
     console.log('current toggle')
     console.log(toggle_data)
     //monster_keys[index].style.display = toggle_data[0]
     if (toggle_data[0]===this.toggle_str) {
       console.log('found leading ' + this.toggle_str)
       monster_keys[index].className += ' ' + this.toggle_str
       console.log(monster_keys[index].className)
     } else {
       console.log('leading ' + this.toggle_str + ' not found')
       monster_keys[index].className = monster_keys[index].className.replace(' ' + this.toggle_str,'')
       console.log(monster_keys[index].className)
     }
     toggle_data.reverse()
     console.log('new toggle')
     console.log(toggle_data)
     monster_keys[index].setAttribute('data-toggle',toggle_data.join(','))
     monster_keys[index].querySelector('input').focus()
  },
  'no_toggle' : function (element,index) {
    element.parentElement.setAttribute('data-toggle',list_str)
    element.parentElement.className = this.className.replace(' ' + this.toggle_str,'')
  }
}

for (var i = 0; i < monster_rows.length; i++) {
  index = i
  assign_clicks('drop',monster_drops[i],index)
  monster_slugs = monster_rows[i].querySelectorAll('td')
  monster_slugs.forEach( (slug) => {
    if (slug.className.indexOf('drop_me') === -1) {
      assign_clicks('toggle',slug,index)
    }
  })
  //assign_focus('block,invis',monster_fields[i],index)
  //assign_clicks('no_toggle',monster_fields[i],index)
}

function assign_clicks(task,element,index) {
  element.addEventListener('click', () => {
    console.log('got ' + task + ' click ' + index.toString() )
    click_tasks[task](element,index)
  })
}

function assign_focus(list_str,element,index) {
  element.addEventListener('focus', function () {
    this.parentElement.setAttribute('data-toggle',list_str)
    this.parentElement.className = this.parentElement.className.replace(' ' + click_tasks.toggle_str,'')
  })
}
