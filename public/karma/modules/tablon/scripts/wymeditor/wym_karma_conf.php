<?php
$conf = array(
    "wymeditor" => array(
        "stylesheet"=>'estilo.css',
        "lang"=>'es',
        "toolsItems" => array(
            'bold' => array(
                'name'=>'Bold',
                'title'=>'Strong',
                'css'=>'wym_tools_strong'
            ),
            array(
                'name'=> 'Italic',
                'title'=> 'Emphasis',
                'css'=> 'wym_tools_emphasis'
            ),
            array(
                'name'=> 'Superscript',
                'title'=> 'Superscript',
                'css'=> 'wym_tools_superscript'
            ),
            array(
                'name'=> 'Subscript',
                'title'=> 'Subscript',
                'css'=> 'wym_tools_subscript'
            ),
            array(
                'name'=> 'InsertOrderedList',
                'title'=> 'Ordered_List',
                'css'=> 'wym_tools_ordered_list'
            ),
            array(
                'name'=> 'InsertUnorderedList',
                'title'=> 'Unordered_List',
                'css'=> 'wym_tools_unordered_list'
            ),
            array(
                'name'=> 'Indent',
                'title'=> 'Indent',
                'css'=> 'wym_tools_indent'
            ),
            array(
                'name'=> 'Outdent',
                'title'=> 'Outdent',
                'css'=> 'wym_tools_outdent'
            ),
            array(
                'name'=> 'Undo',
                'title'=> 'Undo',
                'css'=> 'wym_tools_undo'
            ),
            array(
                'name'=> 'Redo',
                'title'=> 'Redo',
                'css'=> 'wym_tools_redo'
            ),
            array(
                'name'=> 'CreateLink',
                'title'=> 'Link',
                'css'=> 'wym_tools_link'
            ),
            array(
                'name'=> 'Unlink',
                'title'=> 'Unlink',
                'css'=> 'wym_tools_unlink'
            ),
            array(
                'name'=> 'InsertTable',
                'title'=> 'Table',
                'css'=> 'wym_tools_table'
            ),
            array(
                'name'=> 'Paste',
                'title'=> 'Paste_From_Word',
                'css'=> 'wym_tools_paste'
            ),
            array(
                'name'=> 'ToggleHtml',
                'title'=> 'HTML',
                'css'=> 'wym_tools_html'
            )
        ),
        "classesItems"=>array(
            array(
                "name"=>"date",
                'title'=>'P: Date',
                'expr'=>'p'
            ),
            array(
                'name'=> 'hidden-note',
                'title'=> 'P: Hidden note',
                'expr'=> 'p[@class!="important"]'
            ),
            array(
                'name'=> 'important',
                'title'=> 'P: Important',
                'expr'=> 'p[@class!="hidden-note"]'
            ),
            array(
                'name'=> 'border',
                'title'=> 'IMG: Border',
            ),
            array(
                'name'=> 'left',
                'title'=> 'IMG: Left',
            ),
            array(
                'name'=> 'right',
                'title'=> 'IMG: Right',
                'expr'=> 'img'
            ),
        ),
        "editorStyles"=>array(
            array(
                'name'=>'.left',
                'css'=>'float:left;'
            ),
            array(
                'name'=>'.right',
                'css'=>'float:right;'
            ),
            array(
                'name'=>'.border',
                'css'=>'border: 5px solid #ccc;'
            ),
        ),
    ),
    "karma_img"=>array(
         "inputs"=>array(
              'src'=>'URL',
              'alt'=>'Texto alternativo',
              'title'=>'Titulo',
         ),
         "classes"=>array(
              'left'=>'Alineado a la izquierda',
              'right'=>'Alineado a la derecha',
              'center'=>'Alineado al centro'
         )
    ),
    "karma_youtube"=>array(
    	'user'=>'cuatro',
		'conf'=>array(
			'start-index'=>'1',
    		'max-results'=>'4',
    		'orderby'=>'published'
    	)

	),
	"karma_flickr"=>array(
		    'user'=>'27659958%40N06',
		    'api_key'=>'ca1a4f751187e54eca0594ab93482738',
		    'conf'=>array(
		        'page'=>'1',
		        'per_page'=>'5'
		    )
	),
	"karma_slideshare"=>array(
	       'secret'=>'HnpEFZW7',
           'api'=>'jmdShXZr',
           'username'=>'irontec',
           'limit'=>'4'
	)
);

die(json_encode($conf));
