<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, Access-Control-Allow-Headers, Authorization, X-Requested-With');
include('./config/config.inc.php');


//get dfatabase connection information
$db = new Database();
$connect = $db->connection();
//get api information
$dbop = new amharic_news_api($connect);
$get_news = $dbop->read_news();

//decleare news handlers
$news = array();
$news['data'] = array();

//check if news found in database
if ($get_news->rowCount() > 0) {
    while ($row = $get_news->fetch(PDO::FETCH_ASSOC)) {
        //extract news from database
        extract($row);

        //get news from database in array format
        $news_item = array(
            "id" => $id,
            "url" => $default_url,
            "news_title" => $news_title,
            "news_url" => $news_title_href,
            "news_image" => $news_title_image,
            "logo" => $news_posted_logo,
            "date" => $news_posted_date,
        );

        //push array data to the decleared news handler
        array_push($news['data'], $news_item);
    }
    echo json_encode($news['data']);
} else {
    echo json_encode(["msg" => "There is no news", "status" => false]);

}