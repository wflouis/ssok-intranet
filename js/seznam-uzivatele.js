
let select = document.getElementById('select')
select.onchange = getRows
select.value = '%'

//api
let order = 'jmeno'
let orderDirection = 'asc'

let api = 'api/seznam-uzivatele/'

function getRows() {
  let stredisko = select.value
  fetch(
    api + 'get.php' +
    '?stredisko=' + stredisko +
    '&search=' + search.value +
    '&order=' + order +
    '&order-direction=' + orderDirection
  )
  .then(r => r.json())
  .then(r => displayRows(r))
}

// dom manipulation
function rowElementBase(user){
  return `
  <td name="jmeno">${user['name'] ?? ''}</td>
  <td name="funkce">${user['funkce'] ?? ''}</td>
  <td name="telefon" style='white-space:nowrap'>${user['telefon'] ?? ''}</td>
  <td name="email">${user['email'] ?? ''}</td>
  <td name="stredisko" contenteditable='false'>${user['stredisko'] ?? ''}</td>
  <td name="internet" contenteditable='false'>${user['internet'] ?? ''}</td>
  <td name="opravneni" contenteditable='false' style='white-space:nowrap'>${user['opravneni'] ?? ''}</td>
`
}
function getObjectFromRow(row){
  let elValues = row.querySelectorAll('[name]')
  let obj = {}
  elValues.forEach(e => {
    obj[e.getAttribute('name')] = e.innerText.trim()
  })
  return obj
}
function fillRowWithObject(row, obj){
  let elValues = row.querySelectorAll('[name]')
  for(let el of elValues){
    el.innerText = obj[el.getAttribute('name')]
  }
}

// edit format
function formatRowEdit(row){
  row.contentEditable = true;

  let strediskoE = row.querySelector('[name=stredisko]')
  let internetE = row.querySelector('[name=internet]')
  let opravneniE = row.querySelector('[name=opravneni]')

  let internetF = document.createElement('input')
  internetF.type = 'checkbox'
  internetF.checked = internetE.innerHTML == 1

  internetE.clearCh().appendChild(internetF)

  let strediskoStr = strediskoE.innerText.trim() == '' ? '%' : strediskoE.innerText.trim()
  let strediskoF = document.createElement('select')
  fetch(api + 'get-strediska.php')
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
    strediskoE.clearCh().appendChild(strediskoF)
  })

  let opravneniStr = opravneniE.innerText
  fetch(api + 'get-opravneni.php')
  .then(r => {
    if(r.status != 200) alertError(r, 'edit-get-opravneni')
    return r.json()
  })
  .then(r => {
    opravneniE.clearCh()
    for(let o of r){
      opravneniE.insertAdjacentHTML('beforeend', `
      <label style='display:block'>
        <input name='${o['zkratka']}' type='checkbox' ${opravneniStr.includes(o['zkratka']) ? 'checked' : ''}>
        ${o['popis']}
      </label>
      `)
    }
  })

  return row
}
function deformatRowEdit(row){
  row.contentEditable = false;

  let strediskoE = row.querySelector('[name=stredisko]')
  let internetE = row.querySelector('[name=internet]')
  let opravneniE = row.querySelector('[name=opravneni]')

  let strediskoF = row.querySelector('[name=stredisko] select')
  let internetF = row.querySelector('[name=internet] input')
  let opravneniF = row.querySelectorAll('[name=opravneni] input')

  let opravneniStr = ''
  for(let o of opravneniF){
    opravneniStr += o.checked ? o.getAttribute('name') : ''
  }

  strediskoE.clearCh().innerText = strediskoF.value
  internetE.clearCh().innerText = internetF.checked ? 1 : 0
  opravneniE.clearCh().innerText = opravneniStr

  return row
}