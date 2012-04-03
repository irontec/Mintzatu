#!/bin/bash
for i in $( ls $1)
do
	convert "$1$i" -resize 48x48 $i.png
done

rename 's/.svg//' * 
rename 's/gnome-//' *
rename 's/mime-//' * 
