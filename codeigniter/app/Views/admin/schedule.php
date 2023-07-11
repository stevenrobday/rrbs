<div class="container">
    <?= session()->getFlashdata('error') ?>
    <form class="row align-items-center justify-content-center py-3 border-bottom border-light">
        <div id="timezoneForm" class="col-12 col-md-4 col-lg-3 text-center">
            <?= csrf_field() ?>
            <div class="form-floating">
                <select class="form-select" id="timezoneSelect">
                    <?php foreach ($timezones as $timezone): ?>
                        <option value="<?= $timezone['id'] ?>" <?php if ($timezone['id'] === $timezoneId) echo 'selected'; ?>>
                            <?= $timezone['label'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="timezoneSelect">Select Timezone</label>
            </div>
        </div>
        <div id="timezoneMsg" class="col-12 text-center text-success"></div>
        <div id="timezoneErrorMsg" class="col-12 text-center text-danger"></div>
    </form>
    <form id="addScheduleForm" class="row align-items-center justify-content-center my-3">
        <?= csrf_field() ?>
        <div class="col-12 col-lg-3 mb-3 mb-lg-0">
            <div class="form-floating">
                <input type="text" id="category"
                       class="form-control" placeholder="category">
                <label for="category" class="form-label">Category</label>
            </div>
        </div>
        <div class="col-4 col-md-2 text-center mb-3 mb-lg-0">
            <div class="form-floating">
                <select class="form-select" id="startTime">
                    <option value="null" selected></option>
                    <option value="0">12 AM</option>
                    <option value="1">1 AM</option>
                    <option value="2">2 AM</option>
                    <option value="3">3 AM</option>
                    <option value="4">4 AM</option>
                    <option value="5">5 AM</option>
                    <option value="6">6 AM</option>
                    <option value="7">7 AM</option>
                    <option value="8">8 AM</option>
                    <option value="9">9 AM</option>
                    <option value="10">10 AM</option>
                    <option value="11">11 AM</option>
                    <option value="12">12 PM</option>
                    <option value="13">1 PM</option>
                    <option value="14">2 PM</option>
                    <option value="15">3 PM</option>
                    <option value="16">4 PM</option>
                    <option value="17">5 PM</option>
                    <option value="18">6 PM</option>
                    <option value="19">7 PM</option>
                    <option value="20">8 PM</option>
                    <option value="21">9 PM</option>
                    <option value="22">10 PM</option>
                    <option value="23">11 PM</option>
                </select>
                <label for="startTime">Start time</label>
            </div>
        </div>
        <div class="col-4 col-md-2 text-center mb-3 mb-lg-0">
            <div class="form-floating">
                <select class="form-select" id="endTime">
                    <option value="null" selected></option>
                    <option value="0">12 AM</option>
                    <option value="1">1 AM</option>
                    <option value="2">2 AM</option>
                    <option value="3">3 AM</option>
                    <option value="4">4 AM</option>
                    <option value="5">5 AM</option>
                    <option value="6">6 AM</option>
                    <option value="7">7 AM</option>
                    <option value="8">8 AM</option>
                    <option value="9">9 AM</option>
                    <option value="10">10 AM</option>
                    <option value="11">11 AM</option>
                    <option value="12">12 PM</option>
                    <option value="13">1 PM</option>
                    <option value="14">2 PM</option>
                    <option value="15">3 PM</option>
                    <option value="16">4 PM</option>
                    <option value="17">5 PM</option>
                    <option value="18">6 PM</option>
                    <option value="19">7 PM</option>
                    <option value="20">8 PM</option>
                    <option value="21">9 PM</option>
                    <option value="22">10 PM</option>
                    <option value="23">11 PM</option>
                </select>
                <label for="endTime">End time</label>
            </div>
        </div>
        <div class="col-4 col-lg-2 text-center mb-4">
            <div class="text-light">Scheduled</div>
            <input id="status" type="checkbox" checked data-toggle="toggle" data-onstyle="success"
                   data-offstyle="danger">
        </div>
        <div class="col-6 col-md-3 col-lg-2 text-center mb-4">
            <div class="text-light">Regular Rotation</div>
            <input id="regularRotation" type="checkbox" checked data-toggle="toggle" data-onstyle="success"
                   data-offstyle="danger">
        </div>
        <div class="col-3 col-lg-1 text-center">
            <button id="addSchedule" type="submit" class="btn btn-success">Add</button>
        </div>
    </form>
    <div class="row align-items-center justify-content-center">
        <div class="col-12">
            <div id="msg" class="text-danger text-center"></div>
        </div>
    </div>
    <?php
    $bg = 'bg-black';
    foreach ($schedules as $schedule): 
        $id = $schedule['id'];
        $startTime = intval($schedule['start_time']);
        $endTime = intval($schedule['end_time']);
        ?>
        <form id="editScheduleForm_<?= $id ?>" class="row align-items-center justify-content-center py-3  <?= $bg ?>">
            <?= csrf_field() ?>
            <div class="col-2 col-lg-1 text-center mb-3 mb-lg-0">
                <button type="button" class="deleteSchedule btn btn-danger" data-id="<?= $id ?>">Delete</button>
                <div class="deleteErrorMsg text-danger" id="deleteErrorMsg_<?= $id ?>"></div>
            </div>
            <div class="col-12 col-md-4 col-lg-3 mb-3 mb-lg-0">
                <div class="form-floating">
                    <input type="text" id="category_<?= $id ?>"
                           value="<?= htmlspecialchars($schedule['category']) ?>"
                           class="form-control" placeholder="category">
                    <label for="category" class="form-label">Category</label>
                </div>
            </div>
            <div class="col-4 col-md-2 text-center mb-3 mb-lg-0">
                <div class="form-floating">
                    <select class="form-select" id="startTime_<?= $id ?>">
                        <option value="0" <?php if ($startTime === 0) echo 'selected'; ?>>12 AM</option>
                        <option value="1" <?php if ($startTime === 1) echo 'selected'; ?>>1 AM</option>
                        <option value="2" <?php if ($startTime === 2) echo 'selected'; ?>>2 AM</option>
                        <option value="3" <?php if ($startTime === 3) echo 'selected'; ?>>3 AM</option>
                        <option value="4" <?php if ($startTime === 4) echo 'selected'; ?>>4 AM</option>
                        <option value="5" <?php if ($startTime === 5) echo 'selected'; ?>>5 AM</option>
                        <option value="6" <?php if ($startTime === 6) echo 'selected'; ?>>6 AM</option>
                        <option value="7" <?php if ($startTime === 7) echo 'selected'; ?>>7 AM</option>
                        <option value="8" <?php if ($startTime === 8) echo 'selected'; ?>>8 AM</option>
                        <option value="9" <?php if ($startTime === 9) echo 'selected'; ?>>9 AM</option>
                        <option value="10" <?php if ($startTime === 10) echo 'selected'; ?>>10 AM</option>
                        <option value="11" <?php if ($startTime === 11) echo 'selected'; ?>>11 AM</option>
                        <option value="12" <?php if ($startTime === 12) echo 'selected'; ?>>12 PM</option>
                        <option value="13" <?php if ($startTime === 13) echo 'selected'; ?>>1 PM</option>
                        <option value="14" <?php if ($startTime === 14) echo 'selected'; ?>>2 PM</option>
                        <option value="15" <?php if ($startTime === 15) echo 'selected'; ?>>3 PM</option>
                        <option value="16" <?php if ($startTime === 16) echo 'selected'; ?>>4 PM</option>
                        <option value="17" <?php if ($startTime === 17) echo 'selected'; ?>>5 PM</option>
                        <option value="18" <?php if ($startTime === 18) echo 'selected'; ?>>6 PM</option>
                        <option value="19" <?php if ($startTime === 19) echo 'selected'; ?>>7 PM</option>
                        <option value="20" <?php if ($startTime === 20) echo 'selected'; ?>>8 PM</option>
                        <option value="21" <?php if ($startTime === 21) echo 'selected'; ?>>9 PM</option>
                        <option value="22" <?php if ($startTime === 22) echo 'selected'; ?>>10 PM</option>
                        <option value="23" <?php if ($startTime === 23) echo 'selected'; ?>>11 PM</option>
                    </select>
                    <label for="startTime_<?= $id ?>">Start time</label>
                </div>
            </div>
            <div class="col-4 col-md-2 text-center mb-3 mb-lg-0">
                <div class="form-floating">
                    <select class="form-select" id="endTime_<?= $id ?>">
                        <option value="0" <?php if ($endTime === 0) echo 'selected'; ?>>12 AM</option>
                        <option value="1" <?php if ($endTime === 1) echo 'selected'; ?>>1 AM</option>
                        <option value="2" <?php if ($endTime === 2) echo 'selected'; ?>>2 AM</option>
                        <option value="3" <?php if ($endTime === 3) echo 'selected'; ?>>3 AM</option>
                        <option value="4" <?php if ($endTime === 4) echo 'selected'; ?>>4 AM</option>
                        <option value="5" <?php if ($endTime === 5) echo 'selected'; ?>>5 AM</option>
                        <option value="6" <?php if ($endTime === 6) echo 'selected'; ?>>6 AM</option>
                        <option value="7" <?php if ($endTime === 7) echo 'selected'; ?>>7 AM</option>
                        <option value="8" <?php if ($endTime === 8) echo 'selected'; ?>>8 AM</option>
                        <option value="9" <?php if ($endTime === 9) echo 'selected'; ?>>9 AM</option>
                        <option value="10" <?php if ($endTime === 10) echo 'selected'; ?>>10 AM</option>
                        <option value="11" <?php if ($endTime === 11) echo 'selected'; ?>>11 AM</option>
                        <option value="12" <?php if ($endTime === 12) echo 'selected'; ?>>12 PM</option>
                        <option value="13" <?php if ($endTime === 13) echo 'selected'; ?>>1 PM</option>
                        <option value="14" <?php if ($endTime === 14) echo 'selected'; ?>>2 PM</option>
                        <option value="15" <?php if ($endTime === 15) echo 'selected'; ?>>3 PM</option>
                        <option value="16" <?php if ($endTime === 16) echo 'selected'; ?>>4 PM</option>
                        <option value="17" <?php if ($endTime === 17) echo 'selected'; ?>>5 PM</option>
                        <option value="18" <?php if ($endTime === 18) echo 'selected'; ?>>6 PM</option>
                        <option value="19" <?php if ($endTime === 19) echo 'selected'; ?>>7 PM</option>
                        <option value="20" <?php if ($endTime === 20) echo 'selected'; ?>>8 PM</option>
                        <option value="21" <?php if ($endTime === 21) echo 'selected'; ?>>9 PM</option>
                        <option value="22" <?php if ($endTime === 22) echo 'selected'; ?>>10 PM</option>
                        <option value="23" <?php if ($endTime === 23) echo 'selected'; ?>>11 PM</option>
                    </select>
                    <label for="endTime_<?= $id ?>">End time</label>
                </div>
            </div>
            <div class="col-3 col-lg-1 text-center mb-4">
                <div class="text-light">Scheduled</div>
                <input id="status_<?= $id ?>" type="checkbox" <?php if ($schedule['status']) echo 'checked'; ?> data-toggle="toggle" data-onstyle="success"
                       data-offstyle="danger">
            </div>
            <div class="col-3 col-lg-1 text-center">
                <button data-id="<?= $id ?>" class="editSchedule btn btn-success">Save</button>
            </div>
            <div class="col-6 col-md-3 col-lg-2 mb-4 text-center">
                <div class="text-light">Regular Rotation</div>
                <input class="regularRotation" data-id="<?= $id ?>" type="checkbox" <?php if ($schedule['regular_rotation']) echo 'checked'; ?> data-toggle="toggle" data-onstyle="success"
                       data-offstyle="danger">
                <div class="regularRotationMsg text-success" id="regularRotationMsg_<?= $id ?>"></div>
                <div class="regularRotationErrorMsg text-danger" id="regularRotationErrorMsg_<?= $id ?>"></div>
            </div>
            <div class="row align-items-center justify-content-center <?= $bg ?>">
                <div class="col-12">
                    <div id="msg_<?= $id ?>" class="scheduleSaveErrorMsg text-danger text-center"></div>
                </div>
            </div>
        </form>
        <?php
        if ($bg === "bg-dark") $bg = "bg-black";
        elseif ($bg === "bg-black") $bg = "bg-dark";
    endforeach; ?>
</div>
