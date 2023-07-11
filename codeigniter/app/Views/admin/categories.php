<div class="container">
    <div class="row align-items-center justify-content-center my-3">
        <div class="col-12 col-md-4 text-center mt-3 mt-md-0">
            <form class="form-floating" autocomplete="off">
                <select class="form-select" id="categorySelect">
                    <option value="0" <?php if ($scheduleId === 0) echo 'selected'; ?>>Not Scheduled</option>
                    <?php foreach ($schedules as $schedule): ?>
                        <option value="<?= $schedule['id'] ?>" <?php if ($scheduleId === $schedule['id']) echo 'selected'; ?>>
                            <?= htmlspecialchars($schedule['category']) ?> (<?= $schedule['startTime'] ?>
                            - <?= $schedule['endTime'] ?> )
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="categorySelect">Select Category</label>
            </form>
        </div>
    </div>
    <?php
    $bg = 'bg-black';
    foreach ($videos as $video): ?>
        <form id="categoryVideoForm_<?= $video['id'] ?>" class="row align-items-center justify-content-center py-3 <?= $bg ?>">
            <?= csrf_field() ?>
            <div class="col-3 col-md-2 text-center">
                <button type="button" class="categoryDelete btn btn-danger" data-id="<?= $video['id'] ?>">Delete</button>
                <div class="deleteErrorMsg text-danger" id="deleteErrorMsg_<?= $video['id'] ?>"></div>
            </div>
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
            <div class="col-12 col-md-4 text-center mt-3 mt-md-0">
                <div class="form-floating">
                    <select class="form-select categoryScheduleSelect" id="floatingSelect_<?= $video['id'] ?>"
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
                <div class="scheduleErrorMsg text-danger" id="scheduleErrorMsg_<?= $video['id'] ?>"></div>
            </div>
        </form>
        <?php
        if ($bg === "bg-dark") $bg = "bg-black";
        elseif ($bg === "bg-black") $bg = "bg-dark";
    endforeach; ?>
</div>
