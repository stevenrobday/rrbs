<div class="container">
    <form id="suggestVideoForm" class="row align-items-center justify-content-center my-3">
        <?= csrf_field() ?>
        <div class="col-12 col-lg-4 text-center mb-3 mb-lg-0">
            <div class="form-floating">
                <input type="text" name="suggestLink" id="suggestLink"
                       class="form-control" placeholder="link">
                <label for="suggestLink" class="form-label">Paste YouTube link here</label>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3 text-center mb-3 mb-md-0">
            <div class="form-floating">
                <input type="text" name="suggest_start_time" id="suggest_start_time"
                       class="form-control" placeholder="start_time"
                       data-bs-toggle="tooltip" data-bs-placement="top" title="H:M:S, M:S, or S"
                >
                <label for="suggest_start_time" class="form-label">Start Time (Optional)</label>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3 text-center mb-3 mb-md-0">
            <div class="form-floating">
                <input type="text" name="suggest_end_time" id="suggest_end_time"
                       class="form-control" placeholder="end_time"
                       data-bs-toggle="tooltip" data-bs-placement="top" title="H:M:S, M:S, or S"
                >
                <label for="suggest_end_time" class="form-label">End Time (Optional)</label>
            </div>
        </div>
        <div class="col-4 col-lg-2 text-center">
            <button id="addSuggestVideo" type="submit" data-submitToken="0" class="btn btn-success">Add Video</button>
        </div>
    </form>
    <div class="row align-items-center justify-content-center">
        <div class="col-12">
            <div id="suggestMsg" class="text-danger text-center"></div>
        </div>
    </div>
    <div class="row align-items-center justify-content-center my-3">
        <div class="col-7 text-center">
            <div class="form-floating">
                <input type="text" id="searchVideosUser"
                       class="form-control" placeholder="searchVideosUser"
                >
                <label for="searchVideosUser" class="form-label">Search Videos</label>
            </div>
        </div>
        <div class="col-3 text-center">
            <button id="searchBtnUser" class="btn btn-success">Search</button>
        </div>
        <div class="col-2 text-center">
            <button id="clearBtnUser" class="btn btn-danger">Clear</button>
        </div>
    </div>
    <div id="searchedVideosHeader" class="row text-light">
        <div class="col-8 text-center">
            Title
        </div>
        <div class="col-2 text-center">
            Start Time
        </div>
        <div class="col-2 text-center">
            End Time
        </div>
    </div>
    <div id="searchedVideosContainer" class="row mb-3">
    </div>
    <?php
    $bg = 'bg-black';
    foreach ($videos as $video): ?>
        <div class="row align-items-center justify-content-center py-3 <?= $bg ?>">
            <div class="col-5 col-md-2">
                <a href="https://www.youtube.com/watch?v=<?= $video['video_id'] ?>">
                    <img width="100%" height="auto" src="<?= $video['thumbnail'] ?>">
                </a>
            </div>
            <div class="col-4 col-md-3 text-center">
                <a class="text-light" href="https://www.youtube.com/watch?v=<?= $video['video_id'] ?>">
                    <?= $video['title'] ?>
                </a>
            </div>
            <div class="col-4 col-md-3 col-lg-2 text-center text-light mt-3 mt-md-0">
                <div>Start Time:</div>
                <div><?= $video['start_time'] ?></div>
            </div>
            <div class="col-4 col-md-3 col-lg-2 text-center text-light mt-3 mt-md-0">
                <div>End Time:</div>
                <div><?= $video['end_time'] ?></div>
            </div>
            <div class="col-3 text-center text-light mt-3 mt-lg-0">
                <div>Status:</div>
                <div><?= $video['status'] ?></div>
            </div>
            <?php if ($video['status'] !== 'Pending'): ?>
                <?php if ($video['status'] === 'Approved'): ?>
                    <div class="col-4 text-center text-light mt-3">
                        <div>Approved By:</div>
                        <div><?= htmlspecialchars($video['username']) ?></div>
                    </div>
                <?php elseif ($video['status'] === 'Denied'): ?>
                    <div class="col-4 text-center text-light mt-3">
                        <div>Denied By:</div>
                        <div><?= htmlspecialchars($video['username']) ?></div>
                    </div>
                <?php endif; ?>
                <div class="col-12 text-center text-light mt-3">
                    <div>Comments:</div>
                    <div><?= htmlspecialchars($video['comments']) ?></div>
                </div>
            <?php endif; ?>
        </div>
        <?php
        if ($bg === "bg-dark") $bg = "bg-black";
        elseif ($bg === "bg-black") $bg = "bg-dark";
    endforeach; ?>
</div>
