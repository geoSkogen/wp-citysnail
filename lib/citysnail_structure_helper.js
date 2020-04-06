console.log('running wp citysnail structure helper script')
var citysnail_mypages = document.querySelector('#my_pages')
var monster_rows = document.querySelectorAll('.monster_row')
var monster_keys = document.querySelectorAll('.monster_key')
var monster_fields = document.querySelectorAll('.monster_field')
var monster_drops = document.querySelectorAll('.drop_me')
var monster_slugs = []
var index = -1
var mode = ''

var click_tasks = {
  toggle_str : 'invis',
  drop_str : 'drop',
  toggle_list : 'block,pause,invis',
  drop_list : 'drop,block,pause',
  'block' : function (element,index,mode) {
    console.log('leading ' + this.toggle_str + ' not found')
    monster_keys[element,index].className =
      monster_keys[index].className.replace(' ' + this.toggle_str,'')
    console.log(monster_keys[index].className)
    monster_fields[index].focus()
  },
  'pause' : function (element,index,mode) {
    monster_fields[index].focus()
  },
  'invis' : function (element,index,mode) {
    console.log('found leading ' + this.toggle_str)
    monster_keys[index].className += ' ' + this.toggle_str
    console.log(monster_keys[index].className)
    monster_fields[index].setAttribute('data','none')
  },
  'drop' : function (element,index) {
    function fade_out_row() {
      if (opacity_count < 0.05) {
        opacity_count = 0
        element.parentElement.style.display = "none"
        clearInterval(interval)
        element.parentElement.parentElement.removeChild(element.parentElement)
      } else {
        opacity_count = (opacity_count * 100 - 1)/100
        console.log('opacity count: ' +   opacity_count.toString())
        element.parentElement.style.opacity = opacity_count
      }
    }
    console.log('drop row ' + index.toString() + '?')
    var drop_mode = monster_fields[index].getAttribute('data')
    var opacity_count = 1
    var interval = null
    //alert('Drop this row?')
    if (drop_mode === this.drop_str) {
      console.log('row ' + index.toString()  + ' got dropped')
      element.parentElement.style.opacity = opacity_count
      interval = setInterval(fade_out_row, 5)
    } else {
      console.log('row ' + index.toString()  + ' got a warning')
      element.parentElement.className += ' ' + this.drop_str;
      monster_fields[index].setAttribute('data',this.drop_str)
    }
  },
  'toggle' : function (element,index) {
    var toggle_data = monster_keys[index].getAttribute('data-toggle').
      replace(this.drop_str,this.toggle_str).split(',')
    console.log('current toggle')
    console.log(toggle_data)
    var toggle_task = toggle_data.shift()
    var mode = monster_fields[index].getAttribute('data')
    element.parentElement.className =
      element.parentElement.className.replace(' ' + this.drop_str,'')
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

//update_mypages()
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
  assign_change(monster_fields[i],index)
  // NOTE - assign tab-key listener to toggle input open - open only!
}


function assign_clicks(task,element,index) {
  element.addEventListener('click', () => {
    console.log('got ' + task + ' click ' + index.toString() )
    if (click_tasks[task]) {
      click_tasks[task](element,index)
    }
  })
}

function assign_focus(mode,element,index) {
  element.addEventListener('focus', function () {
    console.log('got ' + mode + ' focus ' + index.toString() )
    focus_tasks[mode](element,index)
  })
}

function assign_change(element,index) {
  element.addEventListener('change', function () {
    console.log('got ' + ' x '  + 'onchange fuction ' + index.toString() )
    update_mypages()
  })
}

function update_mypages() {
  var assoc = {}
  var logs = {
    'found' : 0,
    'notfound' : 0
  }
  monster_fields.forEach( (field) => {
    if (field.value) {
      logs.found += 1
    } else {
      logs.notfound += 1
    }
    assoc[field.id] = field.value
  })
  citysnail_mypages.value = JSON.stringify(assoc)
  console.log(JSON.stringify(logs))
}
