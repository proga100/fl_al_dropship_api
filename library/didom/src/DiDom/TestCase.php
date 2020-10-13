<?php
require_once('Document.php');

$document = new Document('http://www.news.com/', true);

$posts = $document->find('.post');

foreach ($posts as $post) {
    echo $post->text(), "\n";
}
