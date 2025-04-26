
Object.prototype.clearCh = function() {
  while(this.firstChild) this.firstChild.remove()
  return this
}

function parentHasClass(el, className) {
  return (el.classList && el.classList.contains(className)) || (el.parentNode && parentHasClass(el.parentNode, className))
}
function parentHasId(el, id) {
  return (el.id && el.id == id) || (el.parentNode && parentHasId(el.parentNode, id))
}
function parentHasTag(el, tag) {
  return (el.tagName == tag) || (el.parentNode && parentHasTag(el.parentNode, tag))
}

let errorMessage = 'Akce se nezdařila.'
function alertError(r, code){
  // alert(errorMessage + ' Kód chyby: ' + code)
  r.text().then(
    t => alert(errorMessage + ' ' + t)
  )
}

class MTable {
  getRowsDefault(order, orderDirection, limit) {
    return fetch(
      this.api + 'get.php' +
      '?search=' + (this.search ? this.search.value : '') +
      '&order=' + order +
      '&order-direction=' + orderDirection +
      (limit ? '&limit=' + limit : '')
    )
    .then(r => r.json())
  }

  getRowsExport(order, orderDirection, limit) {
    return fetch(
      this.api + 'export.php' +
      '?search=' + (this.search ? this.search.value : '') +
      '&order=' + order +
      '&order-direction=' + orderDirection +
      (limit ? '&limit=' + limit : '')
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

  // replaces cells with edit inputs and dynamically updates row.obj
  formatRowEditBase(row){
    row.contentEditable = true

    if(this.formatRowEdit) this.formatRowEdit(row, row.cols)

    return row
  }
  // retrieve remaining static edited values to row.obj
  deformatRowEditBase(row, save, obj){
    if(!obj) {
      this.fillRowObj(row, this.editFormattedColumns)
      obj = row.obj
    }

    if(this.deformatRowEdit) this.deformatRowEdit(row, row.cols, save)
  }
  resetRow(row, obj){
    if(!obj) {
      obj = row.obj
    }

    let index = this.removeRow(row)
    return this.insertObjRow(obj, false, index)
  }
  removeRow(row){
    let index = Array.from(this.tableBody.children).indexOf(row)
    row.remove()
    return index
  }

  constructor(api, editFormattedColumns, tableBody){
    this.api = api
    this.editFormattedColumns = editFormattedColumns ?? []

    this.getRows = this.getRowsDefault

    this.tableBody = tableBody ?? document.getElementById('table-body')
    this.tableBody.spellcheck = false
    this.table = this.tableBody.parentNode

    // remove thead column akce
    if(!writePermission){
      let akce = this.table.querySelector('thead td:last-child')
      if(akce.innerHTML.trim().toLowerCase() == 'akce'){
        akce.remove()
      }
    }

    // sort
    let columns = this.table.querySelectorAll('thead td:not([nosort])')
    let sortTimeoutId = 0
    columns.forEach((c, i) => {
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
      if(i == 0) c.onclick()
    })
    this.columns = columns
  }

  setTitle(title){
    let el = document.createElement('span')
    el.classList.add('table-title')
    el.innerHTML = title

    this.tableBody.parentNode.insertAdjacentElement('beforebegin', el)
  }
  setNewButton(name){
    if(!writePermission) return

    let btnNew = document.createElement('a')
    btnNew.classList.add('icon', 'table-new-icon')
    btnNew.title = 'Vytvořit nový záznam'

    btnNew.onclick = this.displayCreate

    this.tableBody.parentNode.insertAdjacentElement('beforebegin', btnNew)
  }
  setSearch(){
    let search = document.createElement('input')
    search.classList.add('txt')
    search.placeholder = 'Hledat...'

    search.value = window.location.href.split('?search=')[1] ?? ''
    let searchTimeoutId = 0
    search.oninput = () => {
      clearTimeout(searchTimeoutId)
      searchTimeoutId = setTimeout(this.getRowsDisplay, 500)
      window.history.pushState(null, '', window.location.pathname + '?search=' + search.value)
    }

    this.tableBody.parentNode.insertAdjacentElement('beforebegin', search)
    search.focus()

    this.search = search
  }

  setExport(columns){
    if(!columns) columns = this.columns

    let exportEl = document.createElement('a')
    exportEl.innerText = 'Export CSV (max 5000 řádků)'

    exportEl.onclick = async () => {
      let loading = loadingScreen()

      let rows = await this.getRowsExport(this.order, this.orderDir, 5000)
/*
      let csv = ''
      
      if(rows.length > 0){
        for(var data in rows[0]){
          if (data !== 'clearCh') {
              csv += data + ';'
          }
        }
      }

      csv += '\n'

      for(let row of rows){
        for(var property in row){
          if (property !== 'clearCh') {
            let text = row[property]
            csv += text + ';'
          }
        }
        csv += '\n'
      }

      let fileName = (this.search.value.trim() == '' ? 'smlouvy' : this.search.value.trim()) + '.csv'

*/
      let csv = '<table><thead>'
      
      let cols = []
  
      if(rows.length > 0){
        for(var data in rows[0]){
          if (data !== 'clearCh') {
              csv += '<td>' + data + '</td>'
          }
        }
      }

      csv += '</thead>'

      for(let row of rows){
        csv += '<tr>'
        for(var property in row){
          if (property !== 'clearCh') {
            let text = ''
            if (row[property] !== null) {
              text = row[property]
            }
            csv += '<td>' + text + '</td>'
          }
        }
        csv += '</tr>'
      }
    
      csv += '</table>'

      let fileName = (this.search.value.trim() == '' ? 'smlouvy' : this.search.value.trim()) + '.xls'

      let blob = new Blob([csv], {type: "text/html"});
      let file = window.URL.createObjectURL(blob);
      let a = document.createElement('a')
			a.setAttribute("download", fileName)
			a.href = file
			a.target = '_blank'
			a.click()

      loading.remove()
    }
    this.tableBody.parentNode.insertAdjacentElement('beforebegin', exportEl)
  }

  setExportOld(columns){
    if(!columns) columns = this.columns

    let exportEl = document.createElement('a')
    exportEl.innerText = 'Export CSV (max 5000 řádků)'
  
    exportEl.onclick = async () => {
      let loading = loadingScreen()

      let rows = await this.getRows(this.order, this.orderDir, 5000)

      let csv = '<table><tr>'
      let cols = []
      for(let i = 0; i < columns.length; i++){
        let name = columns[i].getAttribute('title') ?? columns[i].innerText
        let columnName = columns[i].getAttribute('column')
        csv += '<td>' + name + '</td>'
        cols.push(columnName)
        // if(i < columns.length - 1) csv += ';'
      }
      csv += '</tr>'
      for(let row of rows){
        csv += '<tr>'
        for(let j = 0; j < cols.length; j++){
          let data = row[cols[j]]
          let cellContent = ''

          if(cols[j] == 'partneri'){
            for(let i = 0; i < data.length; i++){
              cellContent += data[i].nazev + '<br/>'
            }
          }
          else{
            cellContent = data.trim().replace(/\s\s+/g, ' ')
          }
          csv += '<td>' + cellContent + '</td>'
          // if(j < cols.length - 1) csv += ';'
        }
        csv += '</tr>'
        // csv += '\n'
      }
      csv += '</table>'

      let fileName = (this.search.value.trim() == '' ? 'smlouvy' : this.search.value.trim()) + '.xls'

      let blob = new Blob([csv], {type: "text/html"});
      let file = window.URL.createObjectURL(blob);
      let a = document.createElement('a')
			a.setAttribute("download", fileName)
			a.href = file
			a.target = '_blank'
			a.click()

      loading.remove()
    }
    this.tableBody.parentNode.insertAdjacentElement('beforebegin', exportEl)
  }

  getRowsDisplay = () => {
    this.getRows(this.order,this.orderDir).then(this.displayRows)
  }

  rowElement(obj, create){
    let row = document.createElement('tr')
    row.innerHTML = this.rowElementBase(obj)
    row.obj = obj

    if(!writePermission) return row

    row.innerHTML += `
    <td akce class='action-default' contenteditable='false'><a title="Smazat" class="icon td-xmark"></a></td>
    <td akce class='action-edit' contenteditable='false'><a title="Uložit" class="icon td-save"></a><a title="Zrušit" class="icon td-cancel"></a></td>
    <td akce class='action-create' contenteditable='false'><a title="Uložit" class="icon td-save"></a><a title="Zrušit" class="icon td-cancel"></a></td>`

    let edit = (e) => {
      if(parentHasTag(e.target, 'A')) return

      row.ondblclick = null
      this.displayEdit(row)

      let escapeDiscard = (e) => {
        if(e.key != 'Escape') return

        window.removeEventListener('keydown', escapeDiscard)
        discardEdit()
      }
      window.addEventListener('keydown', escapeDiscard)
    }
    let discardEdit = (e) => {
      // e.cancelBubble = true
      row.ondblclick = edit
      this.discardEdit(row)
    }

    if(!create){
      let actionsDefault = row.querySelectorAll('.action-default a')
      row.ondblclick = edit
      actionsDefault[0].onclick = (e) => {
        // e.cancelBubble = true // to prevent calling edit // solved by checking parent tag A (above)
        this.displayDelete(row)
      }

      let actionsEdit = row.querySelectorAll('.action-edit a')
      actionsEdit[0].onclick = () => {
        // e.cancelBubble = true
        row.ondblclick = edit
        this.saveEdit(row)
      }
      actionsEdit[1].onclick = discardEdit
    }
    else{
      let actionsCreate = row.querySelectorAll('.action-create a')
      actionsCreate[0].onclick = (e) => this.saveCreate(e.target.parentNode.parentNode)
      actionsCreate[1].onclick = (e) => this.discardCreate(e.target.parentNode.parentNode)
    }

    return row
  }

  //api
  apiDelete(row, cb){
    fetch(this.api + 'delete.php?id=' + row.obj['id'], {
      method:'post',
      body:JSON.stringify(row.obj)
    })
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

    fetch(this.api + 'post.php', {
      method:'post',
      body:formData
    })
    .then(async r => {
      if(r.status == 200) {
        // retrieve id when creating new row
        let json = await r.text()
        try{
          let robj = JSON.parse(json)
          Object.assign(row.obj, robj)
        } catch {}

        cb()
        alert(this.getObjName(row.obj, 'Záznam') + ' byl vytvořen')
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

    fetch(this.api + 'edit.php', {
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
  displayCreate = () => {
    let row = this.insertObjRow({}, true, 0)
    row.classList.add('create')
    this.formatRowEditBase(row)
  }
  saveCreate(row){
    this.deformatRowEditBase(row, true)
    this.apiPost(row, () => this.resetRow(row), () => this.formatRowEditBase(row))
  }
  discardCreate(row){
    this.removeRow(row)
  }
  getObjName(obj, defaultt){
    let name = defaultt
    if(obj['jmeno']) name = '"' + obj['jmeno'] + '"'
    else if(obj['nazev']) name = '"' + obj['nazev'] + '"'
    else if(obj['cislo']) name = '"' + obj['cislo'] + '"'

    return name
  }
  displayDelete(row){
    if(window.confirm('Smazat ' + this.getObjName(row.obj, 'záznam'))){
      this.apiDelete(row, () => this.removeRow(row))
    }
  }
  displayEdit(row){
    row.classList.add('edit')
    row.oldobj = structuredClone(row.obj)

    this.formatRowEditBase(row)
  }
  saveEdit(row){
    row.classList.remove('edit')
    this.deformatRowEditBase(row, true)
    this.apiPostEdit(row, () => this.resetRow(row), () => {})
  }
  discardEdit(row){
    row.classList.remove('edit')

    this.resetRow(row, row.oldobj)
  }

  // display
  displayRows = (objs) => {
    this.tableBody.clearCh()
    for(let obj of objs){
      this.insertObjRow(obj)
    }
  }
  insertObjRow(obj, create, index){
    let row = this.rowElement(obj, create)
    row.obj = obj
    row.cols = this.fetchColumns(row)

    if(index >= 0){
      this.tableBody.insertBefore(row, this.tableBody.children[index]);
    }
    else{ // default appends to end
      this.tableBody.appendChild(row);
    }

    if(this.rowCallback) this.rowCallback(row, obj)

    return row
  }
}

function selectMultiple(row, fieldName, optionsApi, valueName, textName){
  row.cols[fieldName].clearCh().innerHTML = `
  <select id='select'>
    <option value=''>Vyberte</option>
  </select>
  `
  let strSelect = row.cols[fieldName].querySelector('#select')

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

  if(!row.obj[fieldName]) row.obj[fieldName] = []
  let selectContainer = new SelectContainer(strSelect, row.obj[fieldName], valueName, textName)

  strSelect.onchange = () => {
    let option = strSelect.options[strSelect.selectedIndex]
    selectContainer.select(option.value, option.text)
    strSelect.value = ''
  }
}

class SelectContainer{
  constructor(select, arr, valueName, textName){
    this.container = document.createElement('div')
    this.container.classList.add('select-selected')
    select.insertAdjacentElement('beforebegin', this.container)

    this.arr = arr
    this.valueName = valueName
    this.textName = textName
    this.arrAdded = []
    this.arrRemoved = []

    // fill with initial array
    this.reset()
  }
  reset = () => {
    this.container.clearCh()
    let arrClone = structuredClone(this.arr)
    this.arr.length = 0 // clear the array
    for(let obj of arrClone){
      this.select(obj[this.valueName], obj[this.textName], true, obj)
    }
  }
  select = (value, text, noPushAdded, selectedObj) => {
    // check if object is already selected
    if(this.arr.filter(o => o[this.valueName] == value && o[this.textName] == text).length > 0) return

    if(!selectedObj){
      selectedObj = {}
      selectedObj[this.valueName] = value
      selectedObj[this.textName] = text
    }

    let selectedRow = document.createElement('div')
    selectedRow.classList.add('flex')
    selectedRow.classList.add('nowrap')
    selectedRow.innerHTML = `
    ${text}
    <div class='sgap gap-stretch-h'></div>
    <a>Smazat</a>
    `
    this.arr.push(selectedObj)

    let removed = this.arrRemoved.filter(o => o[this.valueName] == value && o[this.textName] == text)
    if(removed.length > 0) {
      this.arrRemoved.splice(this.arrRemoved.indexOf(removed[0]), 1)
    }
    else if(!noPushAdded) {
      this.arrAdded.push(selectedObj)
    }

    selectedRow.querySelector('a').onclick = () => {
      let index = this.arr.indexOf(selectedObj)
      this.arr.splice(index, 1)

      index = this.arrAdded.indexOf(selectedObj)
      if(index != -1) {
        this.arrAdded.splice(index, 1)
      }
      else {
        this.arrRemoved.push(selectedObj)
      }

      selectedRow.remove()
    }

    this.container.appendChild(selectedRow)
  }
}
function textFormat(parent, obj, fieldName){
  let txt = document.createElement('input')
  txt.type = 'text'
  txt.classList.add('txt')
  if(!obj[fieldName]) obj[fieldName] = ''
  txt.value = obj[fieldName]
  txt.onchange = () => obj[fieldName] = txt.value

  if(parent) parent.clearCh().appendChild(txt)
  return txt
}
function dateFormat(parent, obj, dateName){
  let date = document.createElement('input')
  date.type = 'date'
  if(!obj[dateName]) obj[dateName] = '0000-00-00'
  date.value = obj[dateName]
  date.onchange = () => obj[dateName] = date.value

  if(parent) parent.clearCh().appendChild(date)
  return date
}
function selectFormat(row, colName, objValueName, objTextName, apiGet, valueName, textName){
  let select = document.createElement('select')
  fetch(apiGet)
  .then(r => r.json())
  .then(r => {
    for(let obj of r){
      let option = document.createElement('option')
      option.value = obj[valueName]
      option.innerText = obj[textName]

      select.appendChild(option)

      if(obj[valueName] == row.obj[objValueName]
        || !row.obj[objValueName] // if new row is created, select the first select option
      ) {
        row.obj[objValueName] = obj[valueName]
        row.obj[objTextName] = obj[textName]
        select.value = obj[valueName]
      }
    }

    select.onchange = () => {
      if(objTextName != '') row.obj[objTextName] = select.options[select.selectedIndex].innerText
      row.obj[objValueName] = select.value
    }
  })
  row.cols[colName].clearCh().appendChild(select)
}
function checkboxFormat(row, fieldName){
  let input = document.createElement('input')
  input.type = 'checkbox'

  input.checked = row.obj[fieldName] == 1
  row.obj[fieldName] = input.checked ? 1 : 0

  input.onchange = () => row.obj[fieldName] = input.checked ? 1 : 0
  row.cols[fieldName].clearCh().appendChild(input)
}
