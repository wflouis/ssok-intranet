
Object.prototype.clearCh = function() {
  while(this.firstChild) this.firstChild.remove()
  return this
}

class MTable {
  getRowsDefault(order, orderDirection) {
    return fetch(
      this.api + 'get.php' +
      '?search=' + (this.search ? this.search.value : '') +
      '&order=' + order +
      '&order-direction=' + orderDirection
    )
    .then(r => r.json())
  }

  fetchColumns(row){
    let elValues = row.querySelectorAll('[name]')
    let obj = {}
    elValues.forEach(e => {
      obj[e.getAttribute('name')] = e
    })
    return obj
  }
  getObjectFromRowDefault(row){
    let elValues = row.querySelectorAll('[name]')
    let obj = {}
    elValues.forEach(e => {
      obj[e.getAttribute('name')] = e.innerText.trim()
    })
    return obj
  }
  fillRowWithObjectDefault(row, obj){
    let elValues = row.querySelectorAll('[name]')
    for(let el of elValues){
      el.innerText = obj[el.getAttribute('name')]
    }
  }
  formatRowEditDefault(row) {
    row.contentEditable = true
    return row
  }
  deformatRowEditDefault(row){
    row.contentEditable = false
    return row
  }

  constructor(api, getRows, rowElementBase, getObjectFromRow, fillRowWithObject, formatRowEdit, deformatRowEdit){
    this.api = api;
    
    if(!getRows) getRows = this.getRowsDefault
    if(!getObjectFromRow) getObjectFromRow = this.getObjectFromRowDefault
    if(!fillRowWithObject) fillRowWithObject = this.fillRowWithObjectDefault
    if(!formatRowEdit) formatRowEdit = this.formatRowEditDefault
    if(!deformatRowEdit) deformatRowEdit = this.deformatRowEditDefault
    this.getRows = getRows
    this.getObjectFromRow = getObjectFromRow
    this.fillRowWithObject = fillRowWithObject
    this.rowElementBase = rowElementBase
    this.formatRowEdit = formatRowEdit
    this.deformatRowEdit = deformatRowEdit

    this.search = document.getElementById('search')
    this.table = document.getElementById('table-body')
    this.errorMessage = 'Akce se nezdařila.'
    
    let btnCreateUser = document.getElementById('new-button')
    if(btnCreateUser){
      let displayCreateUser = () => {
        let row = this.rowElement({})
        row.classList.add('create')
        this.formatRowEdit(row)
        
        this.table.prepend(row)
      }

      btnCreateUser.onclick = displayCreateUser
    }

    // sort
    let columns = document.querySelectorAll('thead td:not([nosort])')
    let sortTimeoutId = 0
    columns.forEach(c => {
      c.onclick = () => {
        let sort = c.classList.contains('sort-asc') ? 'desc' : 'asc'

        columns.forEach(c => c.className='')
        c.classList.add('sort-' + sort)
        
        let order = c.getAttribute('column')
        this.order = order
        this.orderDir = sort

        clearTimeout(sortTimeoutId)
        sortTimeoutId = setTimeout(this.getRowsDisplay, sortTimeoutId == 0 ? 0 : 500)
      }
    })
    this.columns = columns
    columns[0].click()

    if(this.search){
      let searchTimeoutId = 0
      search.oninput = () => {
        clearTimeout(searchTimeoutId)
        searchTimeoutId = setTimeout(this.getRowsDisplay, 500)
      }
    }
  }
  
  alertError(r, code){
    // alert(errorMessage + ' Kód chyby: ' + code)
    r.text().then(
      t => alert(this.errorMessage + ' ' + t)
    )
  }

  getRowsDisplay = () => {
    this.getRows(this.order,this.orderDir).then(this.displayRows)
  }

  rowElement(obj){
    let tr = document.createElement('tr')
    tr.innerHTML = rowElementBase(obj) + `
    <td class='action-default' contenteditable='false'>
      <a>Upravit</a><br><a>Smazat</a>
    </td>
    <td class='action-edit' contenteditable='false'>
      <a>Uložit</a><br><a>Zrušit</a>
    </td>
    <td class='action-create' contenteditable='false'>
      <a>Uložit</a><br><a>Zrušit</a>
    </td>`

    let actionsDefault = tr.querySelectorAll('.action-default a')
    actionsDefault[0].onclick = (e) => this.displayEdit(e.target.parentNode.parentNode)
    actionsDefault[1].onclick = (e) => this.displayDelete(obj['id'], obj['name'], e.target.parentNode.parentNode)
    let actionsEdit = tr.querySelectorAll('.action-edit a')
    actionsEdit[0].onclick = (e) => this.saveEdit(obj['id'], e.target.parentNode.parentNode)
    actionsEdit[1].onclick = (e) => this.discardEdit(e.target.parentNode.parentNode)
    let actionsCreate = tr.querySelectorAll('.action-create a')
    actionsCreate[0].onclick = (e) => this.saveCreate(e.target.parentNode.parentNode)
    actionsCreate[1].onclick = (e) => this.discardCreate(e.target.parentNode.parentNode)

    return tr
  }

  //api
  apiDelete(id, cb){
    fetch(api + 'delete.php?id=' + id)
    .then(r => {
      if(r.status == 200) {
        cb()
      }
      else this.alertError(r, 'delete')
    })
  }
  apiPost(obj, cb, cbError){
    fetch(api + 'post.php', {
      method:'post',
      body:JSON.stringify(obj)
    })
    .then(r => {
      if(r.status == 200) {
        cb()
        let name = 'Záznam'
        if(obj['jmeno']) name = `"${obj['jmeno']}"`
        if(obj['nazev']) name = `"${obj['nazev']}"`
        
        alert(name + ' byl vytvořen')
      }
      else {
        if(cbError) cbError()
        alertError(r, 'post')
      }
    })
  }
  apiPostEdit(obj, cb, cbError){
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
        this.alertError(r, 'edit')
      }
    })
  }

  // modification
  saveCreate(row){
    this.apiPost(this.getObjectFromRow(this.deformatRowEdit(row)), this.getRowsDisplay, () => this.formatRowEdit(row))
  }
  discardCreate(row){
    row.remove()
  }
  displayDelete(id, name, row){
    if(window.confirm('Smazat "' + name + '"')){
      this.apiDelete(id, () => row.remove())
    }
  }
  displayEdit(row){
    row.classList.add('edit')
    row.oldobj = this.getObjectFromRow(row)
    this.formatRowEdit(row)
  }
  saveEdit(id, row){
    row.classList.remove('edit')
    let obj = this.getObjectFromRow(this.deformatRowEdit(row))
    obj['id'] = id
    this.apiPostEdit(obj, () => {}, () => this.formatRowEdit(row))
  }
  discardEdit(row){
    row.classList.remove('edit')
    this.deformatRowEdit(row)
    this.fillRowWithObject(row, row.oldobj)
  }

  // display
  displayRows = (objs) => {
    this.table.clearCh()
    for(let obj of objs){
      let tr = this.rowElement(obj)
      this.table.appendChild(tr)

      if(this.rowCallback) this.rowCallback(tr, obj)
    }
  }
}