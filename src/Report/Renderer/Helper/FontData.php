<?php

namespace Report\Renderer\Helper;


use FontLib\Exception\FontNotFoundException;
use FontLib\Font;

class FontData
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var int[]
     */
    public $charMap;
    /**
     * @var int[]
     */
    public $widths;
    /**
     * @var array
     */
    public $kernTree;
    /**
     * @var int
     */
    public $unitsPerEm;
    /**
     * @var FontData[]
     */
    protected static $fonts = [];
    /**
     * @var string
     */
    protected static $fontCacheDir = __DIR__ . '/cache/font/';

    /**
     * @param array $an_array
     * @return FontData
     */
    public static function __set_state($an_array)
    {
        $o = new FontData();
        $o->name = $an_array['name'];
        $o->charMap = $an_array['charMap'];
        $o->widths = $an_array['widths'];
        $o->kernTree = $an_array['kernTree'];
        $o->unitsPerEm = $an_array['unitsPerEm'];
        return $o;
    }

    /**
     * @param null|string $name
     * @return bool
     */
    public function save($name = null)
    {
        $h = fopen(self::$fontCacheDir . ($name ?: $this->name) . '.php', 'w+');
        fwrite($h, "<?php\n");
        fwrite($h, '$fontData = ' . var_export($this, true) . ';');
        return fclose($h);
    }

    /**
     * @param string $fontName
     * @param string $file
     * @return FontData|null
     * @throws FontNotFoundException
     */
    public static function load($fontName, $file = null)
    {
        if (isset(self::$fonts[$fontName])) {
            return self::$fonts[$fontName];
        }
        $fileName = self::$fontCacheDir . $fontName . '.php';
        if (file_exists($fileName)) {
            /** @var FontData $fontData */
            /** @noinspection PhpIncludeInspection */
            require $fileName;
        } elseif ($file) {
            $fontData = self::loadFromFont($file);
            if ($fontData) {
                $fontData->save($fontName);
            } else {
                return null;
            }
        } else {
            throw new FontNotFoundException('cache/font/' . $fontName . '.php');
        }
        self::$fonts[$fontName] = $fontData;

        return $fontData;
    }

    /**
     * @param FontData $fontData
     */
    public static function add(FontData $fontData)
    {
        self::$fonts[$fontData->name] = $fontData;
    }

    /**
     * @return FontData[]
     */
    public static function getFonts()
    {
        return self::$fonts;
    }

    /**
     * @param $file
     * @return FontData|null
     * @throws FontNotFoundException
     */
    protected static function loadFromFont($file)
    {
        $font = Font::load($file);

        if ($font === null) {
            throw new FontNotFoundException($file);
        }

        $font->parse();
        $fontData = new FontData();
        $fontData->name = $font->getFontName();
        $fontData->charMap = $font->getUnicodeCharMap();
        $fontData->kernTree = $font->getData("kern", "subtable")["tree"];
        $fontData->unitsPerEm = $font->getData("head", "unitsPerEm");
        $fontData->widths = [];
        $hmtx = $font->getData("hmtx");
        foreach ($hmtx as $gId => $data) {
            $fontData->widths[$gId] = $data[0];
        }

        return $fontData;
    }


}
