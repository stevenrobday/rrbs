<div class="container">
    <div class="row align-items-center justify-content-center my-3">
        <div class="col-12 col-md-4 text-center mt-3 mt-md-0">
            <form id="selectedWrap" class="form-floating" autocomplete="off">
                <select class="form-select" id="statusSelect">
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= $status['id'] ?>" <?php if ($statusId === (int) $status['id']) echo 'selected'; ?>>
                            <?= $status['status'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="statusSelect">Select Status</label>
            </form>
        </div>
    </div>
    <?php
    $bg = 'bg-black';
    foreach ($videos as $video): ?>
    <?php if ($statusId === 1): ?>
        <form id="suggestedForm_<?= $video['id'] ?>" class="row align-items-center justify-content-center py-3 <?= $bg ?>">
            <?= csrf_field() ?>
    <?php else: ?>
        <div class="row align-items-center justify-content-center py-3 <?= $bg ?>">
    <?php endif; ?>
            <div class="col-5 col-md-2">
                <a href="https://www.youtube.com/watch?v=<?= $video['video_id'] ?>">
                    <img width="100%" height="auto" src="<?= $video['thumbnail'] ?>">
                </a>
            </div>
            <div class="col-4 col-lg-3 text-center">
                <a class="text-light" href="https://www.youtube.com/watch?v=<?= $video['video_id'] ?>">
                    <?= $video['title'] ?>
                </a>
            </div>
            <div class="col-3 col-md-2 col-lg-1 text-center text-light">
                <div>Status:</div>
                <div><?= $video['status'] ?></div>
            </div>
            <div class="col-4 col-md-3 col-lg-2 text-center text-light mt-3 mt-md-0">
                <div>Submitted By:</div>
                <div><?= htmlspecialchars($video['submitted_by_username']) ?></div>
            </div>
            <?php if ($statusId !== 1): ?>
                <div class="col-4 col-md-3 text-center text-light mt-3 mt-lg-0">
                    <?php if ($statusId === 2): ?>
                        <div>Approved By:</div>
                    <?php elseif ($statusId === 3): ?>
                        <div>Denied By:</div>
                    <?php endif; ?>
                    <div><?= htmlspecialchars($video['approved_by_username']) ?></div>
                </div>
            <?php endif; ?>
            <?php if ($statusId === 1): ?>
            <div class="col-12 col-md-4 text-center mt-3 mt-md-0">
                <div class="form-floating">
                    <select class="form-select" id="suggestedSchedule_<?= $video['id'] ?>">
                        <option value="0" <?php if ($video['schedule_id'] === 0) echo 'selected'; ?>>Not Scheduled</option>
                        <?php foreach ($schedules as $schedule): ?>
                            <option value="<?= $schedule['id'] ?>" <?php if ($video['schedule_id'] === $schedule['id']) echo 'selected'; ?>>
                                <?= htmlspecialchars($schedule['category']) ?> (<?= $schedule['startTime'] ?>
                                - <?= $schedule['endTime'] ?> )
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="suggestedSchedule_<?= $video['id'] ?>">Schedule</label>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3 text-center mt-3">
                <div class="form-floating">
                    <input type="text" name="suggested_start_time_<?= $video['id'] ?>" id="suggested_start_time_<?= $video['id'] ?>"
                           value="<?= $video['start_time'] ?>"
                           class="form-control" placeholder="start_time"
                           data-bs-toggle="tooltip" data-bs-placement="top" title="H:M:S, M:S, or S"
                    >
                    <label for="suggested_start_time_<?= $video['id'] ?>" class="form-label">Start Time (Optional)</label>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3 text-center mt-3">
                <div class="form-floating">
                    <input type="text" name="suggested_end_time_<?= $video['id'] ?>" id="suggested_end_time_<?= $video['id'] ?>"
                           value="<?= $video['end_time'] ?>"
                           class="form-control" placeholder="end_time"
                           data-bs-toggle="tooltip" data-bs-placement="top" title="H:M:S, M:S, or S"
                    >
                    <label for="suggested_end_time_<?= $video['id'] ?>" class="form-label">End Time (Optional)</label>
                </div>
            </div>
            <?php elseif($video['schedule_id'] !== 0): ?>
                <?php foreach ($schedules as $schedule): ?>
                    <?php if ($video['schedule_id'] === $schedule['id']): ?>
                        <div class="col-12 col-md-5 col-lg-4 col-xl-3 text-light text-center mt-3">
                            <div>Schedule:</div>
                            <div><?= $schedule['category'] ?> (<?= $schedule['startTime'] ?>
                                - <?= $schedule['endTime'] ?> )</div>
                        </div>
                    <?php break; endif; ?>
                <?php endforeach; ?>
                <div class="col-4 col-md-2 text-center text-light mt-3">
                    <div>Start Time:</div>
                    <div><?= $video['start_time'] ?></div>
                </div>
                <div class="col-4 col-md-2 text-center text-light mt-3">
                    <div>End Time:</div>
                    <div><?= $video['end_time'] ?></div>
                </div>
            <?php endif; ?>
            <?php if ($statusId === 1): ?>
                <div class="col-12 text-center py-3">
                    <div class="form-floating">
                        <textarea class="form-control" placeholder="Leave a comment here" id="comments_<?= $video['id'] ?>"></textarea>
                        <label for="comments_<?= $video['id'] ?>">Comments</label>
                    </div>
                </div>
                <div class="col-3 col-md-2 text-center">
                    <button type="button" class="deny btn btn-danger" data-id="<?= $video['id'] ?>" data-status="denied" data-status_id="3"
                    >Deny</button>
                </div>
                <div class="col-3 col-md-2 text-center">
                    <button type="button" class="approve btn btn-success" data-id="<?= $video['id'] ?>" data-status="approved" data-status_id="2"
                    >Approve</button>
                </div>
                <div class="col-12">
                    <div id="suggestedErrorMsg_<?= $video['id'] ?>" class="text-danger text-center"></div>
                </div>
            <?php else: ?>
                <div class="col-12 text-center text-light py-3">
                    <div>Comments:</div>
                    <div><?= htmlspecialchars($video['comments']) ?></div>
                </div>
            <?php endif; ?>
            <?php if ($statusId === 1): ?>
            </form>
            <?php else: ?>
            </div>
            <?php endif;
        if ($bg === "bg-dark") $bg = "bg-black";
        elseif ($bg === "bg-black") $bg = "bg-dark";
    endforeach; ?>
</div>
