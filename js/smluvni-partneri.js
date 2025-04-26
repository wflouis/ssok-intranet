
let api = 'api/smluvni-partneri/'

// dom manipulation
function rowElementBase(obj){
  return `
  <td name="nazev">${obj['nazev'] ?? ''}</td>
  <td name="ico">${obj['ico'] ?? ''}</td>
  <td name="mesto">${obj['mesto'] ?? ''}</td>
  <td name="ulice">${obj['ulice'] ?? ''}</td>
  <td name="psc">${obj['psc'] ?? ''}</td>
  <td name="osoba">${obj['osoba'] ?? ''}</td>
  <td name="kadresa">${obj['kadresa'] ?? ''}</td>
  <td name="telefon">${obj['telefon'] ?? ''}</td>
  <td name="email">${obj[''] ?? ''}</td>
`
}

let mTable = new MTable(api)
mTable.setSearch()
mTable.setNewButton('Nový partner')

mTable.rowElementBase = rowElementBase