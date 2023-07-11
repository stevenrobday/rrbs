<div class="container">
    <?php
    $bg = 'bg-black';
    foreach ($scores as $score): ?>
        <div class="row align-items-center justify-content-center py-3 <?= $bg ?>">
            <div class="col-12 col-md-4 text-center text-light">
                <?php if (strlen($score['img'])): ?>
                    <div>
                        <img alt="score_picture" class="scorePic" src="/assets/img/profile/<?= $score['img'] ?>">
                    </div>
                <?php else: ?>
                    <div>
                        <span class="fa-stack fa_xlg">
                            <i class="fa-regular fa-circle fa-stack-2x va-m"></i>
                            <i class="fa-solid fa-user fa-stack-1x va-m"></i>
                        </span>
                    </div>
                <?php endif; ?>
                <div>
                    <?= htmlspecialchars($score['username']) ?>
                </div>
            </div>
            <div class="col-12 col-md-4 text-center text-light va-m">
                <p class="fst-italic">
                    <?= htmlspecialchars($score['about']) ?>
                </p>
            </div>
            <div class="col-12 col-md-4 text-center text-light">
                <div>
                    <img alt="level" class="levelPic" src="/assets/img/level/level_<?= $score['level'] ?>.png">
                </div>
                <div>
                    <?= $score['count'] ?> approved videos
                </div>
            </div>
            <?php if ($score['count'] > 0): ?>
            <div class="col-12 col-md-8 text-center text-light mt-3">
                <div class="accordion" id="videoAccordion_<?= $score['id'] ?>">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="videos_<?= $score['id'] ?>">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_<?= $score['id'] ?>" aria-expanded="true" aria-controls="collapse_<?= $score['id'] ?>">
                                Approved Videos
                            </button>
                        </h2>
                        <div id="collapse_<?= $score['id'] ?>" class="accordion-collapse collapse" aria-labelledby="videos_<?= $score['id'] ?>" data-bs-parent="#videoAccordion_<?= $score['id'] ?>">
                            <div class="accordion-body">
                            <?php
                            $vbg = 'bg-black';
                            foreach ($score['videos'] as $video): ?>
                                <div class="row align-items-center justify-content-center py-3 <?= $vbg ?>">
                                    <div class="col-6 col-sm-4 col-md-2">
                                        <a href="https://www.youtube.com/watch?v=<?= $video['video_id'] ?>">
                                            <img width="100%" height="auto" src="<?= $video['thumbnail'] ?>">
                                        </a>
                                    </div>
                                    <div class="col-12 col-sm-8 col-md-10 text-center">
                                        <a class="text-light" href="https://www.youtube.com/watch?v=<?= $video['video_id'] ?>">
                                            <?= $video['title'] ?>
                                        </a>
                                    </div>
                                </div>
                            <?php
                                if ($vbg === "bg-dark") $vbg = "bg-black";
                                elseif ($vbg === "bg-black") $vbg = "bg-dark";
                            endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
        if ($bg === "bg-dark") $bg = "bg-black";
        elseif ($bg === "bg-black") $bg = "bg-dark";
    endforeach; ?>
</div>
