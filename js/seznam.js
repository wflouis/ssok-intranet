

function setupTable(api, getRows, rowElementBase, getObjectFromRow, fillRowWithObject, formatRowEdit, deformatRowEdit){
  function getRowsDefault(order, orderDirection) {
    return fetch(
      api + 'get.php' +
      '?search=' + search.value +
      '&order=' + order +
      '&order-direction=' + orderDirection
    )
    .then(r => r.json())
  }
  function getObjectFromRowDefault(row){
    let elValues = row.querySelectorAll('[name]')
    let obj = {}
    elValues.forEach(e => {
      obj[e.getAttribute('name')] = e.innerText.trim()
    })
    return obj
  }
  function fillRowWithObjectDefault(row, obj){
    let elValues = row.querySelectorAll('[name]')
    for(let el of elValues){
      el.innerText = obj[el.getAttribute('name')]
    }
  }
  if(!getRows) getRows = getRowsDefault
  if(!getObjectFromRow) getObjectFromRow = getObjectFromRowDefault
  if(!fillRowWithObject) fillRowWithObject = fillRowWithObjectDefault

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

  let order
  let orderDir
  function getRowsDisplay(o, d){
    if(o) order = o
    if(d) orderDir = d
    getRows(order,orderDir).then(displayRows)
  }

  function rowElement(obj){
    let tr = document.createElement('tr')
    tr.innerHTML = rowElementBase(obj) + `
    <td class='action-default' contenteditable='false'>
      <a>Upravit</a> <a>Smazat</a>
    </td>
    <td class='action-edit' contenteditable='false'>
      <a>Uložit</a> <a>Zrušit</a>
    </td>
    <td class='action-create' contenteditable='false'>
      <a>Uložit</a> <a>Zrušit</a>
    </td>`

    let actionsDefault = tr.querySelector('.action-default')
    actionsDefault.children[0].onclick = (e) => displayEdit(e.target.parentNode.parentNode)
    actionsDefault.children[1].onclick = (e) => displayDelete(obj['id'], obj['name'], e.target.parentNode.parentNode)
    let actionsEdit = tr.querySelector('.action-edit')
    actionsEdit.children[0].onclick = (e) => saveEdit(obj['id'], e.target.parentNode.parentNode)
    actionsEdit.children[1].onclick = (e) => discardEdit(e.target.parentNode.parentNode)
    let actionsCreate = tr.querySelector('.action-create')
    actionsCreate.children[0].onclick = (e) => saveCreate(e.target.parentNode.parentNode)
    actionsCreate.children[1].onclick = (e) => discardCreate(e.target.parentNode.parentNode)

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
  if(btnCreateUser){
    btnCreateUser.onclick = displayCreateUser
    function displayCreateUser(){
      let row = rowElement({})
      row.classList.add('create')
      formatRowEdit(row)

      table.prepend(row)
    }
  }

  function saveCreate(row){
    apiPost(getObjectFromRow(deformatRowEdit(row)), getRowsDisplay, () => formatRowEdit(row))
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
      
      let order = c.getAttribute('column')
      getRowsDisplay(order, sort)
    }
  })
  columns[0].click()

  let searchTimeoutId = 0
  search.oninput = () => {
    clearTimeout(searchTimeoutId)
    searchTimeoutId = setTimeout(getRowsDisplay, 0)
  }
}