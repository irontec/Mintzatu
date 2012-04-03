[::main]
tab="lekuak"
genero="o"
entity="Lekua"
defaultFLD="izena"
id="id_lekua"

[izena]
type="SAFETEXT"
alias="Izena"
clean="url"
req=1

[helbidea]
type="SAFETEXTAREA"
alias="Helbidea"

[deskribapena]
type="SAFETEXTAREA"
alias="Deskribapena"

[mapa]
type="GLOCATION"
alias="Mapan Kokapena"

[mapa::latitudea]
alias="Latitudea"
type="GLOCATION_DATA"
googleJsonKey="geometry/location/latitude"

[mapa::longitudea]
alias="Longitudea"
type="GLOCATION_DATA"
googleJsonKey="geometry/location/longitude"

[id_erabiltzaile]
type="GHOST"
alias="Nork sortua"
sql="(SELECT e.izena FROM erabiltzaileak e WHERE e.id_erabiltzaile = lekuak.id_erabiltzaile)"

[id_kategoria]
type="ENUMBD"
alias="Kategoria"
tab="kategoriak"
id="id_kategoria"
fld="izena"
fldorder="izena asc"
