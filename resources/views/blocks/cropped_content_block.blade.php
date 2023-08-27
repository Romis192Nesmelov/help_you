<?=mb_substr(strip_tags($content),0,$length,'UTF-8').( mb_strlen(strip_tags($content), 'UTF-8') > $length ? '…' : ''); ?>
