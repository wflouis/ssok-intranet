
let api = 'api/uzivatele/'

let select = document.getElementById('select')

function getRows(order, orderDirection) {
  let stredisko = select.value
  return fetch(
    api + 'get.php' +
    '?stredisko=' + stredisko +
    '&search=' + mTable.search.value +
    '&order=' + order +
    '&order-direction=' + orderDirection
  )
  .then(r => r.json())
}

// dom manipulation
function rowElementBase(user){
  return `
  <td name="jmeno">${user['jmeno'] ?? ''}</td>
  <td name="funkce">${user['funkce'] ?? ''}</td>
  <td name="telefon" style='white-space:nowrap'>${user['telefon'] ?? ''}</td>
  <td name="email">${user['email'] ?? ''}</td>
  <td name="stredisko" contenteditable='false'>${user['stredisko'] ?? ''}</td>
  <td name="internet" contenteditable='false'>${user['internet'] ?? ''}</td>
  <td name="opravneni" contenteditable='false' style='white-space:nowrap'>${user['opravneni'] ?? ''}</td>
`
}

// edit format
function formatRowEdit(row, cols){
  checkboxFormat(row, 'internet')

  selectFormat(row, 'stredisko', 'stredisko', '', 'api/strediska/get-basic.php', 'zkratka', 'zkratka')

  if(!row.obj['opravneni']) row.obj['opravneni'] = ''
  let opravneniStr = row.obj['opravneni']
  fetch(api + 'get-opravneni.php')
  .then(r => {
    if(r.status != 200) alertError(r, 'edit-get-opravneni')
    return r.json()
  })
  .then(r => {
    cols['opravneni'].clearCh()
    for(let o of r){
      cols['opravneni'].insertAdjacentHTML('beforeend', `
      <label style='display:block'>
        <input name='${o['zkratka']}' type='checkbox' ${opravneniStr.includes(o['zkratka']) ? 'checked' : ''}>
        ${o['popis']}
      </label>
      `)
    }
  })

  return row
}
function deformatRowEdit(row, cols){
  let opravneniF = cols['opravneni'].querySelectorAll('input')

  let opravneniStr = ''
  for(let o of opravneniF){
    opravneniStr += o.checked ? o.getAttribute('name') : ''
  }

  row.obj['opravneni'] = opravneniStr
  cols['opravneni'].clearCh().innerText = opravneniStr

  return row
}

select.value = '%'
let mTable = new MTable(api, ['stredisko', 'internet'])
mTable.setSearch()
mTable.setNewButton('Nový uživatel')

mTable.getRows = getRows
mTable.rowElementBase = rowElementBase
mTable.formatRowEdit = formatRowEdit
mTable.deformatRowEdit = deformatRowEdit

select.onchange = mTable.getRowsDisplay