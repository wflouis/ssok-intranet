
Object.prototype.clearCh = function() {
  while(this.firstChild) this.firstChild.remove()
  return this
}

let errorMessage = 'Akce se nezdařila.'
function alertError(r, code){
  // alert(errorMessage + ' Kód chyby: ' + code)
  r.text().then(
    t => alert(errorMessage + ' ' + t)
  )
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
  fillRowObj(row, exclude){
    let elValues = row.querySelectorAll('td[name]')
  
    if(!row.obj) row.obj = {}

    elValues.forEach(e => {
      if(exclude && exclude.includes(e.getAttribute('name'))) return

      row.obj[e.getAttribute('name')] = e.innerText.trim()
    })
    return row.obj
  }

  formatRowEditBase(row){
    row.contentEditable = true
    
    if(this.formatRowEdit) this.formatRowEdit(row, row.cols)

    return row
  }
  deformatRowEditBase(row, save, obj){
    if(!obj) {
      this.fillRowObj(row, this.editFormattedColumns)
      obj = row.obj
    }

    if(this.deformatRowEdit) this.deformatRowEdit(row, row.cols, save)

    let index = this.removeRow(row)
    this.insertRow(obj, index)
  }
  removeRow(row){
    let index = Array.from(this.table.children).indexOf(row)
    row.remove()
    return index
  }

  constructor(api, editFormattedColumns){
    this.api = api;
    this.editFormattedColumns = editFormattedColumns ?? []
    
    this.getRows = this.getRowsDefault

    this.search = document.getElementById('search')
    this.search.value = window.location.href.split('?search=')[1] ?? ''
    this.table = document.getElementById('table-body')
    this.table.spellcheck = false
    
    let btnCreate = document.getElementById('new-button')
    if(btnCreate){
      let displayCreateNew = () => {
        let row = this.rowElement({})
        row.classList.add('create')
        
        row = this.insertRow(row, 0)
        this.formatRowEditBase(row)
      }

      btnCreate.onclick = displayCreateNew
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
    tr.obj = obj

    let actionsDefault = tr.querySelectorAll('.action-default a')
    actionsDefault[0].onclick = (e) => this.displayEdit(e.target.parentNode.parentNode)
    actionsDefault[1].onclick = (e) => this.displayDelete(obj, e.target.parentNode.parentNode)
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
      else alertError(r, 'delete')
    })
  }
  apiPost(row, cb, cbError){
    let formData = row.formData
    if(!formData) formData = new FormData()
    
    formData.append('obj', JSON.stringify(row.obj))
    
    fetch(api + 'post.php', {
      method:'post',
      body:formData
    })
    .then(r => {
      if(r.status == 200) {
        cb()
        alert('Záznam ' + Object.values(obj)[1] + ' byl vytvořen')
      }
      else {
        if(cbError) cbError()
        alertError(r, 'post')
      }
    })
  }
  apiPostEdit(row, cb, cbError){
    let formData = row.formData
    if(!formData) formData = new FormData()
    
    formData.append('obj', JSON.stringify(row.obj))

    console.log(formData.getAll('prilohy[]'))

    fetch(api + 'edit.php', {
      method:'post',
      body:formData
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
  saveCreate(row){
    this.deformatRowEditBase(row, true)
    this.apiPost(row, this.getRowsDisplay, () => this.formatRowEditBase(row))
  }
  discardCreate(row){
    row.remove()
  }
  getObjName(obj){
    let name = 'záznam'
    if(obj['jmeno']) name = '"' + obj['jmeno'] + '"'
    else if(obj['nazev']) name = '"' + obj['nazev'] + '"'
    else if(obj['cislo']) name = '"' + obj['cislo'] + '"'
    
    return name
  }
  displayDelete(obj, row){
    if(window.confirm('Smazat ' + this.getObjName(obj))){
      this.apiDelete(id, () => row.remove())
    }
  }
  displayEdit(row){
    row.classList.add('edit')
    row.oldobj = structuredClone(row.obj)

    this.formatRowEditBase(row)
  }
  saveEdit(id, row){
    row.classList.remove('edit')
    this.deformatRowEditBase(row, true)
    row.obj['id'] = id
    this.apiPostEdit(row, () => {}, () => this.formatRowEditBase(row))
  }
  discardEdit(row){
    row.classList.remove('edit')

    this.deformatRowEditBase(row, false, row.oldobj)
  }

  // display
  displayRows = (objs) => {
    this.table.clearCh()
    for(let obj of objs){
      this.insertRow(obj)
    }
  }
  insertRow(obj, index){
    let tr = this.rowElement(obj)
    tr.obj = obj
    tr.cols = this.fetchColumns(tr)
    this.table.insertBefore(tr, this.table.children[index ?? 0]);

    if(this.rowCallback) this.rowCallback(tr, obj)

    return tr
  }
}

function selectMultiple(col, arr, optionsApi, valueName, textName){
  col.clearCh().innerHTML = `
  <div class='select-selected'></div>
  <select id='select'>
    <option value=''>Vyberte</option>
  </select>
  `
  let selected = col.querySelector('.select-selected')
  let strSelect = col.querySelector('#select')

  fetch(optionsApi)
  .then(r => {
    if(r.status != 200) alertError(r)
    return r.json()
  })
  .then(r => {
    for(let s of r){
      strSelect.insertAdjacentHTML('beforeend', `<option value='${s[valueName]}'>${s[textName]}</option>`)
    }
  })

  function selectStredisko(value, text) {
    let selectedObj = {}
    selectedObj[valueName] = value
    selectedObj[textName] = text

    let selectedRow = document.createElement('div')
    selectedRow.classList.add('flex')
    selectedRow.innerHTML = `
    ${text}
    <div class='sgap gap-stretch-h'></div>
    <a>Smazat</a>
    `
    arr.push(selectedObj)

    selectedRow.querySelector('a').onclick = () => {
      let index = arr.indexOf(selectedObj)
      arr.splice(index, 1);
      selectedRow.remove()
    }

    selected.appendChild(selectedRow)
    strSelect.value = ''
  }

  strSelect.onchange = () => {
    let option = strSelect.options[strSelect.selectedIndex]
    selectStredisko(option.value, option.text)
  }

  let arrClone = structuredClone(arr)
  arr.length = 0 // clear the array
  for(let obj of arrClone){
    selectStredisko(obj[valueName], obj[textName])
  }
}