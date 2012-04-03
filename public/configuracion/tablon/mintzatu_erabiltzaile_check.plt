[::main]
tab="checks"
genero="o"
entity="Checkin-a"
defaultFLD="datetime"
id="id_check"

[id_lekua]
type="GHOST"
alias="Lekua"
sql="(SELECT izena FROM lekuak WHERE lekuak.id_lekua = checks.id_lekua)"

[iruzkina]
type="SAFETEXTAREA"
alias="Iruzkina"

[noiz]
type="SAFETEXT"
alias="Noiz"