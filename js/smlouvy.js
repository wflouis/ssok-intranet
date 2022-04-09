
let prilohyPath = 'prilohy/'

let api = 'api/smlouvy/'

let selectTyp = document.getElementById('select-typ')
let selectStredisko = document.getElementById('select-stredisko')
let selectRok = document.getElementById('select-rok')
let platnostOd = document.getElementById('input-od')
let platnostDo = document.getElementById('input-do')

function getRows(order, orderDirection) {
  return fetch(
    api + 'get.php' +
    '?search=' + search.value +
    '&typ=' + selectTyp.value +
    '&stredisko=' + selectStredisko.value +
    '&rok=' + selectRok.value +
    '&platnost-od=' + platnostOd.value +
    '&platnost-do=' + platnostDo.value +
    '&order=' + order +
    '&order-direction=' + orderDirection
  )
  .then(r => r.json())
}

function stringifyArray(arr, fieldName){
  let str = ''
  for(let i = 0; i < arr.length; i++){
    str += arr[i][fieldName]

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
  <td name="velikost" class='td-right'>${obj['velikost'] ?? ''}</td>
  <td name="strediska" class='td-maxwidth' contenteditable='false'><div class='td-maxheight'>${this.stringifyArray(obj['strediska'], 'zkratka')}</div></td>
  <td name="partneri" class='td-maxwidth' contenteditable='false'>${this.stringifyArray(obj['partneri'], 'nazev')}</td>
  <td name="rodneCislo" class='td-wrap'>${obj['rodneCislo'] ?? ''}</td>
  <td name="datumOd" contenteditable='false'>${obj['datumOd'] ?? ''}</td>
  <td name="datumDo" contenteditable='false'>${obj['datumDo'] ?? ''}</td>
  <td name="faktura" class='td-wrap-s'>${obj['faktura'] ?? ''}</td>
  <td name="prilohy" class='td-right td-maxwidth' contenteditable='false'><div class='scroll-x'></div></td>
`
}
function rowCallback(tr, obj){
  // prilohy
  let prilohy = obj['prilohy']
  let parent = tr.cols['prilohy'].firstChild
  for(let i = 0; i < prilohy.length; i++){
    let priloha = prilohy[i]

    let el = document.createElement('a')
    el.style.display = 'block'
    el.innerHTML = priloha['nazev'] + ' ' + priloha['velikost']
    el.href = prilohyPath + obj['id'] + '/' + priloha['nazev']
    el.target = '_blank'
    parent.appendChild(el)
  }

  // predmet
  tr.insertAdjacentHTML('beforebegin', `
  <tr>
    <td colspan=100><div style='display:flex;'><i>Předmět: </i><span name='predmet' style='flex-grow:1'>${obj['predmet'] ?? ''}</span></div></td>
  </tr>
  `)
  // zaruky
  tr.insertAdjacentHTML('afterend', `
  <tr>
    <td colspan=100>
      <div class='smlouvy-zaruky'>
        <div class='zaruky-reveal'><span>▼</span></div>
        <div class='zaruky-content hidden'>${obj['zaruky']}</div>
      </div>
    </td>
  </tr>
  `)

  let trZaruky = tr.nextElementSibling
  let btnReveal = trZaruky.querySelector('.zaruky-reveal')
  let zaruky = trZaruky.querySelector('.zaruky-content')

  btnReveal.onclick = revealZaruky

  function revealZaruky(e){
    e.cancelBubble = true

    zaruky.classList.remove('hidden')

    btnReveal.onclick = null
    window.addEventListener('click', hideZaruky)
  }
  function hideZaruky(e){
    if(e.target.classList.contains('zaruky-content') || e.target.tagName == 'A') return

    zaruky.classList.add('hidden')

    btnReveal.onclick = revealZaruky
    window.removeEventListener('click', hideZaruky)
  }

  return tr
}
function removeRow(row){
  let index = Array.from(this.table.children).indexOf(row) - 1

  row.previousElementSibling.remove()
  row.nextElementSibling.remove()
  row.remove()

  return index
}

function formatRowEdit(row, cols) {
  cols['predmet'] = row.previousElementSibling.querySelector('[name=predmet]')
  cols['predmet'].contentEditable = true

  // typ
  row.obj['popis'] = cols['popis'].innerText.trim()
  let typSelect = document.createElement('select')
  fetch('api/smlouvy/get-typy.php')
  .then(r => r.json())
  .then(r => {
    for(let typ of r){
      let option = document.createElement('option')
      option.value = typ['id']
      option.innerText = typ['popis']

      typSelect.appendChild(option)

      if(typ['popis'] == row.obj['popis']) {
        typSelect.value = typ['id']
        row.obj['typSmlouvy'] = typ['id']
      }
    }

    typSelect.onchange = () => {
      row.obj['popis'] = typSelect.options[typSelect.selectedIndex].innerText
      row.obj['typSmlouvy'] = typSelect.value
    }
  })
  cols['popis'].clearCh().appendChild(typSelect)

  // dates
  let uzDate = document.createElement('input')
  uzDate.type = 'date'
  uzDate.value = row.obj['datumUzavreni']
  uzDate.onchange = () => row.obj['datumUzavreni'] = uzDate.value
  let odDate = document.createElement('input')
  odDate.type = 'date'
  odDate.value = row.obj['datumOd']
  odDate.onchange = () => row.obj['datumOd'] = odDate.value
  let doDate = document.createElement('input')
  doDate.type = 'date'
  doDate.value = row.obj['datumDo']
  doDate.onchange = () => row.obj['datumDo'] = doDate.value

  cols['datumUzavreni'].clearCh().appendChild(uzDate)
  cols['datumOd'].clearCh().appendChild(odDate)
  cols['datumDo'].clearCh().appendChild(doDate)

  // strediska
  selectMultiple(cols['strediska'], row.obj['strediska'], 'api/strediska/get-basic.php', 'id', 'zkratka')
  // partneri
  selectMultiple(cols['partneri'], row.obj['partneri'], 'api/smluvni-partneri/get-basic.php', 'id', 'nazev')

  // prilohy
  let prilohyLabel = document.createElement('label')
  prilohyLabel.classList.add('txt')
  prilohyLabel.classList.add('button')
  let prilohyInput = document.createElement('input')
  prilohyInput.type = 'file'
  prilohyInput.multiple = true
  
  prilohyLabel.appendChild(prilohyInput)
  cols['prilohy'].clearCh().appendChild(prilohyLabel)

  return row
}

function deformatRowEdit(row, cols){
  row.contentEditable = false

  cols['predmet'].contentEditable = false
  row.obj['predmet'] = cols['predmet'].innerText.trim()

  // get and post prilohy
  let prilohyInput = cols['prilohy'].querySelector('input[type=file]')

  row.formData = new FormData();
  row.obj['newPrilohy'] = []
  let prilohy = prilohyInput.files
  for(let i = 0; i < prilohy.length; i++){
    let file = prilohy[i]

    row.formData.append("prilohy[]", file);
    row.obj['prilohy'].push({nazev: file.name, velikost: file.size})
    row.obj['newPrilohy'].push({nazev: file.name, velikost: file.size})
  }
  cols['prilohy'].clearCh()

  return row
}

selectStredisko.value = '%'
let table = new MTable(api, ['predmet', 'popis', 'strediska', 'partneri', 'datumUzavreni', 'datumOd', 'datumDo', 'prilohy', 'zaruky'])
table.getRows = getRows
table.rowElementBase = rowElementBase
table.removeRow = removeRow
table.formatRowEdit = formatRowEdit
table.deformatRowEdit = deformatRowEdit

table.rowCallback = rowCallback

selectTyp.onchange = table.getRowsDisplay
selectStredisko.onchange = table.getRowsDisplay
selectRok.onchange = table.getRowsDisplay
platnostOd.onchange = table.getRowsDisplay
platnostDo.onchange = table.getRowsDisplay
