
let api = 'api/strediska/'

// dom manipulation
function rowElementBase(obj){ 
  return `
  <td name="zkratka">${obj['zkratka'] ?? ''}</td>
  <td name="nazev">${obj['nazev'] ?? ''}</td>
  <td name="poradi">${obj['poradi'] ?? ''}</td>
  <td name="ostatni">${stringifyOstatni(obj['ostatni'])}</td>
`
}
function parseOstatni(text){
  let ostatni = []
  for(let row of text.trim().split(/\r?\n/)){
    let split = row.trim().split(':')

    if(split[0] == '') continue

    let nadpis = split[0] ?? ''
    let text = split[1] ?? ''

    let jednoOstatni = {}
    jednoOstatni['nadpis'] = nadpis.trim() + ':'
    jednoOstatni['text'] = text.trim()

    ostatni.push(jednoOstatni)
  }
  return ostatni
}
function stringifyOstatni(ostatni){
  let ostatniStr = ''
  if(!ostatni) return ostatniStr

  for(let i = 0; i < ostatni.length; i++){
    ostatniStr += ostatni[i]['nadpis'] + ' ' + ostatni[i]['text']
    if(i != ostatniStr.length - 1) ostatniStr += "\n"
  }
  return ostatniStr
}

function deformatRowEdit(row, cols){
  row.obj['ostatni'] = parseOstatni(cols['ostatni'].innerText)

  return row
}

let mTable = new MTable(api)
mTable.setSearch()
mTable.setNewButton('Nové středisko')

mTable.rowElementBase = rowElementBase
mTable.deformatRowEdit = deformatRowEdit
