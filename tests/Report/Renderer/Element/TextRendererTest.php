<?php namespace report\Renderer\Element;


use PHPUnit\Framework\TestCase;
use Report\Band\DataBand;
use Report\Element\TextBox;
use Report\Helper\FontStyle;
use Report\PageTemplate;
use Report\Renderer\Html\Element\TextBoxRenderer;

class TextRendererTest extends TestCase
{
    /**
     * @var TextBox
     */
    protected $text;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $style = new FontStyle([
            'font-size' => 5,
            'line-height' => 7
        ]);
        $page = new PageTemplate();
        $band = new DataBand($page);
        $this->text = new TextBox($band, 'text');
        $this->text
            ->setFontStyle($style)
            ->setPadding(1);
    }

    public function testRenderHtml()
    {
        $result = TextBoxRenderer::getRenderResult($this->text, 100, true);
        self::assertEquals('<div class="element text" style="left:0px;top:0px; text-align:left;color:#000000;font-family:Roboto;font-size:5px;line-height:7px;padding:1px 1px 1px 1px;word-break:break-word;overflow-wrap:anywhere;">text</div>', $result->content);
    }

    public function testGetStyle()
    {
        $style = TextBoxRenderer::getStyle($this->text);
        // class name contains spl_object_id, substr cut it
        $style = substr($style, strpos($style, '{'));
        self::assertEquals("{ left:0px;top:0px; text-align:left;color:#000000;font-family:Roboto;font-size:5px;line-height:7px;padding:1px 1px 1px 1px;word-break:break-word;overflow-wrap:anywhere; }\n", $style);
    }

}
