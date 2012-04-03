<?php
//http://www.splitbrain.org/_static/color/index.php?c1=%232af3e1&c2=%237b9327
//

/**
 * <code>
 * $color = new Color('#FFFFFF');
 * echo $color[0];
 * //expects:
 *
 * </code>
 *
 **/
class Color implements ArrayAccess, Iterator, Countable
{
    const MIN__colDiff = 500;
    const MIN__BRIDIFF = 125;
    const MIN__lumDiff = 5;
    const MIN__PYDIFF = 250;
    const MAX_LOOP = 10;
    const NUM_GRAYS = 8;
    const RED_CHANNEL = 0;
    const GREEN_CHANNEL = 1;
    const BLUE_CHANNEL = 2;
    const ALPHA_CHANNEL = 3;

    private $_channels;

    private function constructHex($color)
    {

        if ($color[0] == '#') {
            $color = substr($color, 1);
        }
        if (strlen($color) == 6) {
            list($r, $g, $b) = array(
                $color[0].$color[1],
                $color[2].$color[3],
                $color[4].$color[5]
            );
        } elseif (strlen($color) == 3) {
            list($r, $g, $b) = array(
                $color[0] . $color[0],
                $color[1] . $color[1],
                $color[2] . $color[2]
            );
        } else {
            return false;
        }

        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);

        return new Color($r, $g, $b);

    }

    private function constructRGB(array $_channels)
    {
        foreach ($_channels as $key=>$value) {
            $this->_channels[] = $value;
        }
    }

    public function __construct()
    {
        $args = func_get_args();
        $this->_channels = array();
        if (count($args) <= 1) {
            $this->constructHex($args[0]);
        } elseif (count($args) >= 3) {
            $this->constructRGB($args);
        } else {
            throw new Exception("Invalid number of arguments");
        }
    }
    public static function getGrayScale()
    {
        //return array('#181818', '#383838', '#505050', '#707070', '#909090', '#B0B0B0', '#D0D0D0', '#F0F0F0');
        return array('#000000', '#FFFFFF');
    }


    private static function colDiff(Color $color1, Color $color2)
    {
        return max($color1->getRed(), $color2->getRed()) - min($color1->getRed(), $color2->getRed())
            + max($color1->getGreen(), $color2->getGreen()) - min($color1->getGreen(), $color2->getGreen())
            + max($color1->getBlue(), $color2->getBlue()) - min($color1->getBlue(), $color2->getBlue());
    }

    public static function brightDiff(Color $color1, Color $color2)
    {
        $br1 = (299 * $color1->getRed() + 587 * $color1->getGreen() + 114 * $color1->getBlue()) / 1000;
        $br2 = (299 * $color2->getRed() + 587 * $color2->getGreen() + 114 * $color2->getBlue()) / 1000;

        return abs($br1-$br2);
    }

    public static function lumDiff(Color $color1, Color $color2)
    {
        $L1 = 0.2126 * pow($color1->getRed()/255, 2.2) +
            0.7152 * pow($color1->getGreen()/255, 2.2) +
            0.0722 * pow($color1->getBlue()/255, 2.2);

        $L2 = 0.2126 * pow($color2->getRed()/255, 2.2) +
            0.7152 * pow($color2->getGreen()/255, 2.2) +
            0.0722 * pow($color2->getBlue()/255, 2.2);

        if ($L1 > $L2) {
            return ($L1 + 0.05) / ($L2 + 0.05);
        } else {
            return ($L2 + 0.05) / ($L1 + 0.05);
        }
    }

    public static function pythDiff(Color $color1, Color $color2)
    {
        $RD = $color1->getRed() - $color2->getRed();
        $GD = $color1->getGreen() - $color2->getGreen();
        $BD = $color1->getBlue() - $color2->getBlue();

        return  sqrt( $RD * $RD + $GD * $GD + $BD * $BD );
    }

    public static function HEXtoRGB($color)
    {
        if ($color[0] == '#')
            $color = substr($color, 1);

        if (strlen($color) == 6)
            list($r, $g, $b) = array($color[0].$color[1],
                $color[2].$color[3],
                $color[4].$color[5]);
        elseif (strlen($color) == 3)
            list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
        else
            return false;

        $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

        return new Color($r, $g, $b);
    }

    public static function similarScore(Color $color1, Color $color2)
    {
        $difCol =  Color::colDiff($color1, $color2);
        $difBr = Color::brightdiff($color1, $color2);
        $difLum =  Color::lumDiff($color1, $color2);
        $difP = Color::pythDiff($color1, $color2);
        return abs($difCol - Color::MIN__colDiff) + abs($difBr - Color::MIN__BRIDIFF) + abs($difLum - Color::MIN__lumDiff) + abs($difP - Color::MIN__PYDIFF);
    }

    public static function isSimilarTo(Color $color1, Color $color2)
    {

        $difCol =  Color::colDiff($color1, $color2);
        $difBr = Color::brightdiff($color1, $color2);
        $difLum =  Color::lumDiff($color1, $color2);
        $difP = Color::pythDiff($color1, $color2);
        return ($difCol < Color::MIN__colDiff) && ($difBr < Color::MIN__BRIDIFF) && ($difLum < Color::MIN__lumDiff) && ($difP < Color::MIN__PYDIFF);
    }

    function RGBtoHEX(Color $color)
    {

        $r = $color[0];
        $g = $color[1];
        $b = $color[2];

        $r = intval($r);
        $g = intval($g);
        $b = intval($b);

        $r = dechex($r<0?0:($r>255?255:$r));
        $g = dechex($g<0?0:($g>255?255:$g));
        $b = dechex($b<0?0:($b>255?255:$b));

        $hexColor = (strlen($r) < 2?'0':'').$r;
        $hexColor .= (strlen($g) < 2?'0':'').$g;
        $hexColor .= (strlen($b) < 2?'0':'').$b;
        return '#'.$hexColor;
    }


    public  static function randomColor()
    {
        mt_srand((double)microtime()*1000000);
        $c = array();
        while (sizeof($c)<3) {
            $c[]= mt_rand(0, 255);
        }
        return new Color($c[0], $c[1], $c[2]);
    }


    public static function getGrayContrastColor($htmlColor)
    {
        return Color::getAcontrastColor($htmlColor, true);
    }

    public static function getAcontrastColor($htmlColor, $gray=true)
    {
        $color1 = Color::HEXtoRGB($htmlColor);

        $i = 0;
        $grays = Color::getGrayScale();
        if ($gray) {
            $randomColor = Color::HEXtoRGB($grays[$i]);
        } else {
            $randomColor = Color::randomColor();
        }

        $alike = Color::isSimilarTo($color1, $randomColor);
        $best = array();
        $best['score'] = Color::similarScore($color1, $randomColor);
        $best['color'] = $randomColor;
        $i++;
        while(/*$alike &&*/ $i < Color::MAX_LOOP && $i < count(Color::getGrayScale())) {

            if ($gray) {
                if ($i > Color::NUM_GRAYS) {
                    break;
                }
                $randomColor = Color::HEXtoRGB($grays[$i]);
            } else {
                $randomColor = Color::randomColor();
            }
            $alike = Color::isSimilarTo($color1, $randomColor);
            $score = Color::similarScore($color1, $randomColor);
            $alike = Color::isSimilarTo($color1, $randomColor);
            if ($best['score'] > $score) {
                $best['color'] = $randomColor;
            }
            $i++;
        }
        return Color::RGBtoHEX($best['color']);
    }

    public function getRed()
    {
        return $this->_channels[Color::RED_CHANNEL];
    }

    public function getGreen()
    {
        return  $this->_channels[Color::GREEN_CHANNEL];
    }
    public function getBlue()
    {
        return  $this->_channels[Color::BLUE_CHANNEL];
    }
    public function getAlpha()
    {
        return  $this->_channels[Color::ALPHA_CHANNEL];
    }





    public function offsetSet($offset,$value)
    {
        if ($value instanceof ColorModel) {
            if ($offset == "") {
                $this->_channels[] = $value;
            } else {
                $this->_channels[$offset] = $value;
            }
        } else {
            throw new Exception("Value have to be a instance of the Model ColorModel");
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->_channels[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->_channels[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->_channels[$offset]) ? $this->_channels[$offset] : null;
    }

    public function rewind()
    {
        reset($this->_channels);
    }

    public function current()
    {
        return current($this->_channels);
    }

    public function key()
    {
        return key($this->_channels);
    }

    public function next()
    {
        return next($this->_channels);
    }

    public function valid()
    {
        return $this->current() !== false;
    }

    public function getChannels()
    {
        return $this->_channels;
    }

    public function setChannels($channels)
    {
        $this->_channels = $channels;
    }

    public function count()
    {
        return count($this->_channels);
    }
}
