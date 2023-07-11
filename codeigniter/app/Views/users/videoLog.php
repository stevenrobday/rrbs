<div class="container">
    <?php
    $bg = 'bg-black';
    foreach ($videos as $video): ?>
        <div class="row align-items-center justify-content-center py-3 <?= $bg ?>">
            <div class="col-6 col-sm-4 col-md-2">
                <a href="https://www.youtube.com/watch?v=<?= $video['video_id'] ?>">
                    <img width="100%" height="auto" src="<?= $video['thumbnail'] ?>">
                </a>
            </div>
            <div class="col-12 col-sm-4 col-md-7 text-center">
                <a class="text-light" href="https://www.youtube.com/watch?v=<?= $video['video_id'] ?>">
                    <?= $video['title'] ?>
                </a>
            </div>
            <div class="col-12 col-sm-4 col-md-3 text-center text-light">
                <?= $video['datetime'] ?>
            </div>
        </div>
        <?php
        if ($bg === "bg-dark") $bg = "bg-black";
        elseif ($bg === "bg-black") $bg = "bg-dark";
    endforeach; ?>
</div>