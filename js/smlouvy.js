
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

// dom manipulation
function rowElementBase(obj){
  return `
  <td name="cisloSmlouvy" class='td-wrap-s'>${obj['cisloSmlouvy'] ?? ''}</td>
  <td name="popis" class='td-wrap-s' contenteditable=false>${obj['popis'] ?? ''}</td>
  <td name="datumUzavreni">${obj['datumUzavreni'] ?? ''}</td>
  <td name="cena" class='td-right'>${obj['cena'] ?? ''}</td>
  <td name="velikost" class='td-right'>${obj['velikost'] ?? ''}</td>
  <td name="strediska" class='td-wrap-s'><div class='td-maxheight'>${obj['strediska'] ?? ''}</div></td>
  <td name="partneri" class='td-wrap'>${obj['partneri'] ?? ''}</td>
  <td name="rodneCislo" class='td-wrap'>${obj['rodneCislo'] ?? ''}</td>
  <td name="datumOd" class='td-wrap-s'>${obj['datumOd'] ?? ''}</td>
  <td name="datumDo" class='td-wrap-s'>${obj['datumDo'] ?? ''}</td>
  <td name="faktura" class='td-wrap-s'>${obj['faktura'] ?? ''}</td>
  <td name="prilohy" class='td-right'>${obj['prilohy'] ?? ''}</td>
`
}
function rowCallback(tr, obj){
  tr.insertAdjacentHTML('beforebegin', `
  <tr>
    <td colspan=100><i>Předmět: </i><span name='predmet'>${obj['predmet'] ?? ''}</span></td>
  </tr>
  `)
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
    console.log(e.target, e.target.tagName)
    if(e.target.classList.contains('zaruky-content') || e.target.tagName == 'A') return

    zaruky.classList.add('hidden')

    btnReveal.onclick = revealZaruky
    window.removeEventListener('click', hideZaruky)
  }

  return tr
}

function formatRowEdit(row) {
  row.contentEditable = true
  row.obj = {}

  let columns = table.fetchColumns(row)
  row.obj['typ'] = columns['popis'].innerText.trim()

  let typSelect = document.createElement('select')
  typSelect.classList.add('select-menu')
  
  fetch('api/smlouvy/get-typy.php')
  .then(r => r.json())
  .then(r => {
    for(let typ of r){
      let option = document.createElement('option')
      option.value = typ['id']
      option.innerText = typ['popis']

      typSelect.appendChild(option)

      if(typ['popis'] == row.obj['typ']) typSelect.value = typ['id']
    }

    typSelect.onchange = () => {
      row.obj['typ'] = typSelect.options[typSelect.selectedIndex].innerText
    }
  })
  columns['popis'].clearCh().appendChild(typSelect)

  

  return row
}
function deformatRowEdit(row){
  row.contentEditable = false

  let columns = table.fetchColumns(row)
  columns['popis'].clearCh().innerText = row.obj['typ']

  return row
}

selectStredisko.value = '%'
let table = new MTable(api, getRows, rowElementBase, null, null, formatRowEdit, deformatRowEdit)

table.rowCallback = rowCallback

selectTyp.onchange = table.getRowsDisplay
selectStredisko.onchange = table.getRowsDisplay
selectRok.onchange = table.getRowsDisplay
platnostOd.onchange = table.getRowsDisplay
platnostDo.onchange = table.getRowsDisplay
