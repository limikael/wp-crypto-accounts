<?php

$postdata = file_get_contents("php://input");
file_put_contents(__DIR__."/tests/log/post.".microtime(TRUE).".html", $postdata);
