

// api
let api = 'api/seznam-strediska/'
let order = 'nazev'
let orderDirection = 'asc'

function getRows() {
  fetch(
    api + 'get.php' +
    '?search=' + search.value +
    '&order=' + order +
    '&order-direction=' + orderDirection
  )
  .then(r => r.json())
  .then(r => displayRows(r))
}

// dom manipulation
function rowElementBase(obj){ 
  return `
  <td name="zkratka">${obj['zkratka'] ?? ''}</td>
  <td name="nazev">${obj['name'] ?? ''}</td>
  <td name="ostatni">${stringifyOstatni(obj['ostatni'])}</td>
`
}
function parseOstatni(text){
  let ostatni = []
  for(let row of text.trim().split(/\r?\n/)){
    let split = row.trim().split(':')

    if(split[0] == '') continue

    let nadpis = split[0] ?? ''
    let text = split[1] ?? ''

    let jednoOstatni = {}
    jednoOstatni['nadpis'] = nadpis.trim() + ':'
    jednoOstatni['text'] = text.trim()

    ostatni.push(jednoOstatni)
  }
  return ostatni
}
function stringifyOstatni(ostatni){
  let ostatniStr = ''
  if(!ostatni) return ostatniStr

  for(let i = 0; i < ostatni.length; i++){
    ostatniStr += ostatni[i]['nadpis'] + ' ' + ostatni[i]['text']
    if(i != ostatniStr.length - 1) ostatniStr += "\n"
  }
  return ostatniStr
}
function getObjectFromRow(row){
  let elValues = row.querySelectorAll('[name]')
  let obj = {}
  elValues.forEach(e => {
    let attr = e.getAttribute('name')
    obj[attr] = (attr == 'ostatni' ? parseOstatni(e.innerText) : e.innerText.trim())
  })
  return obj
}
function fillRowWithObject(row, obj){
  obj['ostatni'] = stringifyOstatni(obj['ostatni'])
  let elValues = row.querySelectorAll('[name]')
  for(let el of elValues){
    el.innerText = obj[el.getAttribute('name')]
  }
}

// edit format
function formatRowEdit(row) {
  row.contentEditable = true
  return row
}
function deformatRowEdit(row){
  row.contentEditable = false
  return row
}