[::main]
tab="lekuen_irudiak"
genero="o"
entity="Irudia"
defaultFLD="irudi_izena"
id="id_irudia"

[fitxategia]
type="FILEFS"

filesystem="../data/"
alias="Irudia"
name_fld="irudi_tamaina"

[fitxategia::irudi_tamaina]
alias="Irudiaren Tamaina"
type="FILE_SIZE"

[fitxategia::irudi_izena]
alias="Irudiaren Izena"
type="FILE_NAME"

[fitxategia::irudi_mota]
alias="Irudiaren Mota"
type="FILE_TYPE"

[iruzkina]
type="SAFETEXTAREA"
alias="Iruzkina"