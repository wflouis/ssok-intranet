
let api = 'api/smluvni-partneri/'

// dom manipulation
function rowElementBase(user){
  return `
  <td name="nazev">${user['name'] ?? ''}</td>
  <td name="ico">${user['ico'] ?? ''}</td>
  <td name="mesto">${user['mesto'] ?? ''}</td>
  <td name="ulice">${user['ulice'] ?? ''}</td>
  <td name="psc">${user['psc'] ?? ''}</td>
  <td name="osoba">${user['osoba'] ?? ''}</td>
  <td name="kadresa">${user['kadresa'] ?? ''}</td>
  <td name="telefon">${user['telefon'] ?? ''}</td>
  <td name="email">${user[''] ?? ''}</td>
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

setupTable(api, null, rowElementBase, null, null, formatRowEdit, deformatRowEdit)