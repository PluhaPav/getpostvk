<?php
// ID нашего сообщества или страницы вконтакте
$wall_id="-{ID GROUP}";

// Удаляем минус у ID групп, что мы используем выше (понадобится для ссылки).
$group_id = preg_replace("/-/i", "", $wall_id);

// Количество записей, которое нам нужно получить.
$count = "50";

// Токен
$token = "{TOKEN}";

// Получаем информацию, подставив все данные выше.
$api = file_get_contents("https://api.vk.com/api.php?oauth=1&method=wall.get&owner_id={$wall_id}&count={$count}&v=5.58&access_token={$token}");

// Преобразуем JSON-строку в массив
$wall = json_decode($api);

// var_dump($wall);

// Получаем массив
$wall = $wall->response->items;
// Обрабатываем данные массива с помощью for и выводим нужные значения
header('Content-Type: text/xml;');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<rss version="2.0"
xmlns:content="http://purl.org/rss/1.0/modules/content/"
xmlns:wfw="http://wellformedweb.org/CommentAPI/"
xmlns:dc="http://purl.org/dc/elements/1.1/"
xmlns:atom="http://www.w3.org/2005/Atom"
xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
>';?>
<channel>
	<title><?php echo 'http://'.$_SERVER['HTTP_HOST'];?></title>
	<link><?php echo 'http://'.$_SERVER['HTTP_HOST'].'';?></link>
	<description></description>
	<lastBuildDate><?php echo date("D, j M Y G:i:s")." GMT";?></lastBuildDate>
	<language>RU</language>
	<?php
	$rssresults = '';
	for ($i = 5; $i < count($wall); $i++) {
		$rssimg ='';
		$text ='';
		$rssresults .= '<item>
		<title>'.explode('.',$wall[$i]->text)[0].'</title>
		<link>https://vk.com/wall-'.$group_id.'_'.$wall[$i]->id.'</link>
		<pubDate>'.date("Y-m-d H:i:s", $wall[$i]->date).'</pubDate>';
	    if(!empty($wall[$i]->attachments)){
    		foreach ($wall[$i]->attachments as $key => $val) {
    			foreach ($val as $k => $value) {
    				if($k == "photo"){
    					if(!empty($value->photo_1280)){
    						$rssresults .= '<enclosure url="'.$value->photo_1280.'" type="image/jpg"/>';
    						$rssimg .= '<img src="'.$value->photo_1280.'"/><br/>';
    					}elseif(!empty($value->photo_807)){
    						$rssresults .= '<enclosure url="'.$value->photo_807.'" type="image/jpg"/>';
    						$rssimg .= '<img src="'.$value->photo_807.'"/><br/>';
    					}
    					$text = $value->title;
    				}elseif($k == "video"){
    					$rssresults .= '<enclosure url="https://vk.com/video'.(string)$value->owner_id.'_'.(string)$value->id.'" type="video/x-ms-asf"/>';
    					
    					$text = $value->title;
    				}elseif($k == "link"){
    					if(!empty($value->photo->photo_1280)){
    						$rssresults .= '<enclosure url="'.$value->photo->photo_1280.'" type="image/jpg"/>';
    						$rssimg .= '<img src="'.$value->photo->photo_1280.'"/><br/>';
    					}elseif(!empty($value->photo->photo_807)){
    						$rssresults .= '<enclosure url="'.$value->photo->photo_807.'" type="image/jpg"/>';
    						$rssimg .= '<img src="'.$value->photo->photo_807.'"/><br/>';
    					}
    					$text = $value->title;
    				}elseif ($k == "doc"){
    					if(!empty($value->url)){
    						$rssresults .= '<enclosure url="'.htmlspecialchars($value->url).'" type="image/gif"/>';
    						$rssimg .= '<img src="'.htmlspecialchars($value->url).'"/><br/>';
    					}
    					$text = $value->title;
    				}
    			}
    		}
    	$rssresults .= 	'<dc:creator><![CDATA[vk.com]]></dc:creator>
    	<category><![CDATA[Ремонт]]></category>
    	<guid isPermaLink="false">'.$wall[$i]->id.'</guid>
    	<description><![CDATA['.$text.']]></description>
    	<content:encoded><![CDATA['.$wall[$i]->text.'<br/>'.$rssimg.']]></content:encoded>
    	</item>';
	    }else{
	        $rssresults .= 	'<dc:creator><![CDATA[vk.com]]></dc:creator>
        	<category><![CDATA[Ремонт]]></category>
        	<guid isPermaLink="false">'.$wall[$i]->id.'</guid>
        	<description><![CDATA['.$wall[$i]->text.']]></description>
        	<content:encoded><![CDATA['.$wall[$i]->text.'<br/>'.$rssimg.']]></content:encoded>
        	</item>';
	    }
	
}
echo $rssresults;
?>
</channel>
</rss>