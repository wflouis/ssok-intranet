
let prilohyPath = 'prilohy/'

let api = 'api/smlouvy/'

let selectTyp = document.getElementById('select-typ')
let selectStredisko = document.getElementById('select-stredisko')
let selectRok = document.getElementById('select-rok')
let platnostOd = document.getElementById('input-od')
let platnostDo = document.getElementById('input-do')

function getRows(order, orderDirection, limit) {
  return fetch(
    api + 'get.php' +
    '?search=' + mTable.search.value +
    '&typ=' + selectTyp.value +
    '&stredisko=' + selectStredisko.value +
    '&rok=' + selectRok.value +
    '&platnost-od=' + platnostOd.value +
    '&platnost-do=' + platnostDo.value +
    '&order=' + order +
    '&order-direction=' + orderDirection +
    (limit ? '&limit=' + limit : '')
  )
  .then(r => r.json())
}

function getRowsExport(order, orderDirection, limit) {
  return fetch(
    api + 'export.php' +
    '?search=' + mTable.search.value +
    '&typ=' + selectTyp.value +
    '&stredisko=' + selectStredisko.value +
    '&rok=' + selectRok.value +
    '&platnost-od=' + platnostOd.value +
    '&platnost-do=' + platnostDo.value +
    '&order=' + order +
    '&order-direction=' + orderDirection +
    (limit ? '&limit=' + limit : '')
  )
  .then(r => r.json())
}

function stringifyArray(arr, fieldName){
  let str = ''

  if(!arr) return str
  for(let i = 0; i < arr.length; i++){
    str += arr[i][fieldName]

    if(i != arr.length - 1) str += '<br>'
  }
  return str
}
function formatFaktura(obj){
  if(!obj['faktura']) return "<div class='faktura'></div><div class='uhrazeno'></div>"
  let str = "<div class='faktura'>"+obj['faktura']+"</div>"
  if(obj['uhrazeno']) str += "uhrazeno: <div class='uhrazeno'>"+obj['uhrazeno']+"</div>"
  return str
}
function formatFaktury(arr){
  let str = ''

  if(!arr) return str
  for(let i = 0; i < arr.length; i++){
    str += arr[i]['faktura'] + ', uhrazeno: ' + arr[i]['uhrazeno']

    if(i != arr.length - 1) str += '<br>'
  }
  return str
}
function rowElementBase(obj){
  return `
  <td name="cisloSmlouvy" class='td-wrap-s'>${obj['cisloSmlouvy'] ?? ''}</td>
  <td name="popis" class='td-wrap-s' contenteditable='false'>${obj['popis'] ?? ''}</td>
  <td name="datumUzavreni" contenteditable='false'>${obj['datumUzavreni'] ?? ''}</td>
  <td name="cena" class='td-right'>${obj['cena'] ?? ''}</td>
  <td name="strediska" class='td-maxwidth' contenteditable='false'><div class='td-maxheight'>${stringifyArray(obj['strediska'], 'zkratka')}</div></td>
  <td name="partneri" class='td-maxwidth' contenteditable='false'>${stringifyArray(obj['partneri'], 'nazev')}</td>
  <td name="rodneCislo" class='td-wrap'>${obj['rodneCislo'] ?? ''}</td>
  <td name="datumOd" contenteditable='false'>${obj['datumOd'] ?? ''}</td>
  <td name="datumDo" contenteditable='false'>${obj['datumDo'] ?? ''}</td>
  <td name="faktury" contenteditable='false'>${formatFaktury(obj['faktury'])}</td>
  <td name="prilohy" class='td-maxwidth' contenteditable='false'><div class='scroll-x'></div></td>
`
}
function rowCallback(tr, obj){
  // prilohy
  let prilohy = obj['prilohy'] ?? {}
  let prilohyEl = tr.cols['prilohy'].firstChild
  for(let i = 0; i < prilohy.length; i++) {
    let priloha = prilohy[i]

    let el = document.createElement('a')
    el.style.display = 'block'
    el.innerHTML = priloha['nazev']
    el.onclick = () => downloadFile('api/smlouvy/readpriloha.php', obj['id'] + '/' + priloha['nazev'])
    prilohyEl.appendChild(el)
  }

  let souvisejici = obj['souvisejici'] ?? []
  for(let i = 0; i < souvisejici.length; i++){
    if(i == 0) prilohyEl.insertAdjacentHTML('beforeend', '<span>Související smlouvy:</span><br>')
    let smlouvaEl = document.createElement('a')
    smlouvaEl.innerHTML = souvisejici[i]['cisloSmlouvy']
    smlouvaEl.href = '/smlouvy.php?search=' + souvisejici[i]['cisloSmlouvy']
    prilohyEl.appendChild(smlouvaEl)

    if(i < souvisejici.length - 1) prilohyEl.appendChild(document.createElement('br'))
  }

  // predmet
  tr.insertAdjacentHTML('beforebegin', `
  <tr>
    <td colspan=100><div style='display:flex;'><i>Předmět: </i><span name='predmet' style='flex-grow:1'>${obj['predmet'] ?? ''}</span></div></td>
  </tr>
  `)

  let dropdown = new RowDropdown(tr)

  if(obj['id'] == null) return tr

  let zaruky = dropdown.content

  let zarukyTabl = null
  dropdown.onReveal = () => {
    if(zarukyTabl) return

    zarukyTabl = zarukyTable(obj['id'] ?? '')
    zaruky.appendChild(zarukyTabl.table)
    zarukyTabl.setTitle('Záruky')
    zarukyTabl.setNewButton('Nová záruka')
  }

  return tr
}
class RowDropdown {
  constructor(tr){
    this.id = Math.random()
    tr.insertAdjacentHTML('afterend', `
    <tr>
      <td colspan=100><div class='row-dropdown'>
          <div class='row-dropdown-button'><span>▼</span></div>
          <div id='${this.id}' class='row-dropdown-content hidden'></div>
      </div></td>
    </tr>
    `)

    let trDropdown = tr.nextElementSibling
    this.btnReveal = trDropdown.querySelector('.row-dropdown-button')
    this.content = trDropdown.querySelector('.row-dropdown-content')

    this.btnReveal.onclick = this.revealDropdown
  }

  revealDropdown = (e) => {
    e.cancelBubble = true

    this.content.classList.remove('hidden')

    this.btnReveal.onclick = null
    window.addEventListener('click', this.hideDropdown)

    if(this.onReveal) this.onReveal()
  }
  hideDropdown = (e) => {
    if(parentHasId(e.target, this.id) || e.target.tagName == 'A') return

    this.content.classList.add('hidden')

    this.btnReveal.onclick = this.revealDropdown
    window.removeEventListener('click', this.hideDropdown)
  }
}
function zarukyTable(idSmlouvy){
  // zaruky
  let table = document.createElement('table')
  table.classList.add('table-overflow')
  table.innerHTML = `
  <thead>
    <tr>
      <td column='predmetZaruky'>Předmět</td>
      <td column='datumZarukyOd'>Od</td>
      <td column='datumZarukyDo'>Do</td>
      <td nosort>Akce</td>
    </tr>
  </thead>
  <tbody></tbody>
  `
  let tableBody = table.querySelector('tbody')
  let apiZaruky = 'api/smlouvy/zaruky/'
  let mTable = new MTable(apiZaruky, ['datumZarukyOd', 'datumZarukyDo'], tableBody)

  mTable.getRows = () => {
    return fetch(apiZaruky + 'get.php' +
    '?id-smlouvy=' + idSmlouvy +
    '&order=' + mTable.order +
    '&order-direction=' + mTable.orderDir
    ).then(r => r.json())
  }
  mTable.rowElementBase = obj => {
    // for new create rows
    obj['id_smlouvy'] = idSmlouvy

    return `
    <td name="predmetZaruky">${obj['predmetZaruky'] ?? ''}</td>
    <td name="datumZarukyOd" contenteditable='false'>${obj['datumZarukyOd'] ?? ''}</td>
    <td name="datumZarukyDo" contenteditable='false'>${obj['datumZarukyDo'] ?? ''}</td>
    `
  }
  mTable.removeRow = row => {
    let index = Array.from(mTable.tableBody.children).indexOf(row)
    row.nextElementSibling.remove()
    row.remove()
    return index
  }
  mTable.formatRowEdit = (row, cols) => {
    dateFormat(row.cols['datumZarukyOd'], row.obj, 'datumZarukyOd')
    dateFormat(row.cols['datumZarukyDo'], row.obj, 'datumZarukyDo')
  }

  mTable.rowCallback = (row, obj) => {
    let kontroly = new RowDropdown(row).content

    if(obj['id_zaruky'] == null) return
    let kontrolyTabl = kontrolyTable(idSmlouvy, obj['id_zaruky'])
    kontroly.appendChild(kontrolyTabl.table)

    kontrolyTabl.setTitle('Kontroly')
    kontrolyTabl.setNewButton('Nová kontrola')
  }

  return mTable
}
function kontrolyTable(idSmlouvy, idZaruky){
  // zaruky
  let table = document.createElement('table')
  table.innerHTML = `
  <thead>
    <tr>
      <td column='datumKontroly'>Datum</td>
      <td column='vysledekKontroly'>Výsledek</td>
      <td column="zavady">Závady</td>
      <td column='datumOdstraneni'>Datum Odstranění</td>
      <td nosort>Akce</td>
    </tr>
  </thead>
  <tbody></tbody>
  `
  let tableBody = table.querySelector('tbody')
  let apiKontroly = 'api/smlouvy/kontroly/'
  let mTable = new MTable(apiKontroly, ['datumKontroly', 'zavady', 'datumOdstraneni'], tableBody)

  mTable.getRows = () => {
    return fetch(apiKontroly + 'get.php' +
    '?id-smlouvy=' + idSmlouvy +
    '&id-zaruky=' + idZaruky +
    '&order=' + mTable.order +
    '&order-direction=' + mTable.orderDir
    ).then(r => r.json())
  }
  mTable.rowElementBase = obj => {
    // for new create rows
    obj['id_smlouvy'] = idSmlouvy
    obj['id_zaruky'] = idZaruky

    return `
    <td name="datumKontroly" contenteditable='false'>${obj['datumKontroly'] ?? ''}</td>
    <td name="vysledekKontroly">${obj['vysledekKontroly'] ?? ''}</td>
    <td name="zavady" contenteditable='false'>${obj['zavady'] ?? ''}</td>
    <td name="datumOdstraneni" contenteditable='false'>${obj['datumOdstraneni'] ?? ''}</td>
    `
  }
  mTable.formatRowEdit = (row, cols) => {
    dateFormat(row.cols['datumKontroly'], row.obj, 'datumKontroly')
    dateFormat(row.cols['datumOdstraneni'], row.obj, 'datumOdstraneni')
    checkboxFormat(row, 'zavady')
  }

  return mTable
}

function removeRow(row){
  let index = Array.from(mTable.tableBody.children).indexOf(row) - 1

  row.previousElementSibling.remove()
  row.nextElementSibling.remove()
  row.remove()

  return index
}

function formatRowEdit(row, cols) {
  cols['predmet'] = row.previousElementSibling.querySelector('[name=predmet]')
  cols['predmet'].contentEditable = true

  // typ
  selectFormat(row, 'popis', 'typSmlouvy', 'popis', 'api/smlouvy/get-typy.php', 'id', 'popis')

  // dates
  dateFormat(row.cols['datumUzavreni'], row.obj, 'datumUzavreni')
  dateFormat(row.cols['datumOd'], row.obj, 'datumOd')
  dateFormat(row.cols['datumDo'], row.obj, 'datumDo')

  // strediska
  selectMultiple(row, 'strediska', 'api/strediska/get-basic.php', 'id', 'zkratka')
  // partneri
  selectMultiple(row, 'partneri', 'api/smluvni-partneri/get-basic.php', 'id', 'nazev')

  // faktura
  if(!row.obj['faktury']) row.obj['faktury'] = []

  let newFaktura = {faktura:'',uhrazeno:'0000-00-00'}
  let div = document.createElement('div')
  let text = textFormat(null, newFaktura, 'faktura')
  let date = dateFormat(null, newFaktura, 'uhrazeno')
  let submit = document.createElement('a')
  submit.innerHTML = 'Přidat'

  div.appendChild(text)
  div.appendChild(date)
  div.appendChild(submit)
  row.cols['faktury'].clearCh().appendChild(div)

  let fakturyContainer = new SelectContainer(div, row.obj['faktury'], 'faktura', 'text')

  submit.onclick = () => {
    fakturyContainer.select(text.value, text.value + ', uhrazeno: ' + date.value, false, {faktura:text.value,uhrazeno:date.value,text:text.value + ', uhrazeno: ' + date.value})
    text.value = ''
  }

  // textFormat(row.cols['faktura'].querySelector('.faktura'), row.obj, 'faktura')
  // dateFormat(row.cols['faktura'].querySelector('.uhrazeno'), row.obj, 'uhrazeno')

  // prilohy
  row.formData = new FormData();
  row.obj['postPrilohy'] = []
  row.obj['deletePrilohy'] = []

  let prilohyLabel = document.createElement('label')
  prilohyLabel.classList.add('txt','button','icon','input-file-icon')
  let prilohyInput = document.createElement('input')
  prilohyInput.type = 'file'
  prilohyInput.multiple = true

  prilohyLabel.appendChild(prilohyInput)
  cols['prilohy'].clearCh().appendChild(prilohyLabel)

  if(!row.obj['prilohy']) row.obj['prilohy'] = []
  let selectContainer = new SelectContainer(prilohyLabel, row.obj['prilohy'], 'velikost', 'nazev')
  selectContainer.arrAdded = row.obj['postPrilohy']
  selectContainer.arrRemoved = row.obj['deletePrilohy']

  prilohyInput.onchange = () => {
    // remove previous inputted files
    row.formData.delete('prilohy[]')
    for(let i = 0; i < row.obj['postPrilohy'].length;) {
      let priloha = row.obj['postPrilohy'][0]

      row.obj['prilohy'].splice(row.obj['prilohy'].indexOf(priloha), 1)
      row.obj['postPrilohy'].splice(0, 1)
    }
    selectContainer.reset()

    // add new inputted files
    for(let file of prilohyInput.files){
      row.formData.append('prilohy[]', file);
      selectContainer.select(file.size, file.name)
    }
  }

  return row
}

function deformatRowEdit(row, cols){
  row.contentEditable = false

  cols['predmet'].contentEditable = false
  row.obj['predmet'] = cols['predmet'].innerText.trim()

  return row
}

selectStredisko.value = '%'
let mTable = new MTable(api, ['predmet', 'popis', 'strediska', 'partneri', 'datumUzavreni', 'datumOd', 'datumDo', 'uhrazeno', 'faktury', 'prilohy', 'zaruky'])
mTable.columns[2].click()
mTable.columns[2].click()
mTable.setSearch()

let exportColumns = Array.from(mTable.columns)

let predmet = document.createElement('td')
predmet.setAttribute('column', 'predmet')
predmet.innerText = 'Předmět'
exportColumns.splice(1, 0, predmet)

console.log(mTable.table.querySelector('thead td[column=partneri]'))
exportColumns.splice(4, 0, mTable.table.querySelector('thead td[column=partneri]'))

mTable.setExport(exportColumns)

mTable.setNewButton('Nová smlouva')

mTable.getRows = getRows
mTable.getRowsExport = getRowsExport
mTable.rowElementBase = rowElementBase
mTable.removeRow = removeRow
mTable.formatRowEdit = formatRowEdit
mTable.deformatRowEdit = deformatRowEdit

mTable.rowCallback = rowCallback

selectTyp.onchange = mTable.getRowsDisplay
selectStredisko.onchange = mTable.getRowsDisplay
selectRok.onchange = mTable.getRowsDisplay
platnostOd.onchange = mTable.getRowsDisplay
platnostDo.onchange = mTable.getRowsDisplay
