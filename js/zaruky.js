
let api = 'api/zaruky/'

let selectStredisko = document.getElementById('select-stredisko')
let selectZadavatel = document.getElementById('select-zadavatel')
let inputOd = document.getElementById('input-od')
let inputDo = document.getElementById('input-do')

function getRows(order, orderDirection) {
  return fetch(
    api + 'get.php' +
    '?search=' + mTable.search.value +
    '&stredisko=' + selectStredisko.value +
    '&zadavatel=' + selectZadavatel.value +
    '&od=' + inputOd.value +
    '&do=' + inputDo.value +
    '&order=' + order +
    '&order-direction=' + orderDirection
  )
  .then(r => r.json())
}

// dom manipulation
function rowElementBase(obj){
  return `
  <td name="cisloSmlouvy">${obj['cisloSmlouvy'] ?? ''}</td>
  <td name="predmetZaruky" class='td-wrap'>${obj['predmetZaruky'] ?? ''}</td>
  <td name="datumZarukyOd">${obj['datumZarukyOd'] ?? ''}</td>
  <td name="datumZarukyDo">${obj['datumZarukyDo'] ?? ''}</td>
  <td name="zadavatel">${obj['zadavatel'] ?? ''}</td>
  <td name="strediska" class='td-wrap'><div class='td-maxheight'>${obj['strediska'] ?? ''}</td>
  <td name="kontroly" class='td-wrap'>${obj['kontroly'] ?? ''}</td>
`
}

function formatRowEdit(row) {
  row.contentEditable = true
  return row
}
function deformatRowEdit(row){
  row.contentEditable = false
  return row
}

selectStredisko.value = '%'
let mTable = new MTable(api)
mTable.setSearch()

mTable.getRows = getRows
mTable.rowElementBase = rowElementBase
mTable.formatRowEdit = formatRowEdit
mTable.deformatRowEdit = deformatRowEdit

selectStredisko.onchange = mTable.getRowsDisplay
selectZadavatel.onchange = mTable.getRowsDisplay
inputOd.onchange = mTable.getRowsDisplay
inputDo.onchange = mTable.getRowsDisplay

mTable.rowCallback = (tr, obj) => {
  tr.onclick = () => {
    window.location = './smlouvy.php?search=' + obj['cisloSmlouvy']
  }

  Array.from(tr.querySelectorAll('[akce]')).map(a => a.remove())

  let days = (new Date(obj['datumZarukyDo']) - new Date()) / 1000 / 86400
  if(days > 30 || days <= -1) return
  tr.style.backgroundColor = 'rgba(255,0,0,'+ (1 - days / 30) +')'
}
mTable.columns.forEach(c => {
  if(c.getAttribute('column') == 'datumZarukyDo'){
    c.onclick()
  }
})