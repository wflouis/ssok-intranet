
let api = 'api/aktuality/'

function getRows(order, orderDirection) {
  return fetch(
    api + 'get.php' +
    '?from-id=' + fromId +
    '&order=' + order +
    '&order-direction=' + orderDirection
  )
  .then(r => r.json())
}

// dom manipulation
function rowElementBase(obj){
  return `
  <td name="datum" contentEditable='false'>${obj['datum'] ?? ''}</td>
  <td name="text" class='td-newline'>${obj['text'] ?? ''}</td>
  <td name="mail" contentEditable='false'></td>
`
}

function formatRowEdit(row){
  row.contentEditable = true

  let mail = row.querySelector('[name=mail]')
  mail.innerHTML = `
  <div id='mail-selected'></div>
  <select id='mail-select' class='txt'>
    <option value=''>Vyberte adresáty</option>
  </select>
  Zpráva bude odeslána na<br>uvedené e-maily, jakmile ji uložíte
  `
  let mailSelected = mail.querySelector('#mail-selected')
  let mailSelect = mail.querySelector('#mail-select')

  fetch('api/strediska/get-basic.php')
  .then(r => {
    if(r.status != 200) alertError(r, 'mail-get-strediska')
    return r.json()
  })
  .then(r => {
    for(let s of r){
      mailSelect.insertAdjacentHTML('beforeend', `<option type='s' value='${s['zkratka']}'>${s['nazev']}</option>`)
    }
  })
  fetch('api/uzivatele/get-basic.php')
  .then(r => {
    if(r.status != 200) alertError(r, 'mail-get-uzivatele')
    return r.json()
  })
  .then(r => {
    for(let s of r){
      mailSelect.insertAdjacentHTML('beforeend', `<option type='u' email='${s['email']}' value='${s['id']}'>${s['name']}</option>`)
    }
  })

  row.usersMails = []
  row.strediskaMails = []
  mailSelect.onchange = () => {
    let option = mailSelect.options[mailSelect.selectedIndex]
    let type = option.getAttribute('type')
    let text = option.text

    if(type == 'u') {
      row.usersMails.push(option.value)
      text = option.getAttribute('email')
      if(text.trim() == '') {
        alert('E-mail uživatele "' + option.text + '" je prázdný')
        return
      }
    }
    else if(type == 's') row.strediskaMails.push(option.value)
    else return

    let mail = document.createElement('div')
    mail.classList.add('flex')
    mail.innerHTML = `
    <${type}-id hidden>${option.value}</${type}-id>
    ${text}
    <div class='sgap gap-stretch-h'></div>
    <a>Smazat</a>
    `
    mail.querySelector('a').onclick = () => {
      let arr = row.usersMails
      if(type == 's') arr = row.strediskaMails
      
      arr.splice(arr.indexOf(option.value), 1);
      mail.remove()
    }

    mailSelected.appendChild(mail)
    mailSelect.value = ''
  }

  return row
}
function deformatRowEdit(row){
  row.contentEditable = false
  
  let mail = row.querySelector('[name=mail]')
  mail.clearCh()

  if((row.usersMails && row.usersMails.length != 0) || (row.strediskaMails && row.strediskaMails.length != 0)){
    alert('Zpráva byla odeslána na vybrané e-maily')
  }

  return row
}

function getObjectFromRow(row){
  let elValues = row.querySelectorAll('[name]')
  let obj = {}
  elValues.forEach(e => {
    let attr = e.getAttribute('name')

    if(attr == 'mail'){
      obj['usersMails'] = row.usersMails ?? []
      obj['strediskaMails'] = row.strediskaMails ?? []
    }
    else{
      obj[attr] = e.innerText.trim()
    }
  })

  return obj
}

let table = new MTable(api, getRows, rowElementBase, getObjectFromRow, null, formatRowEdit, deformatRowEdit)
