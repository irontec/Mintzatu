<?php
define("CHK_PUBLIC",0);

/* Incluimos fichero autoload */
require_once '../../../../../../libs/autoload.php';

$get = $_GET;

if ( !isset($get['category']) && !isset($get['img']) && !isset($get['sizes']) ) {
    $categori = new con("SELECT * FROM karma_img_categorias");
    if ( $categori->getNumRows()>0 ) {
        while($cate = $categori->getResult()) {
            $cat['cat'][] = $cate;
        }
    }
    die(json_encode($cat));
} elseif ( isset($get['category']) && $get['category'] != '' && !isset($get['img']) && !isset($get['sizes']) ) {
    $catId = (int)$get['category'];
    if ( isset($get['pag']) ) {
        $from = ($get['pag'] * 10) - 10;
    } else {
        $from = 0;
    }
    $imagenes = new con("SELECT * FROM karma_img WHERE idCategoria ='".$catId."' ORDER BY idImg LIMIT ".$from.",10");
    if ( $imagenes->getNumRows()>0 ) {
        $count = new con("SELECT count(*) as count FROM karma_img WHERE idCategoria ='".$catId."'");
        $count = $count->getResult();
        $category = new con("SELECT nombre, url FROM karma_img_categorias WHERE idCategoria='".$catId."'");
        $category = $category->getResult();
        $img['cat'] = $category['nombre'];
        $img['cat_url'] = $category['url'];
        $img['count'] = $count['count'];
        $img['pages'] = ceil((int)$count['count'] / 10);
        (isset($get['pag'])) ? $img['pag'] = $get['pag'] : $img['pag'] = 1;
        while($image = $imagenes->getResult()) {
            $img['img'][] = $image;
        }
    }
    die(json_encode($img));
} elseif ( isset($get['sizes']) && $get['sizes'] == '' ) {
    $sql = new con("SELECT * FROM karma_img_size ORDER BY width ASC");
    if ( $sql->getNumRows()>0 ) {
        while($size = $sql->getResult()) {
            $sizes['sizes'][] = $size;
        }
        die(json_encode($sizes));
    }
}

