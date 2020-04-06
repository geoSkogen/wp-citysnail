'use strict'
console.log('running sitemap nester js')
var icon_base_class = 'toggle_icon'
var toggle_icon = 'caret'
console.log('calling init sitemap nester')

init_sitemap_nester()

function toggle_child_pages(parent_slug, tier) {
  var next_gen_tier_str = (Number(tier)+1).toString()
  var sitemap_items = document.querySelectorAll('.sitemap_item')
  var r_margin_val = ((Number(tier)+1)*50).toString() + 'px'
  var children = []
  var compstyle = {}
  var display_str = ''
  var toggle_val = ''
  sitemap_items.forEach( (e) => {
    if (e.className.indexOf(parent_slug) > -1) {
      if (e.className.indexOf('tier_' + next_gen_tier_str) > -1) {
          children.push(e)
      }
    }
  })
  children.forEach( (child) => {
    compstyle = window.getComputedStyle(child)
    display_str = compstyle.getPropertyValue('display')
    toggle_val = child.getAttribute('data_toggle')
    child.style.display = toggle_val
    child.setAttribute('data_toggle', display_str)
    child.style.margin = '0 0 0 ' + r_margin_val
  })
}

function toggle_icon() {

}

function init_sitemap_nester() {
  var icons = document.querySelectorAll('.' + icon_base_class)
  icons.forEach( (icon) => {
    icon.addEventListener('click', function () {
      var i_tag = this.querySelector('.fas')
      var toggle_val = i_tag.getAttribute('data_toggle')
      var old_toggle_pos = i_tag.className.indexOf(toggle_icon)+toggle_icon.length+1
      var old_toggle_val = i_tag.className.slice(old_toggle_pos, this.className.length)
      var new_class_name = i_tag.className.replace(old_toggle_val,toggle_val)
      var parent_slug = this.className.slice(icon_base_class.length+1, this.className.indexOf(' tier'))
      var tier = this.className.slice(this.className.indexOf(' tier_')+6, this.className.length)
      i_tag.className = new_class_name
      i_tag.setAttribute('data_toggle', old_toggle_val)
      toggle_child_pages(parent_slug, tier)
      console.log(parent_slug)
      console.log(tier)
    })
  })
}
