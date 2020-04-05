console.log('structure-helper-script')
var monster_slugs = []
var monster_rows = document.querySelectorAll('.monster_row')
var monster_keys = document.querySelectorAll('.monster_key')
var monster_fields = document.querySelectorAll('.monster_field')
var monster_drops = document.querySelectorAll('.drop_me')
var index = -1
var mode = ''
var click_tasks = {
  toggle_str : 'invis',
  toggle_list : 'block,pause,invis',
  'block' : function (element,index,mode) {
    console.log('leading ' + this.toggle_str + ' not found')
    monster_keys[element,index].className =
      monster_keys[index].className.replace(' ' + this.toggle_str,'')
    console.log(monster_keys[index].className)
    monster_keys[index].querySelector('input').focus()
  },
  'pause' : function (element,index,mode) {
    monster_keys[index].querySelector('input').focus()
  },
  'invis' : function (element,index,mode) {
    console.log('found leading ' + this.toggle_str)
    monster_keys[index].className += ' ' + this.toggle_str
    console.log(monster_keys[index].className)
    monster_keys[index].querySelector('input').setAttribute('data','none')
  },
  'drop' : function (element,index) {
    console.log('drop row ' + index.toString() )
  },
  'toggle' : function (element,index) {
    var toggle_data = monster_keys[index].getAttribute('data-toggle').split(',')
    console.log('current toggle')
    console.log(toggle_data)
    var toggle_task = toggle_data.shift()
    var mode = monster_keys[index].querySelector('input').getAttribute('data')

    this[toggle_task](element,index,mode)
    toggle_data.push(toggle_task)
    console.log('new toggle')
    console.log(toggle_data)
    monster_keys[index].setAttribute('data-toggle',toggle_data.join(','))
  },
  'no_toggle' : function (element,index) {
    element.parentElement.setAttribute('data-toggle',this.toggle_list)
    element.parentElement.className =
      element.parentElement.className.replace(' ' + this.toggle_str,'')
    console.log('current toggle')
    console.log(this.toggle_list)
  }
}

var focus_tasks = {
  'none' : function (element,index) {
    element.setAttribute('data','auto')
    console.log('new focus mode: ' + element.getAttribute('data'))
  },
  'auto' : function (element,index) {
    element.setAttribute('data','manual')
    console.log('new focus mode: ' + element.getAttribute('data'))
  },
  'manual' : function (element,index) {
    console.log('new focus mode: ' + element.getAttribute('data'))
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
  mode = monster_fields[i].getAttribute('data')
  assign_focus(mode,monster_fields[i],index)
  assign_clicks('no_toggle',monster_fields[i],index)
}


function assign_clicks(task,element,index) {
  element.addEventListener('click', () => {
    console.log('got ' + task + ' click ' + index.toString() )
    click_tasks[task](element,index)
  })
}

function assign_focus(mode,element,index) {
  element.addEventListener('focus', function () {
    console.log('got ' + mode + ' focus ' + index.toString() )
    focus_tasks[mode](element,index)
  })
}
