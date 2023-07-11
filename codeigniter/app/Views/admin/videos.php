<div class="container">
    <?= session()->getFlashdata('error') ?>
    <form id="addVideoForm" class="row align-items-center justify-content-center my-3">
        <?= csrf_field() ?>
        <div class="col-12 col-md-6 text-center mb-3">
            <div class="form-floating">
                <input type="text" name="link" id="link"
                       class="form-control" placeholder="link">
                <label for="link" class="form-label">Paste YouTube link here</label>
            </div>
        </div>
        <div class="col-12 col-md-6 text-center mb-3">
            <div class="form-floating">
                <select class="form-select" id="floatingSelect">
                    <option value="0" selected>Not Scheduled</option>
                    <?php foreach ($schedules as $schedule): ?>
                        <option value="<?= $schedule['id'] ?>">
                            <?= htmlspecialchars($schedule['category']) ?> (<?= $schedule['startTime'] ?> - <?= $schedule['endTime'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="floatingSelect">Schedule</label>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3 text-center mb-3 mb-lg-0">
            <div class="form-floating">
                <input type="text" name="start_time" id="start_time"
                       class="form-control" placeholder="start_time"
                       data-bs-toggle="tooltip" data-bs-placement="top" title="H:M:S, M:S, or S"
                >
                <label for="start_time" class="form-label">Start Time (Optional)</label>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3 text-center mb-3 mb-lg-0">
            <div class="form-floating">
                <input type="text" name="end_time" id="end_time"
                       class="form-control" placeholder="end_time"
                       data-bs-toggle="tooltip" data-bs-placement="top" title="H:M:S, M:S, or S"
                >
                <label for="end_time" class="form-label">End Time (Optional)</label>
            </div>
        </div>
        <div class="col-4 col-lg-2 text-center">
            <button id="addVideo" type="submit" data-submitToken="0" class="btn btn-success">Add Video</button>
        </div>
    </form>
    <div class="row align-items-center justify-content-center">
        <div class="col-12 text-center">
            <div id="msg" class="text-danger text-center"></div>
        </div>
    </div>
    <div class="row align-items-center justify-content-center my-3">
        <div class="col-8 text-center">
            <form class="form-floating" autocomplete="off">
                <input type="text" name="searchVideos" id="searchVideos"
                       class="form-control" placeholder="searchVideos" value="<?= htmlspecialchars($search) ?>"
                >
                <label for="searchVideos" class="form-label">Search Videos</label>
            </form>
        </div>
        <div class="col-4 text-center">
            <button id="searchBtn" class="btn btn-success">Search</button>
        </div>
    </div>
    <?php
    $bg = 'bg-black';
    foreach ($videos as $video): ?>
        <form id="videoForm_<?= $video['id'] ?>" class="row align-items-center justify-content-center py-3 <?= $bg ?>">
            <?= csrf_field() ?>
            <div class="col-6 col-sm-3 col-md-2 text-center">
                <button type="button" class="delete btn btn-danger" data-id="<?= $video['id'] ?>">Delete</button>
                <div class="deleteErrorMsg text-danger" id="deleteErrorMsg_<?= $video['id'] ?>"></div>
            </div>
            <div class="col-6 col-sm-5 col-md-2">
                <a href="https://www.youtube.com/watch?v=<?= $video['video_id'] ?>">
                    <img width="100%" height="auto" src="<?= $video['thumbnail'] ?>">
                </a>
            </div>
            <div class="col-12 col-md-3 text-center">
                <a class="text-light" href="https://www.youtube.com/watch?v=<?= $video['video_id'] ?>">
                    <?= $video['title'] ?>
                </a>
            </div>
            <div class="col-12 col-md-4 text-center mt-3 mt-md-0">
                <div class="form-floating">
                    <select class="form-select scheduleSelect" id="floatingSelect_<?= $video['id'] ?>"
                            data-id="<?= $video['id'] ?>">
                        <option value="0" <?php if ($video['schedule_id'] === 0) echo 'selected'; ?>>Not Scheduled</option>
                        <?php foreach ($schedules as $schedule): ?>
                            <option value="<?= $schedule['id'] ?>" <?php if ($video['schedule_id'] === $schedule['id']) echo 'selected'; ?>>
                                <?= htmlspecialchars($schedule['category']) ?> (<?= $schedule['startTime'] ?>
                                - <?= $schedule['endTime'] ?> )
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="floatingSelect_<?= $video['id'] ?>">Schedule</label>
                </div>
                <div class="scheduleMsg text-success" id="scheduleMsg_<?= $video['id'] ?>"></div>
                <div class="scheduleErrorMsg text-danger" id="scheduleErrorMsg_<?= $video['id'] ?>"></div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 text-center mt-3">
                <div class="form-floating">
                    <input type="text" name="start_time_<?= $video['id'] ?>" id="start_time_<?= $video['id'] ?>"
                           value="<?= $video['start_time'] ?>"
                           class="form-control" placeholder="start_time"
                           data-bs-toggle="tooltip" data-bs-placement="top" title="H:M:S, M:S, or S"
                    >
                    <label for="start_time_<?= $video['id'] ?>" class="form-label">Start Time (Optional)</label>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 text-center mt-3">
                <div class="form-floating">
                    <input type="text" name="end_time_<?= $video['id'] ?>" id="end_time_<?= $video['id'] ?>"
                           value="<?= $video['end_time'] ?>"
                           class="form-control" placeholder="end_time"
                           data-bs-toggle="tooltip" data-bs-placement="top" title="H:M:S, M:S, or S"
                    >
                    <label for="end_time_<?= $video['id'] ?>" class="form-label">End Time (Optional)</label>
                </div>
            </div>
            <div class="col-4 col-lg-2 text-center mt-3">
                <button data-id="<?= $video['id'] ?>" id="saveTimes_<?= $video['id'] ?>" class="saveTimes btn btn-success">Save Times</button>
                <div class="saveTimesMsg text-success" id="saveTimesMsg_<?= $video['id'] ?>"></div>
                <div class="saveTimesErrorMsg text-danger" id="saveTimesErrorMsg_<?= $video['id'] ?>"></div>
            </div>
        </form>
        <?php
        if ($bg === "bg-dark") $bg = "bg-black";
        elseif ($bg === "bg-black") $bg = "bg-dark";
    endforeach; ?>
</div>
