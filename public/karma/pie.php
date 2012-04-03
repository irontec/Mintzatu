    <div id="pieDeKarma">
            Irontec &copy; <?php echo $kMenu->getYear()?> -- karma v<?php
                $v = $kMenu->getVersion();
                if (time() > 1244111604 && $v=="1.0") {
                    echo "2";
                } else echo $v;
            ?>
            <em><?php
            $timeEnd = microtime(true);
            $time = $timeEnd - $timeStart;
            echo $l->lvar('exectime', sprintf("%.4f", $time));
            echo ' | '.$l->lvar('equ', ((isset(con::$contadorQueries))? con::$contadorQueries:0));

            ?></em>
            <br/><span id="s"><img src="./icons/karma2.0.logo.png" alt="Karma 2.0" /></span>
    </div>
</div>
<div id="errorLog"><?php $oError->draw()?></div>
</div>
<script type="text/javascript">
if(typeof CFInstall != 'undefined'){
    CFInstall.check({url:'http://www.google.com/chromeframe'});
}
</script>
</body>
</html>

