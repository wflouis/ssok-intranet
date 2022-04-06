
let api = 'api/smluvni-partneri/'

// dom manipulation
function rowElementBase(obj){
  return `
  <td name="nazev">${obj['name'] ?? ''}</td>
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

let table = new MTable(api, null, rowElementBase, null, null, null, null)