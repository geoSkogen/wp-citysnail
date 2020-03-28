var sitemap_items = document.querySelectorAll('.sitemap_item')
var schema_text = document.querySelector('#report_schema').innerText
var modal = document.querySelector('#snail_modal')
var close_modal = document.querySelector('#close_modal')

function toggle_modal(rules) {
  var data_toggle = modal.getAttribute('data-toggle')
  if (rules.indexOf(data_toggle)) {
    modal.style.display = rules[rules.indexOf(data_toggle)]
    modal.setAttribute('data-toggle',rules[0])
  }
}

function write_report(obj) {
  return
}

function lookup_report(dom_obj) {
  var href = dom_obj.querySelector('a').href
  var data = JSON.parse(document.querySelector('#report_schema').innerText)
  return data[href]
}

sitemap_items.forEach( (item) => {
  item.addEventListener('click', () => {
    var content = lookup_report(this)
    write_report(content)
    toggle_modal(['none','block'])
  })
})

close_modal.addEventListener('click', () => {
  toggle_modal(['block','none'])
})
