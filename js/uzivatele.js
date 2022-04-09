
let api = 'api/uzivatele/'

let select = document.getElementById('select')

function getRows(order, orderDirection) {
  let stredisko = select.value
  return fetch(
    api + 'get.php' +
    '?stredisko=' + stredisko +
    '&search=' + search.value +
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
  let internetF = document.createElement('input')
  internetF.type = 'checkbox'

  internetF.checked = row.obj['internet'] == 1
  row.obj['internet'] = internetF.checked ? 1 : 0

  cols['internet'].clearCh().appendChild(internetF)
  internetF.onchange = () => row.obj['internet'] = internetF.checked ? 1 : 0

  let strediskoStr = row.obj['stredisko'] ? row.obj['stredisko'] : '%'
  console.log(strediskoStr)
  row.obj['stredisko'] = strediskoStr
  let strediskoF = document.createElement('select')
  fetch('api/strediska/get-basic.php')
  .then(r => {
    if(r.status != 200) alertError(r, 'edit-get-strediska')
    return r.json()
  })
  .then(r => {
    for(let s of r){
      let option = document.createElement('option')
      option.value = s['zkratka']
      option.innerText = s['zkratka']
      strediskoF.appendChild(option)
    }
    strediskoF.value = strediskoStr
    cols['stredisko'].clearCh().appendChild(strediskoF)
    strediskoF.onchange = () => row.obj['stredisko'] = strediskoF.value
  })

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
let table = new MTable(api, ['stredisko', 'internet'])

table.getRows = getRows
table.rowElementBase = rowElementBase
table.formatRowEdit = formatRowEdit
table.deformatRowEdit = deformatRowEdit

select.onchange = table.getRowsDisplay