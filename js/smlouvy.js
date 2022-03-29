
let api = 'api/smlouvy/'

let selectTyp = document.getElementById('select-typ')
let selectStredisko = document.getElementById('select-stredisko')
selectStredisko.value = '%'

function getRows(order, orderDirection) {
  return fetch(
    api + 'get.php' +
    '?search=' + search.value +
    '&typ=' + selectTyp.value +
    '&stredisko=' + selectStredisko.value +
    '&order=' + order +
    '&order-direction=' + orderDirection
  )
  .then(r => r.json())
}

// dom manipulation
function rowElementBase(obj){
  return `
  <td name="cisloSmlouvy">${obj['cisloSmlouvy'] ?? ''}</td>
  <td name="predmet" class='td-wrap'>${obj['predmet'] ?? ''}</td>
  <td name="datumUzavreni">${obj['datumUzavreni'] ?? ''}</td>
  <td name="cena" class='td-wrap-s'>${obj['cena'] ?? ''}</td>
  <td name="velikost">${obj['velikost'] ?? ''}</td>
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

setupTable(api, getRows, rowElementBase, null, null, formatRowEdit, deformatRowEdit)