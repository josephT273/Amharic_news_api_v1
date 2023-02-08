<?php

class Database
{
    private $host = 'localhost';
    private $user = 'root';
    private $password = '';
    private $database = 'amharic_news_api_v1';
    // private $database = 'Dash@2965api';
    private $conn;
    public function connection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->database,
                    $this->user, $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'connection error: ' . $e->getMessage();
        }

        return $this->conn;
    }
}

class amharic_news_api
{
    private $pdo;
    function __construct($db)
    {
        $this->pdo = $db;
    }

    public function create_news($url, $news_title, $news_title_href, $news_title_image, $news_posted_logo, $news_posted_date)
    {
        // sql query that handle the insert function
        $sql = "INSERT INTO newsapi SET
                    default_url = ?,
                    news_title = ?,
                    news_title_href = ?,
                    news_title_image = ?,
                    news_posted_logo = ?,
                    news_posted_date = ?";
        // prepare the sql query
        $stmt = $this->pdo->prepare($sql);
        // binding parameters in array
        $data = array($url, $news_title, $news_title_href, $news_title_image, $news_posted_logo, $news_posted_date);
        //execute the query
        $stmt->execute($data);
        // return the result
        return $stmt;
    }
    public function read_news()
    {
        // sql query that handle the search function
        $sql = "SELECT * FROM newsapi ORDER BY news_posted_date DESC";
        // prepare the sql query
        $stmt = $this->pdo->prepare($sql);
        //execute the query
        $stmt->execute();
        // return the result
        return $stmt;
    }
    public function search_news($news_title)
    {
        // sql query that handle the search function
        $sql = "SELECT * FROM newsapi
                WHERE news_title=:news_title";
        // prepare the sql query
        $stmt = $this->pdo->prepare($sql);
        // bind the parameter
        $stmt->bindParam(':news_title', $news_title);
        //execute the query
        $stmt->execute();
        // return the result
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }

    }

}

class scrape_voa
{
    private $arr;
    private $href;
    private $title;
    private $img;
    const VOA = "https://amharic.voanews.com";

    function __construct()
    {
        $this->arr['data'] = array();
        $this->href = array();
        $this->title = array();
        $this->img = array();
    }

    public function get_url()
    {
        return self::VOA;
    }

    public function get_news_title_href()
    {
        $v = new scrapper(self::VOA);
        $search = '.col-xs-12 .row .col-xs-12 .media-block .img-wrap';
        $attr = 'href';
        $list = $v->find_tag($search, $attr);

        return $list;
    }

    public function get_news_title_image()
    {
        $v = new scrapper(self::VOA);
        $search = '.col-xs-12 .row .col-xs-12 .media-block .img-wrap .thumb .nojs-img img';
        // $attr = 'data-src';
        $attr = 'src';
        $list = $v->find_tag($search, $attr);

        return $list;
    }

    public function get_news_title()
    {
        $v = new scrapper(self::VOA);
        $search = '.col-xs-12 .row .col-xs-12 .media-block .img-wrap';
        $attr = 'title';
        $list = $v->find_tag($search, $attr);

        return $list;
    }

    public function get_news_posted_logo()
    {
        $v = new scrapper(self::VOA);
        // $search = '#page .hdr .hdr-nav-frag .container a img';
        $search = '.hdr-20 .hdr-20__inner .hdr-20__max .logo-print img';
        $attr = 'src';
        $list = $v->find_tag($search, $attr);

        return $list;
    }

    public function scrap_voa()
    {
        $this->href = $this->get_news_title_href();
        $this->img = $this->get_news_title_image();
        $this->title = $this->get_news_title();

        array_push($this->arr['data'], $this->href);
        array_push($this->arr['data'], $this->img);
        array_push($this->arr['data'], $this->title);

        return $this->arr;
    }

    function is_same_size_arr($list)
    {
        $size_data = sizeof($list);
        if ($size_data > 0) {
            $temp_size = sizeof($list[0]);
            for ($i = 1; $i < $size_data; $i++) {
                if (sizeof($list[$i]) != $temp_size) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}

class scrape_ebc
{
    private $arr;
    private $href;
    private $title;
    private $img;
    const EBC = "http://www.ebc.et/";

    function __construct()
    {
        $this->arr['data'] = array();
        $this->href = array();
        $this->title = array();
        $this->img = array();
    }

    public function get_url()
    {
        return self::EBC;
    }

    public function get_news_title_href()
    {
        $v = new scrapper(self::EBC);
        // $search = '.container .row .col-lg-4 .slider-right ul li .right-images a';
        $search = '.news-post li .row .col-lg-12 .item-post .row .col-lg-4 a';
        $attr = 'href';
        $list = $v->find_tag($search, $attr);
        // $list_even = array();
        // foreach ($list as $k => $v) {
        //     if ($k % 2 == 0) {
        //         $list_even[] = $v;
        //     }
        // }

        // return $list_even;
        return $list;
    }

    public function get_news_title_image()
    {
        $v = new scrapper(self::EBC);
        $search = '.all-news-area .container .row .col-lg-6 .news-post li .row .col-lg-12 .item-post .row .col-lg-4 a img';
        // $attr = 'data-src';
        $attr = 'src';
        $list = $v->find_tag($search, $attr);

        return $list;
    }

    public function get_news_title()
    {
        $v = new scrapper(self::EBC);
        $search = '.all-news-area .container .row .col-lg-6 .news-post li .row .col-lg-12 .item-post .row .col-lg-4 a';
        $attr = 'title';
        $list = $v->find_tag($search, $attr);

        return $list;
    }

    public function get_news_posted_logo()
    {
        $v = new scrapper(self::EBC);
        $search = '.container .row .col-lg-2 .logo-area a #header_imageLogo';
        $attr = 'src';
        $list = $v->find_tag($search, $attr);

        return $list;
    }

    public function scrap_ebc()
    {
        $this->href = $this->get_news_title_href();
        $this->img = $this->get_news_title_image();
        $this->title = $this->get_news_title();

        array_push($this->arr['data'], $this->href);
        array_push($this->arr['data'], $this->img);
        array_push($this->arr['data'], $this->title);

        return $this->arr;
    }

    function is_same_size_arr($list)
    {
        $size_data = sizeof($list);
        if ($size_data > 0) {
            $temp_size = sizeof($list[0]);
            for ($i = 1; $i < $size_data; $i++) {
                if (sizeof($list[$i]) != $temp_size) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}

class scrape_fanabc
{
    private $arr;
    private $href;
    private $title;
    private $img;
    const FANA = "https://www.fanabc.com/";

    function __construct()
    {
        $this->arr['data'] = array();
        $this->href = array();
        $this->title = array();
        $this->img = array();
    }

    public function get_url()
    {
        return self::FANA;
    }

    public function get_news_title_href()
    {
        $v = new scrapper(self::FANA);
        // $search = '.container .row .col-lg-4 .slider-right ul li .right-images a';
        $search = '.row-2 .listing .type-post .item-inner .featured a';
        $attr = 'href';
        $list = $v->find_tag($search, $attr);
        return $list;
    }

    public function get_news_title_image()
    {
        $list = '';

        return $list;
    }

    public function get_news_title()
    {
        $v = new scrapper(self::FANA);
        $search = '.row-2 .listing .type-post .item-inner .featured a';
        $attr = 'title';
        $list = $v->find_tag($search, $attr);

        return $list;
    }

    public function get_news_posted_logo()
    {
        $v = new scrapper(self::FANA);
        $search = '.row-height .logo-col .col-inside .site-branding .logo a img';
        $attr = 'src';
        $list = $v->find_tag($search, $attr);

        return $list;
    }

    public function scrap_fanabc()
    {
        $this->href = $this->get_news_title_href();
        $this->img = $this->get_news_title_image();
        $this->title = $this->get_news_title();

        array_push($this->arr['data'], $this->href);
        // array_push($this->arr['data'], $this->img);
        array_push($this->arr['data'], $this->title);

        return $this->arr;
    }

    function is_same_size_arr($list)
    {
        $size_data = sizeof($list);
        if ($size_data > 0) {
            $temp_size = sizeof($list[0]);
            for ($i = 1; $i < $size_data; $i++) {
                if (sizeof($list[$i]) != $temp_size) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}

function run_scrap_voa()
{
    $s = new scrape_voa();
    $list = $s->scrap_voa();
    $url = $s->get_url();
    print_r($list);
    $database = new Database();
    $db = $database->connection();
    $dbop = new amharic_news_api($db);

    if ($s->is_same_size_arr($list['data'])) {
        $size = sizeof($list['data']);
        $el_size = sizeof($list['data'][0]);
        $date = new DateTime();

        for ($i = 0; $i < $el_size; $i++) {
            $href = $list['data'][0][$i];
            $image = $s->get_url() . $list['data'][1][$i];
            $title = $list['data'][2][$i];
            $today = $date->format('Y-m-d h:i:s');
            $logolink = $s->get_url() . $s->get_news_posted_logo()[0];

            // print($url . $title . $href . $image . $logolink . $today . "<br>");
            if (!$dbop->search_news($title)) {
                $dbop->create_news($url, $title, $href, $image, $logolink, $today);
            }
        }
    }
}

function run_scrap_ebc()
{
    $s = new scrape_ebc();
    $list = $s->scrap_ebc();
    $url = $s->get_url();
    print_r($list);
    $database = new Database();
    $db = $database->connection();
    $dbop = new amharic_news_api($db);

    if ($s->is_same_size_arr($list['data'])) {
        $size = sizeof($list['data']);
        $el_size = sizeof($list['data'][0]);
        $date = new DateTime();

        for ($i = 0; $i < $el_size; $i++) {
            $href = $list['data'][0][$i];
            $image = $s->get_url() . $list['data'][1][$i];
            $title = $list['data'][2][$i];
            $today = $date->format('Y-m-d h:i:s');
            $logolink = $s->get_url() . $s->get_news_posted_logo()[0];

            // print($url . $title . $href . $image . $logolink . $today . "<br>");
            if (!$dbop->search_news($title)) {
                $dbop->create_news($url, $title, $href, $image, $logolink, $today);
            }
        }
    }
}
function run_scrap_fanabc()
{
    $s = new scrape_fanabc();
    $list = $s->scrap_fanabc();
    $url = $s->get_url();
    print_r($list);
    $database = new Database();
    $db = $database->connection();
    $dbop = new amharic_news_api($db);

    if ($s->is_same_size_arr($list['data'])) {
        $size = sizeof($list['data']);
        $el_size = sizeof($list['data'][0]);
        $date = new DateTime();

        for ($i = 0; $i < $el_size; $i++) {
            $href = $list['data'][0][$i];
            $image = $s->get_news_posted_logo()[0];
            $title = $list['data'][1][$i];
            $today = $date->format('Y-m-d h:i:s');
            $logolink = $s->get_news_posted_logo()[0];

            // print($url . $title . $href . $image . $logolink . $today . "<br>");
            if (!$dbop->search_news($title)) {
                $dbop->create_news($url, $title, $href, $image, $logolink, $today);
            }
        }
    }
}