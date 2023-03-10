<?php
use Kirby\Cms\Html;

/** @var \Kirby\Cms\Block $block */
?>
<?php if ($video = Html::video($block->url())): ?>
<?php 
  
  $video = str_replace("src", "data-src", $video); 
  $video = str_replace("www.youtube.com", "www.youtube-nocookie.com", $video);
  $video = str_replace("fullscreen", "fullscreen; autoplay; picture-in-picture; xr-spatial-tracking; clipboard-write", $video);  
  $hoster = ""; 
  $video_thumb = "";
  $hd_thumb = "";
  $sd_thumb = "";
  
  if(str_contains($block->url(), "youtu")){
    $hoster = "YouTube";
    
    // youtu.be umwandeln:
    if(str_contains($block->url(), "youtu.be")){
      $id = substr($block->url(), strrpos($block->url(), '/') + 1);
      $url =  "https://www.youtube.com/watch?v=$id";
    } else {
      $url = $block->url();
    }
    $query = parse_url($url, PHP_URL_QUERY);
    parse_str($query, $params);
    $videoID = isset($params['v']) ? $params['v'] : null;
    $hd_thumb = "https://img.youtube.com/vi/".$videoID."/maxresdefault.jpg";
    $sd_thumb = "https://img.youtube.com/vi/".$videoID."/hqdefault.jpg";
  }
  
  if(str_contains($block->url(), "vimeo")){
    $hoster = "Vimeo";
    $url = $block->url();
    $regex = '/^https?:\/\/(www\.)?vimeo\.com\/([0-9]+).*$/';
    preg_match($regex, $url, $matches);
    $videoID = isset($matches[2]) ? $matches[2] : null;
    $endpoint = "https://vimeo.com/api/v2/video/$videoID.json";
    
    $target_filename = $videoID;    
    $current_dir = $page->contentFileDirectory();
    $file_exists = glob($current_dir."/".$target_filename.".*");
    if(empty($file_exists)){
      $json = file_get_contents($endpoint);
      $data = json_decode($json, true);
      $hd_thumb = isset($data[0]['thumbnail_large']) ? $data[0]['thumbnail_large'] : null;
    } else {
      $hd_thumb = $file_exists[0];
    }
  }
  
  $video_thumb = $hd_thumb;
  if($video_thumb == ""){
    $video_thumb = $sd_thumb;
  }
  
  if($video_thumb != ""){
    // save thumbnail file to current directory to serve it locally
    $ext = pathinfo($video_thumb, PATHINFO_EXTENSION);
    if(empty($ext)){
      $ext = "jpg";
    }
    $target_filename = $videoID.".".$ext;    
    $current_dir = $page->contentFileDirectory();
    if(!file_exists($current_dir."/".$target_filename)){
      $save_image = file_get_contents($video_thumb);
      file_put_contents($current_dir."/".$target_filename, $save_image);
    }
  }
  $final_thumb = $page->url()."/".$target_filename;
  
?>
<figure class="video-wrapper shadow bg-light" style="background: url('<?= $final_thumb ?>'); background-position: center center; background-size: cover;" id="video-<?php echo $block->id(); ?>">
  <div class="ratio ratio-<?= $block->ratio() ?>">
  <?= $video ?>
  <div class="video-overlay d-flex w-100 h-100 align-items-center justify-content-center">
    <div class="text-center">
      <button onclick="playVideo('video-<?php echo $block->id(); ?>');return false;" class="btn btn-primary mt-4 btn-lg video-play-button">
        <i class="fa-solid fa-circle-play"></i> <?= t("play-video") ?>
      </button>
      <div class="item-desc mt-3"><i class="fa-brands fa-<?= strtolower($hoster); ?>"></i> <?= $hoster ?></div>
    </div>
  </div>
  </div>
  <?php if ($block->caption()->isNotEmpty()): ?>
  <figcaption><?= $block->caption() ?></figcaption>
  <?php endif ?>
</figure>
<?php endif ?>

