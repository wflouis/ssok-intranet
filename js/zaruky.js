
let api = 'api/zaruky/'

let selectStredisko = document.getElementById('select-stredisko')
let selectZadavatel = document.getElementById('select-zadavatel')
let inputOd = document.getElementById('input-od')
let inputDo = document.getElementById('input-do')

function getRows(order, orderDirection) {
  return fetch(
    api + 'get.php' +
    '?search=' + search.value +
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
let table = new MTable(api, getRows, rowElementBase, null, null, formatRowEdit, deformatRowEdit)

selectStredisko.onchange = table.getRowsDisplay
selectZadavatel.onchange = table.getRowsDisplay
inputOd.onchange = table.getRowsDisplay
inputDo.onchange = table.getRowsDisplay

table.rowCallback((tr, obj) => {
  tr.onclick = () => {
    window.location = './smlouvy.php?cislo=' + obj['cisloSmlouvy']
  }
})