[::main]
tab="erabiltzaileak"
genero="o"
entity="Erabiltzailea"
defaultFLD="izena"
id="id_erabiltzaile"

[izena]
type="SAFETEXT"
alias="Izena"
clean="url"
req=1

[abizenak]
type="SAFETEXT"
alias="Abizenak"

[erabiltzailea]
type="SAFETEXT"
alias="Erabiltzailea Izena"

[pasahitza]
type="PWD"
alias="Pasahitza"
req="1"
clone="1"
cloneInfo="sartu berriro Pasahitza"

[fitxategia]
type="FILEFS"

filesystem="./data/"
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

[jaiotze_data]
type="DATE"
alias="Jaiotze Data"

[deskribapena]
type="SAFETEXTAREA"
alias="Deskribapena"

[posta]
type="SAFETEXT"
alias="Posta Elektronikoa"
req=1

[facebook]
type="SAFETEXT"
alias="Facebook Erabiltzailea"

[twitter]
type="SAFETEXT"
alias="Twitter Erabiltzailea"
