
let search = document.getElementById('search')
let table = document.getElementById('table-body')
let btnCreateUser = document.getElementById('new-button')

let errorMessage = 'Akce se nezdařila.'
function alertError(r, code){
  // alert(errorMessage + ' Kód chyby: ' + code)
  r.text().then(
    t => alert(errorMessage + ' ' + t)
  )
}

Object.prototype.clearCh = function() {
  while(this.firstChild) this.firstChild.remove()
  return this
}

function rowElement(obj){
  let tr = document.createElement('tr')
  tr.innerHTML = rowElementBase(obj) + `
  <td class='action-default' contenteditable='false'><a onclick='displayEdit(this.parentNode.parentNode)'>Upravit</a> <a onclick='displayDelete(${obj['id']}, "${obj['name']}", this.parentNode.parentNode)'>Smazat</a></td>
  <td class='action-edit' contenteditable='false'><a onclick='saveEdit(${obj['id']}, this.parentNode.parentNode)'>Uložit</a> <a onclick='discardEdit(this.parentNode.parentNode)'>Zrušit</a></td>
  <td class='action-create' contenteditable='false'><a onclick='saveCreate(this.parentNode.parentNode)'>Uložit</a> <a onclick='discardCreate(this.parentNode.parentNode)'>Zrušit</a></td>`
  return tr
}

//api
function apiDelete(id, cb){
  fetch(api + 'delete.php?id=' + id)
  .then(r => {
    if(r.status == 200) {
      cb()
    }
    else alertError(r, 'delete')
  })
}
function apiPost(obj, cb, cbError){
  fetch(api + 'post.php', {
    method:'post',
    body:JSON.stringify(obj)
  })
  .then(r => {
    if(r.status == 200) {
      cb()
      alert('"'+ (obj['jmeno'] ?? obj['nazev']) +'" byl vytvořen')
    }
    else {
      cbError()
      alertError(r, 'post')
    }
  })
}
function apiPostEdit(obj, cb, cbError){
  fetch(api + 'edit.php', {
    method:'post',
    body:JSON.stringify(obj)
  })
  .then(r => {
    if(r.status == 200) {
      cb()
    }
    else {
      cbError()
      alertError(r, 'edit')
    }
  })
}

// modification
btnCreateUser.onclick = displayCreateUser
function displayCreateUser(){
  let row = rowElement({})
  row.classList.add('create')
  formatRowEdit(row)

  table.prepend(row)
}

function saveCreate(row){
  apiPost(getObjectFromRow(deformatRowEdit(row)), getRows, () => formatRowEdit(row))
}
function discardCreate(row){
  row.remove()
}
function displayDelete(id, name, row){
  if(window.confirm('Smazat "' + name + '"')){
    apiDelete(id, () => row.remove())
  }
}
function displayEdit(row){
  row.classList.add('edit')
  row.setAttribute('oldobj', JSON.stringify(getObjectFromRow(row)))
  formatRowEdit(row)
}
function saveEdit(id, row){
  row.classList.remove('edit')
  let obj = getObjectFromRow(deformatRowEdit(row))
  obj['id'] = id
  apiPostEdit(obj, () => {}, () => formatRowEdit(row))
}
function discardEdit(row){
  row.classList.remove('edit')
  let oldobj = JSON.parse(row.getAttribute('oldobj'))
  deformatRowEdit(row)
  fillRowWithObject(row, oldobj)
}

// display
function displayRows(objs){
  table.clearCh()
  for(let obj of objs){
    table.appendChild(rowElement(obj))
  }
}

// sort
let columns = document.querySelectorAll('thead td:not([nosort])')
columns.forEach(c => {
  c.onclick = () => {
    let sort = c.classList.contains('sort-asc') ? 'desc' : 'asc'

    columns.forEach(c => c.className='')
    c.classList.add('sort-' + sort)
    
    order = c.getAttribute('column')
    orderDirection = sort
    getRows()
  }
})
columns[0].click()

let searchTimeoutId = 0
search.oninput = () => {
  clearTimeout(searchTimeoutId)
  searchTimeoutId = setTimeout(getRows, 0)
}
