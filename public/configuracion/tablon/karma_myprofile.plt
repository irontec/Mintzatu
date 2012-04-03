[::main]

tab="karma_usuarios"
genero="o"
entity="Usuario"
defaultFLD="nombre"
id="id_usuario"

[nombre]
type="SAFETEXT"
alias="Nombre"
req="1"

[apellidos]
type="SAFETEXT"
alias="Apellidos"

[login]
type="SAFETEXT"
alias="Usuario"
unique="1"
req="1"

[email]
type="EMAIL"
alias="Email"

[fecha_nacimiento]
type="DATE"
date="date"
;dateformat = "mdy"
;dateformat = "ymd"
dateformat = "dmy"
alias="Fecha de Nacimiento"

[pass]
type="PWD"
alias="Password"
req="1"
clone="1"
cloneInfo="please reenter password"

[fecha_insert]
type="GHOST"
sql = "fecha_insert";
alias = "Fecha de creación"