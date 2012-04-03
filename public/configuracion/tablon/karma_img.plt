[::main]
tit="Bildumako Irudiak"
tab="karma_img"
entity="Irudia"
id="idImg"
defaultFLD="nombre_img"
genero="a"


[idCategoria]
type="ENUMBD"
alias="Kategoria"
tab="karma_img_categorias"
id="idCategoria"
fld="nombre"

[titulo]
type="SAFETEXT"
alias="Titulua"

[img_binario]
type="IMG"
alias="Irudia"
name_fld="nombre_img"
req=1


[img_binario::size_img]
alias="Neurria"
type="IMG_SIZE"

[img_binario::nombre_img]
alias="Irudiaren Izena"
type="IMG_NAME"