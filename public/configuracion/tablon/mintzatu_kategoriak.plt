[::main]
tab="kategoriak"
genero="o"
entity="Kategoria"
defaultFLD="izena"
id="id_kategoria"

[izena]
type="SAFETEXT"
alias="Izena"
clean="url"
req=1

[deskribapena]
type="SAFETEXTAREA"
alias="Deskribapena"

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