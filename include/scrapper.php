<?php
// include the simple_html_dom
include('simple_html_dom.php');

// scrape the HTML document
class scrapper
{
    private $url;

    function __construct($url)
    {
        $this->url = $url;
    }

    public function get_url()
    {
        return $this->url;
    }

    public function get_plain_text_content()
    {
        // header("Content-Type:text/plain");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $html = curl_exec($ch);
        curl_close($ch);

        return $html;
    }

    public function find_tag($search_for, $ret_attr)
    {

        $content = $this->get_plain_text_content();
        $str_content = str_get_html($content);
        $tag_array = array();

        foreach ($str_content->find($search_for) as $result) {
            $tag_array[] = $result->$ret_attr;
        }

        return $tag_array;
    }

}


?>